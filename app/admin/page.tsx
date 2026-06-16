import Link from 'next/link'
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

  if (supabase) {
    const [posts, schedule, leads, cronConfig] = await Promise.all([
      supabase.from('blog_posts').select('id', { count: 'exact', head: true }).eq('status', 'published'),
      supabase.from('publishing_schedule').select('id', { count: 'exact', head: true }).eq('status', 'pending'),
      supabase.from('lead_captures').select('id', { count: 'exact', head: true }).eq('status', 'new'),
      supabase.from('ai_config').select('value').eq('key', 'last_cron_run').maybeSingle(),
    ])

    stats = {
      publishedPosts: posts.count ?? 0,
      pendingSchedule: schedule.count ?? 0,
      newLeads: leads.count ?? 0,
      lastCron: cronConfig.data?.value
        ? String(cronConfig.data.value).replace(/^"|"$/g, '')
        : 'Never',
    }
  }

  return (
    <div>
      <h1 className="h3 mb-4">Dashboard</h1>
      <div className="row g-3 mb-4">
        <div className="col-md-3">
          <div className="admin-card">
            <div className="text-muted small">Published Posts</div>
            <div className="h2">{stats.publishedPosts}</div>
          </div>
        </div>
        <div className="col-md-3">
          <div className="admin-card">
            <div className="text-muted small">Pending Schedule</div>
            <div className="h2">{stats.pendingSchedule}</div>
          </div>
        </div>
        <div className="col-md-3">
          <div className="admin-card">
            <div className="text-muted small">New Leads</div>
            <div className="h2">{stats.newLeads}</div>
          </div>
        </div>
        <div className="col-md-3">
          <div className="admin-card">
            <div className="text-muted small">Last Cron Run</div>
            <div style={{ fontSize: 14 }}>{stats.lastCron}</div>
          </div>
        </div>
      </div>
      <div className="admin-card">
        <h2 className="h5">Quick Actions</h2>
        <div className="d-flex gap-2 flex-wrap">
          <Link href="/admin/schedule" className="btn btn-primary btn-sm">Manage Schedule</Link>
          <Link href="/admin/articles" className="btn btn-outline-primary btn-sm">View Articles</Link>
          <Link href="/admin/settings/article-machine" className="btn btn-outline-primary btn-sm">Article Machine</Link>
        </div>
      </div>
    </div>
  )
}
