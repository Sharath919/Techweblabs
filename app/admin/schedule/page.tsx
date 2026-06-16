'use client'

import { useEffect, useState } from 'react'
import { createBrowserSupabase } from '@/lib/supabase'
import { TEMPLATE_TYPES } from '@/config/articleMachinePrompts'

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
    <div>
      <h1 className="h3 mb-4">Publishing Schedule</h1>
      <div className="admin-card mb-4">
        <h2 className="h5">Add Topic</h2>
        <form onSubmit={addToSchedule} className="row g-3">
          <div className="col-md-5">
            <input
              className="form-control"
              placeholder="Article topic (e.g. Flutter vs React Native 2025)"
              value={topic}
              onChange={(e) => setTopic(e.target.value)}
              required
            />
          </div>
          <div className="col-md-3">
            <select className="form-select" value={templateType} onChange={(e) => setTemplateType(e.target.value)}>
              {Object.entries(TEMPLATE_TYPES).map(([k, v]) => (
                <option key={k} value={k}>{v}</option>
              ))}
            </select>
          </div>
          <div className="col-md-2">
            <input type="date" className="form-control" value={scheduledDate} onChange={(e) => setScheduledDate(e.target.value)} />
          </div>
          <div className="col-md-2">
            <button type="submit" className="btn btn-primary w-100" disabled={loading}>Add</button>
          </div>
        </form>
        {message && <p className="mt-2 small text-muted">{message}</p>}
      </div>
      <div className="admin-card">
        <table className="table table-sm">
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
                  <span className={`badge ${item.status === 'done' ? 'bg-success' : item.status === 'failed' ? 'bg-danger' : 'bg-secondary'}`}>
                    {item.status}
                  </span>
                  {item.error_text && <small className="d-block text-danger">{item.error_text}</small>}
                </td>
                <td>
                  {item.status === 'pending' && (
                    <button type="button" className="btn btn-sm btn-outline-primary" onClick={() => generateNow(item)}>
                      Generate Now
                    </button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  )
}
