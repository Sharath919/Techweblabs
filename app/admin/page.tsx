import Link from 'next/link'
import { CalendarClock, FileText, Bot, UserPlus } from 'lucide-react'
import StatCard from '@/components/admin/StatCard'
import AdminPanel from '@/components/admin/AdminPanel'
import StatusBadge from '@/components/admin/StatusBadge'
import { createServerSupabase } from '@/lib/supabase'

export const dynamic = 'force-dynamic'

export default async function AdminDashboard() {
  const supabase = createServerSupabase()

  let stats = {
    publishedPosts: 0,
    pendingSchedule: 0,
    newLeads: 0,
    lastCron: 'Never',
  }

  let recentSchedule: Array<{
    id: string
    card_name: string
    scheduled_date: string
    status: string
  }> = []

  if (supabase) {
    const [posts, schedule, leads, cronConfig, recent] = await Promise.all([
      supabase.from('blog_posts').select('id', { count: 'exact', head: true }).eq('status', 'published'),
      supabase.from('publishing_schedule').select('id', { count: 'exact', head: true }).eq('status', 'pending'),
      supabase.from('lead_captures').select('id', { count: 'exact', head: true }).eq('status', 'new'),
      supabase.from('ai_config').select('value').eq('key', 'last_cron_run').maybeSingle(),
      supabase
        .from('publishing_schedule')
        .select('id, card_name, scheduled_date, status')
        .order('scheduled_date', { ascending: false })
        .limit(8),
    ])

    stats = {
      publishedPosts: posts.count ?? 0,
      pendingSchedule: schedule.count ?? 0,
      newLeads: leads.count ?? 0,
      lastCron: cronConfig.data?.value
        ? String(cronConfig.data.value).replace(/^"|"$/g, '')
        : 'Never',
    }
    recentSchedule = recent.data ?? []
  }

  return (
    <div className="admin-stack">
      <div className="admin-stat-grid">
        <StatCard icon={FileText} label="Published Posts" value={stats.publishedPosts} />
        <StatCard icon={CalendarClock} label="Pending Schedule" value={stats.pendingSchedule} />
        <StatCard icon={UserPlus} label="New Leads" value={stats.newLeads} />
        <StatCard icon={Bot} label="Last Cron Run" value={stats.lastCron} hint="UTC" />
      </div>

      <AdminPanel
        title="Quick Actions"
        description="Manage automated publishing, review articles, and configure the article engine."
        actions={
          <>
            <Link href="/admin/schedule" className="admin-btn admin-btn--primary admin-btn--sm">
              Manage Schedule
            </Link>
            <Link href="/admin/articles" className="admin-btn admin-btn--ghost admin-btn--sm">
              View Articles
            </Link>
            <Link href="/admin/settings/article-machine" className="admin-btn admin-btn--ghost admin-btn--sm">
              Article Machine
            </Link>
          </>
        }
      >
        <p className="admin-panel__desc" style={{ margin: 0 }}>
          Queue up to 10 topics per day. Vercel runs 7 crons (batch of 3) — same pattern as Limansa.
        </p>
      </AdminPanel>

      <AdminPanel title="Recent Schedule">
        <div className="admin-table-wrap">
          <table className="admin-table">
            <thead>
              <tr>
                <th>Topic</th>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              {recentSchedule.map((item) => (
                <tr key={item.id}>
                  <td>{item.card_name}</td>
                  <td>{item.scheduled_date}</td>
                  <td>
                    <StatusBadge status={item.status} />
                  </td>
                </tr>
              ))}
              {recentSchedule.length === 0 && (
                <tr>
                  <td colSpan={3} style={{ color: 'var(--admin-muted)' }}>
                    No scheduled topics yet — add some in Schedule.
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
