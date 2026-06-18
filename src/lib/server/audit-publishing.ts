import { isAdminAccessToken } from '@/lib/server/admin-auth'
import { createClient } from '@supabase/supabase-js'

export const maxDuration = 60

function getServerSupabase() {
  const url = (process.env.SUPABASE_URL || process.env.NEXT_PUBLIC_SUPABASE_URL || '').trim()
  const key = (process.env.SUPABASE_SERVICE_ROLE_KEY || '').trim()
  if (!url || !key) return null
  return createClient(url, key)
}

function utcDate(d = new Date()): string {
  return d.toISOString().slice(0, 10)
}

export async function handleAuditPublishing(request: Request): Promise<Response> {
  const token = (request.headers.get('authorization') || '').replace(/^Bearer\s+/i, '').trim()
  const supabase = getServerSupabase()
  if (!supabase) return Response.json({ error: 'Server configuration error' }, { status: 500 })
  if (!(await isAdminAccessToken(supabase, token))) {
    return Response.json({ error: 'Unauthorized' }, { status: 401 })
  }

  const url = new URL(request.url)
  const days = Math.min(14, Math.max(4, parseInt(url.searchParams.get('days') || '7', 10) || 7))

  const start = new Date()
  start.setUTCDate(start.getUTCDate() - days + 1)
  start.setUTCHours(0, 0, 0, 0)

  const [postsRes, scheduleRes, failuresRes, usageRes] = await Promise.all([
    supabase
      .from('blog_posts')
      .select('published_at')
      .eq('status', 'published')
      .gte('published_at', start.toISOString()),
    supabase
      .from('publishing_schedule')
      .select('scheduled_date, status, updated_at')
      .gte('scheduled_date', start.toISOString().slice(0, 10)),
    supabase
      .from('publishing_schedule')
      .select('id, card_name, error_text, updated_at')
      .eq('status', 'failed')
      .order('updated_at', { ascending: false })
      .limit(10),
    supabase
      .from('api_usage_log')
      .select('created_at, success, operation')
      .eq('operation', 'article_generation')
      .gte('created_at', start.toISOString())
      .order('created_at', { ascending: false })
      .limit(20),
  ])

  const publishedByDay: Record<string, number> = {}
  for (const row of postsRes.data ?? []) {
    if (!row.published_at) continue
    const day = row.published_at.slice(0, 10)
    publishedByDay[day] = (publishedByDay[day] ?? 0) + 1
  }

  const scheduleByDate: Record<string, { done: number; pending: number; failed: number }> = {}
  for (const row of scheduleRes.data ?? []) {
    const day = row.scheduled_date
    if (!scheduleByDate[day]) scheduleByDate[day] = { done: 0, pending: 0, failed: 0 }
    if (row.status === 'done') scheduleByDate[day].done++
    else if (row.status === 'failed') scheduleByDate[day].failed++
    else scheduleByDate[day].pending++
  }

  const today = utcDate()
  const { count: backlog } = await supabase
    .from('publishing_schedule')
    .select('id', { count: 'exact', head: true })
    .eq('status', 'pending')
    .lt('scheduled_date', today)

  const { count: failedStuck } = await supabase
    .from('publishing_schedule')
    .select('id', { count: 'exact', head: true })
    .in('status', ['failed', 'processing'])

  return Response.json({
    days,
    published_articles_by_utc_day: publishedByDay,
    schedule_by_date: scheduleByDate,
    backlog_pending_before_today_utc: { count: backlog ?? 0 },
    failed_stuck_count: failedStuck ?? 0,
    failures_sample: failuresRes.data ?? [],
    recent_api_usage: usageRes.data ?? [],
    today_utc: today,
    published_today: publishedByDay[today] ?? 0,
    schedule_done_today: scheduleByDate[today]?.done ?? 0,
  })
}
