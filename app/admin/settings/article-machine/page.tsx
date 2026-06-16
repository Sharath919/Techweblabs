'use client'

import { useEffect, useState } from 'react'
import { createBrowserSupabase } from '@/lib/supabase'
import { getBuiltInArticlePrompt, TEMPLATE_TYPES } from '@/config/articleMachinePrompts'
import AdminPanel from '@/components/admin/AdminPanel'

export default function ArticleMachineSettingsPage() {
  const [automationEnabled, setAutomationEnabled] = useState(true)
  const [prompts, setPrompts] = useState<Record<string, string>>({})
  const [personaPrompt, setPersonaPrompt] = useState('')
  const [welcomeMessage, setWelcomeMessage] = useState('')
  const [message, setMessage] = useState('')
  const supabase = createBrowserSupabase()

  useEffect(() => {
    async function load() {
      const keys = [
        'automation_enabled',
        'persona_system_prompt',
        'persona_welcome_message',
        ...Object.keys(TEMPLATE_TYPES).map((t) => `article_machine_prompt_${t.replace(/-/g, '_')}`),
        'article_machine_prompt_default',
      ]
      const { data } = await supabase.from('ai_config').select('key, value').in('key', keys)
      const map: Record<string, string> = {}
      for (const row of data ?? []) {
        const v = row.value
        map[row.key] = typeof v === 'string' ? v.replace(/^"|"$/g, '') : JSON.stringify(v ?? '')
      }
      setAutomationEnabled(map.automation_enabled !== 'false')
      setPersonaPrompt(map.persona_system_prompt || '')
      setWelcomeMessage(map.persona_welcome_message || '')
      setPrompts(map)
    }
    load()
  }, [])

  async function save() {
    setMessage('Saving…')
    const upserts = [
      { key: 'automation_enabled', value: automationEnabled ? 'true' : 'false' },
      { key: 'persona_system_prompt', value: JSON.stringify(personaPrompt) },
      { key: 'persona_welcome_message', value: JSON.stringify(welcomeMessage) },
      ...Object.entries(prompts)
        .filter(([k]) => k.startsWith('article_machine_prompt_'))
        .map(([key, value]) => ({ key, value: JSON.stringify(value) })),
    ]
    for (const row of upserts) {
      await supabase.from('ai_config').upsert({ ...row, updated_at: new Date().toISOString() })
    }
    setMessage('Saved!')
  }

  return (
    <div className="admin-stack">
      <AdminPanel
        title="Automation"
        description="When enabled, Vercel cron jobs process pending schedule items automatically."
        actions={
          <button type="button" className="admin-btn admin-btn--primary admin-btn--sm" onClick={save}>
            Save Settings
          </button>
        }
      >
        <label className="admin-switch">
          <input
            type="checkbox"
            checked={automationEnabled}
            onChange={(e) => setAutomationEnabled(e.target.checked)}
          />
          Automation enabled (cron will process schedule)
        </label>
        {message && <p className="admin-message">{message}</p>}
      </AdminPanel>

      {Object.entries(TEMPLATE_TYPES).map(([type, label]) => {
        const key = `article_machine_prompt_${type.replace(/-/g, '_')}`
        return (
          <AdminPanel key={type} title={`${label} Prompt`}>
            <textarea
              className="admin-textarea"
              rows={6}
              value={prompts[key] || ''}
              onChange={(e) => setPrompts({ ...prompts, [key]: e.target.value })}
              placeholder={getBuiltInArticlePrompt(type).slice(0, 200) + '…'}
            />
          </AdminPanel>
        )
      })}

      <AdminPanel title="Persona Welcome Message">
        <input className="admin-input" value={welcomeMessage} onChange={(e) => setWelcomeMessage(e.target.value)} />
      </AdminPanel>

      <AdminPanel title="Persona System Prompt">
        <textarea
          className="admin-textarea"
          rows={8}
          value={personaPrompt}
          onChange={(e) => setPersonaPrompt(e.target.value)}
        />
      </AdminPanel>
    </div>
  )
}
