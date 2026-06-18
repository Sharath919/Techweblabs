'use client'

import { useEffect, useState } from 'react'
import {
  ARTICLE_MACHINE_PROMPT_TABS,
  countTemplatesUsingDefault,
  estimatePromptTokens,
} from '@/config/articleMachinePrompts'
import {
  PUBLISHING_CRON_RUNS_PER_DAY,
  PUBLISHING_BATCH_SIZE,
  PUBLISHING_DAILY_TARGET,
} from '@/config/site'
import AdminPanel from '@/components/admin/AdminPanel'
import PublishingHealthPanel from '@/components/admin/PublishingHealthPanel'
import { createBrowserSupabase } from '@/lib/supabase'

type AiConfigRow = { key: string; value: unknown; updated_at: string | null }

async function getToken() {
  const supabase = createBrowserSupabase()
  const { data: { session } } = await supabase.auth.getSession()
  return session?.access_token || ''
}

export default function ArticleMachineSettingsPage() {
  const supabase = createBrowserSupabase()
  const [loading, setLoading] = useState(true)
  const [automationEnabled, setAutomationEnabled] = useState(true)
  const [lastRun, setLastRun] = useState<string | null>(null)
  const [processedToday, setProcessedToday] = useState(0)
  const [processedMonth, setProcessedMonth] = useState(0)
  const [savingToggle, setSavingToggle] = useState(false)

  const [activeTab, setActiveTab] = useState(ARTICLE_MACHINE_PROMPT_TABS[0].id)
  const [prompts, setPrompts] = useState<Record<string, string>>({})
  const [promptUpdatedAt, setPromptUpdatedAt] = useState<Record<string, string | null>>({})
  const [savingKey, setSavingKey] = useState<string | null>(null)
  const [testing, setTesting] = useState(false)
  const [testOutput, setTestOutput] = useState<string | null>(null)
  const [personaPrompt, setPersonaPrompt] = useState('')
  const [welcomeMessage, setWelcomeMessage] = useState('')
  const [message, setMessage] = useState('')

  useEffect(() => {
    async function load() {
      const promptKeys = ARTICLE_MACHINE_PROMPT_TABS.map((t) => t.configKey)
      const { data } = await supabase
        .from('ai_config')
        .select('key, value, updated_at')
        .in('key', [
          'automation_enabled',
          'last_cron_run',
          'persona_system_prompt',
          'persona_welcome_message',
          ...promptKeys,
        ])

      const loaded: Record<string, string> = {}
      const updated: Record<string, string | null> = {}
      for (const row of (data ?? []) as AiConfigRow[]) {
        const val = typeof row.value === 'string' ? row.value.replace(/^"|"$/g, '') : String(row.value ?? '')
        if (row.key === 'automation_enabled') setAutomationEnabled(val !== 'false')
        if (row.key === 'last_cron_run') setLastRun(val || null)
        if (row.key === 'persona_system_prompt') setPersonaPrompt(val)
        if (row.key === 'persona_welcome_message') setWelcomeMessage(val)
        if (promptKeys.includes(row.key)) {
          loaded[row.key] = val
          updated[row.key] = row.updated_at
        }
      }
      setPrompts(loaded)
      setPromptUpdatedAt(updated)

      const now = new Date()
      const startToday = new Date(now)
      startToday.setHours(0, 0, 0, 0)
      const startMonth = new Date(now.getFullYear(), now.getMonth(), 1)
      const [todayRes, monthRes] = await Promise.all([
        supabase
          .from('publishing_schedule')
          .select('id', { count: 'exact', head: true })
          .eq('status', 'done')
          .gte('updated_at', startToday.toISOString()),
        supabase
          .from('publishing_schedule')
          .select('id', { count: 'exact', head: true })
          .eq('status', 'done')
          .gte('updated_at', startMonth.toISOString()),
      ])
      setProcessedToday(todayRes.count ?? 0)
      setProcessedMonth(monthRes.count ?? 0)
      setLoading(false)
    }
    load()
  }, [supabase])

  const tab = ARTICLE_MACHINE_PROMPT_TABS.find((t) => t.id === activeTab) ?? ARTICLE_MACHINE_PROMPT_TABS[0]
  const activeText = prompts[tab.configKey] ?? ''
  const usingDefault = tab.templateType && !activeText.trim()

  function updatePrompt(value: string) {
    setPrompts((p) => ({ ...p, [tab.configKey]: value }))
  }

  async function savePrompt() {
    setSavingKey(tab.configKey)
    await supabase.from('ai_config').upsert({
      key: tab.configKey,
      value: JSON.stringify(activeText),
      updated_at: new Date().toISOString(),
    })
    setPromptUpdatedAt((p) => ({ ...p, [tab.configKey]: new Date().toISOString() }))
    setSavingKey(null)
    setMessage(`${tab.saveLabel} prompt saved`)
  }

  async function savePersona() {
    await supabase.from('ai_config').upsert([
      { key: 'persona_system_prompt', value: JSON.stringify(personaPrompt), updated_at: new Date().toISOString() },
      { key: 'persona_welcome_message', value: JSON.stringify(welcomeMessage), updated_at: new Date().toISOString() },
    ])
    setMessage('Persona settings saved')
  }

  async function toggleAutomation(next: boolean) {
    setSavingToggle(true)
    await supabase.from('ai_config').upsert({
      key: 'automation_enabled',
      value: next ? 'true' : 'false',
      updated_at: new Date().toISOString(),
    })
    setAutomationEnabled(next)
    setSavingToggle(false)
  }

  async function testPrompt() {
    setTesting(true)
    setTestOutput(null)
    try {
      const token = await getToken()
      const res = await fetch('/api/test-article-machine', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
        body: JSON.stringify({
          topic: 'Flutter vs React Native for startups in 2026',
          template_type: tab.testTemplateType,
        }),
      })
      const body = await res.json()
      if (!res.ok) throw new Error(body.error || 'Test failed')
      setTestOutput(body.output)
    } catch (err) {
      setTestOutput(err instanceof Error ? err.message : 'Test failed')
    } finally {
      setTesting(false)
    }
  }

  if (loading) return <p className="admin-message">Loading Article Machine…</p>

  return (
    <div className="admin-stack">
      <AdminPanel title="Automation Status">
        <div className="admin-automation-row">
          <div>
            <span className={`admin-badge ${automationEnabled ? 'admin-badge--success' : 'admin-badge--warn'}`}>
              {automationEnabled ? 'Automation Active' : 'Automation Paused'}
            </span>
            <p className="admin-panel__desc" style={{ marginTop: '0.5rem' }}>
              Last run: {lastRun ? new Date(lastRun).toLocaleString() : 'Never'}
            </p>
            <p className="admin-hint">
              {PUBLISHING_CRON_RUNS_PER_DAY} crons/day × batch {PUBLISHING_BATCH_SIZE} (target {PUBLISHING_DAILY_TARGET}/day).
              Done today: {processedToday} · This month: {processedMonth}
            </p>
          </div>
          <button
            type="button"
            className="admin-btn admin-btn--ghost"
            disabled={savingToggle}
            onClick={() => toggleAutomation(!automationEnabled)}
          >
            {automationEnabled ? 'Pause' : 'Activate'}
          </button>
        </div>
      </AdminPanel>

      <PublishingHealthPanel />

      <AdminPanel
        title="Article Machine System Prompts"
        description="Configure template-specific instructions for Claude. Empty tabs fall back to Default."
      >
        <div className="admin-tabs">
          {ARTICLE_MACHINE_PROMPT_TABS.map((t) => (
            <button
              key={t.id}
              type="button"
              className={`admin-tab${activeTab === t.id ? ' admin-tab--active' : ''}`}
              onClick={() => {
                setActiveTab(t.id)
                setTestOutput(null)
              }}
            >
              {t.tabLabel}
            </button>
          ))}
        </div>

        <div className="admin-prompt-header">
          <div>
            <h3 className="admin-prompt-title">{tab.label}</h3>
            <p className="admin-hint">
              {activeText.length.toLocaleString()} chars · ~{estimatePromptTokens(activeText)} tokens
              {promptUpdatedAt[tab.configKey] &&
                ` · Saved ${new Date(promptUpdatedAt[tab.configKey]!).toLocaleString()}`}
            </p>
          </div>
          <div className="admin-form-actions">
            {usingDefault && <span className="admin-badge admin-badge--warn">Using Default</span>}
            {!usingDefault && activeText.trim() && (
              <span className="admin-badge admin-badge--success">Active</span>
            )}
            {!tab.templateType && (
              <span className="admin-badge admin-badge--muted">
                Fallback for {countTemplatesUsingDefault(prompts)} templates
              </span>
            )}
          </div>
        </div>

        <textarea
          className="admin-textarea admin-textarea--tall"
          rows={14}
          value={activeText}
          onChange={(e) => updatePrompt(e.target.value)}
          placeholder={`Built-in ${tab.tabLabel} prompt used when empty…`}
        />

        <div className="admin-form-actions admin-form-actions--footer">
          <button type="button" className="admin-btn admin-btn--ghost admin-btn--sm" onClick={testPrompt} disabled={testing}>
            {testing ? 'Testing…' : 'Test Prompt'}
          </button>
          <button
            type="button"
            className="admin-btn admin-btn--primary admin-btn--sm"
            onClick={savePrompt}
            disabled={savingKey === tab.configKey}
          >
            Save {tab.saveLabel} Prompt
          </button>
        </div>

        {testOutput && (
          <div className="admin-test-output">
            <p className="admin-label">Test output</p>
            <pre>{testOutput}</pre>
          </div>
        )}
      </AdminPanel>

      <AdminPanel title="Persona Widget (Homepage)">
        <div className="admin-stack">
          <div>
            <label className="admin-label">Welcome message</label>
            <input className="admin-input" value={welcomeMessage} onChange={(e) => setWelcomeMessage(e.target.value)} />
          </div>
          <div>
            <label className="admin-label">System prompt</label>
            <textarea className="admin-textarea" rows={6} value={personaPrompt} onChange={(e) => setPersonaPrompt(e.target.value)} />
          </div>
          <button type="button" className="admin-btn admin-btn--primary admin-btn--sm" onClick={savePersona}>
            Save Persona Settings
          </button>
        </div>
      </AdminPanel>

      {message && <p className="admin-message">{message}</p>}
    </div>
  )
}
