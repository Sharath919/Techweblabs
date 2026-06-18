'use client'

import Link from 'next/link'
import { useCallback, useEffect, useMemo, useState } from 'react'
import { createBrowserSupabase } from '@/lib/supabase'

type AuditReport = {
  published_articles_by_utc_day: Record<string, number>
  schedule_by_date: Record<string, { done: number; pending: number; failed: number }>
  backlog_pending_before_today_utc: { count: number }
  failed_stuck_count: number
  failures_sample: Array<{ card_name: string; error_text: string | null; updated_at: string }>
  published_today: number
  schedule_done_today: number
  today_utc: string
}

async function getToken() {
  const supabase = createBrowserSupabase()
  const { data: { session } } = await supabase.auth.getSession()
  return session?.access_token || ''
}

export default function PublishingHealthPanel() {
  const [days, setDays] = useState(7)
  const [audit, setAudit] = useState<AuditReport | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const load = useCallback(async () => {
    setLoading(true)
    setError(null)
    try {
      const token = await getToken()
      const res = await fetch(`/api/audit-publishing?days=${days}`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      const body = await res.json()
      if (!res.ok) throw new Error(body.error || 'Failed to load')
      setAudit(body)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load')
      setAudit(null)
    } finally {
      setLoading(false)
    }
  }, [days])

  useEffect(() => {
    void load()
  }, [load])

  const chartRows = useMemo(() => {
    if (!audit) return []
    const dates = new Set<string>()
    Object.keys(audit.published_articles_by_utc_day).forEach((d) => dates.add(d))
    Object.keys(audit.schedule_by_date).forEach((d) => dates.add(d))
    return [...dates].sort().map((date) => ({
      date: date.slice(5),
      published: audit.published_articles_by_utc_day[date] ?? 0,
      done: audit.schedule_by_date[date]?.done ?? 0,
    }))
  }, [audit])

  const maxBar = Math.max(1, ...chartRows.flatMap((r) => [r.published, r.done]))

  return (
    <section className="admin-panel">
      <header className="admin-panel__header">
        <div>
          <h2 className="admin-panel__title">Publishing Health</h2>
          <p className="admin-panel__desc">Published posts vs schedule completions (UTC days).</p>
        </div>
        <div className="admin-panel__actions">
          <select
            className="admin-select admin-select--sm"
            value={days}
            onChange={(e) => setDays(Number(e.target.value))}
          >
            {[4, 7, 14].map((d) => (
              <option key={d} value={d}>
                Last {d} days
              </option>
            ))}
          </select>
          <button type="button" className="admin-btn admin-btn--ghost admin-btn--sm" onClick={() => void load()} disabled={loading}>
            {loading ? 'Loading…' : 'Refresh'}
          </button>
        </div>
      </header>
      <div className="admin-panel__body admin-stack">
        {error && <p className="admin-message admin-message--error">{error}</p>}
        {audit && (
          <>
            <div className="admin-stat-grid">
              <div className="admin-stat-card">
                <p className="admin-stat-card__label">Published today (UTC)</p>
                <p className="admin-stat-card__value">{audit.published_today}</p>
              </div>
              <div className="admin-stat-card">
                <p className="admin-stat-card__label">Schedule done today</p>
                <p className="admin-stat-card__value">{audit.schedule_done_today}</p>
              </div>
              <div className="admin-stat-card">
                <p className="admin-stat-card__label">Backlog</p>
                <p className="admin-stat-card__value">{audit.backlog_pending_before_today_utc.count}</p>
              </div>
              <div className="admin-stat-card">
                <p className="admin-stat-card__label">Failed / stuck</p>
                <p className="admin-stat-card__value">{audit.failed_stuck_count}</p>
              </div>
            </div>

            {audit.backlog_pending_before_today_utc.count > 0 && (
              <p className="admin-alert">
                {audit.backlog_pending_before_today_utc.count} overdue pending rows —{' '}
                <Link href="/admin/schedule">review schedule</Link>
              </p>
            )}

            {chartRows.length > 0 && (
              <div className="admin-bar-chart">
                {chartRows.map((row) => (
                  <div key={row.date} className="admin-bar-chart__col">
                    <div className="admin-bar-chart__bars">
                      <div
                        className="admin-bar-chart__bar admin-bar-chart__bar--published"
                        style={{ height: `${(row.published / maxBar) * 100}%` }}
                        title={`Published: ${row.published}`}
                      />
                      <div
                        className="admin-bar-chart__bar admin-bar-chart__bar--done"
                        style={{ height: `${(row.done / maxBar) * 100}%` }}
                        title={`Schedule done: ${row.done}`}
                      />
                    </div>
                    <span className="admin-bar-chart__label">{row.date}</span>
                  </div>
                ))}
              </div>
            )}

            {audit.failures_sample.length > 0 && (
              <div className="admin-table-wrap">
                <table className="admin-table">
                  <thead>
                    <tr>
                      <th>Topic</th>
                      <th>Error</th>
                      <th>When</th>
                    </tr>
                  </thead>
                  <tbody>
                    {audit.failures_sample.map((f, i) => (
                      <tr key={i}>
                        <td>{f.card_name}</td>
                        <td>{f.error_text || '—'}</td>
                        <td>{new Date(f.updated_at).toLocaleString()}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </>
        )}
      </div>
    </section>
  )
}
