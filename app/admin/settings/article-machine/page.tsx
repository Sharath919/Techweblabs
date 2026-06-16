'use client'

import { useEffect, useState } from 'react'
import { createBrowserSupabase } from '@/lib/supabase'
import { getBuiltInArticlePrompt, TEMPLATE_TYPES } from '@/config/articleMachinePrompts'

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
    <div>
      <h1 className="h3 mb-4">Article Machine Settings</h1>
      <div className="admin-card mb-4">
        <div className="form-check form-switch mb-3">
          <input
            className="form-check-input"
            type="checkbox"
            checked={automationEnabled}
            onChange={(e) => setAutomationEnabled(e.target.checked)}
            id="automation"
          />
          <label className="form-check-label" htmlFor="automation">Automation enabled (cron will process schedule)</label>
        </div>
        <button type="button" className="btn btn-primary" onClick={save}>Save Settings</button>
        {message && <span className="ms-3 small text-muted">{message}</span>}
      </div>
      {Object.entries(TEMPLATE_TYPES).map(([type, label]) => {
        const key = `article_machine_prompt_${type.replace(/-/g, '_')}`
        return (
          <div key={type} className="admin-card mb-3">
            <h2 className="h6">{label} Prompt</h2>
            <textarea
              className="form-control font-monospace small"
              rows={6}
              value={prompts[key] || ''}
              onChange={(e) => setPrompts({ ...prompts, [key]: e.target.value })}
              placeholder={getBuiltInArticlePrompt(type).slice(0, 200) + '…'}
            />
          </div>
        )
      })}
      <div className="admin-card mb-3">
        <h2 className="h6">Persona Welcome Message</h2>
        <input className="form-control" value={welcomeMessage} onChange={(e) => setWelcomeMessage(e.target.value)} />
      </div>
      <div className="admin-card mb-3">
        <h2 className="h6">Persona System Prompt</h2>
        <textarea className="form-control font-monospace small" rows={8} value={personaPrompt} onChange={(e) => setPersonaPrompt(e.target.value)} />
      </div>
    </div>
  )
}
