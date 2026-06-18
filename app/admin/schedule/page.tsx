'use client'

import { useEffect, useState } from 'react'
import { createBrowserSupabase } from '@/lib/supabase'
import { TEMPLATE_TYPES } from '@/config/articleMachinePrompts'
import AdminPanel from '@/components/admin/AdminPanel'
import StatusBadge from '@/components/admin/StatusBadge'

type ScheduleItem = {
  id: string
  card_name: string
  template_type: string
  scheduled_date: string
  status: string
  error_text: string | null
}

export default function AdminSchedulePage() {
  const [items, setItems] = useState<ScheduleItem[]>([])
  const [topic, setTopic] = useState('')
  const [templateType, setTemplateType] = useState('seo-blog')
  const [scheduledDate, setScheduledDate] = useState(new Date().toISOString().slice(0, 10))
  const [loading, setLoading] = useState(false)
  const [message, setMessage] = useState('')

  const supabase = createBrowserSupabase()

  async function loadSchedule() {
    const { data } = await supabase
      .from('publishing_schedule')
      .select('*')
      .order('scheduled_date', { ascending: true })
      .limit(100)
    setItems((data as ScheduleItem[]) ?? [])
  }

  useEffect(() => {
    loadSchedule()
  }, [])

  async function addToSchedule(e: React.FormEvent) {
    e.preventDefault()
    if (!topic.trim()) return
    setLoading(true)
    setMessage('')
    const { error } = await supabase.from('publishing_schedule').insert({
      card_name: topic.trim(),
      template_type: templateType,
      scheduled_date: scheduledDate,
      status: 'pending',
    })
    setLoading(false)
    if (error) {
      setMessage(error.message)
    } else {
      setTopic('')
      setMessage('Added to schedule')
      loadSchedule()
    }
  }

  async function generateNow(item: ScheduleItem) {
    setMessage('Generating…')
    const { data: { session } } = await supabase.auth.getSession()
    const token = session?.access_token || ''
    const res = await fetch('/api/generate-article', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({
        card_name: item.card_name,
        template_type: item.template_type,
        schedule_id: item.id,
      }),
    })
    const body = await res.json()
    setMessage(res.ok ? `Published: ${body.slug}` : body.error || 'Failed')
    loadSchedule()
  }

  return (
    <div className="admin-stack">
      <AdminPanel
        title="Add Topic"
        description="Queue articles for automated publishing. Cron runs 7× daily, processing up to 3 pending items per run."
      >
        <form onSubmit={addToSchedule} className="admin-form-grid admin-form-grid--schedule">
          <div>
            <label className="admin-label" htmlFor="topic">
              Article topic
            </label>
            <input
              id="topic"
              className="admin-input"
              placeholder="Flutter vs React Native 2026"
              value={topic}
              onChange={(e) => setTopic(e.target.value)}
              required
            />
          </div>
          <div>
            <label className="admin-label" htmlFor="template">
              Template
            </label>
            <select
              id="template"
              className="admin-select"
              value={templateType}
              onChange={(e) => setTemplateType(e.target.value)}
            >
              {Object.entries(TEMPLATE_TYPES).map(([k, v]) => (
                <option key={k} value={k}>
                  {v}
                </option>
              ))}
            </select>
          </div>
          <div>
            <label className="admin-label" htmlFor="date">
              Scheduled date
            </label>
            <input
              id="date"
              type="date"
              className="admin-input"
              value={scheduledDate}
              onChange={(e) => setScheduledDate(e.target.value)}
            />
          </div>
          <button type="submit" className="admin-btn admin-btn--primary" disabled={loading}>
            Add
          </button>
        </form>
        {message && <p className="admin-message">{message}</p>}
      </AdminPanel>

      <AdminPanel title="Schedule Queue">
        <div className="admin-table-wrap">
          <table className="admin-table">
            <thead>
              <tr>
                <th>Topic</th>
                <th>Template</th>
                <th>Date</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {items.map((item) => (
                <tr key={item.id}>
                  <td>{item.card_name}</td>
                  <td>{item.template_type}</td>
                  <td>{item.scheduled_date}</td>
                  <td>
                    <StatusBadge status={item.status} />
                    {item.error_text && (
                      <small className="admin-message admin-message--error" style={{ display: 'block', marginTop: 4 }}>
                        {item.error_text}
                      </small>
                    )}
                  </td>
                  <td>
                    {item.status === 'pending' && (
                      <button
                        type="button"
                        className="admin-btn admin-btn--ghost admin-btn--sm"
                        onClick={() => generateNow(item)}
                      >
                        Generate Now
                      </button>
                    )}
                  </td>
                </tr>
              ))}
              {items.length === 0 && (
                <tr>
                  <td colSpan={5} style={{ color: 'var(--admin-muted)' }}>
                    No items in schedule
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </AdminPanel>
    </div>
  )
}
