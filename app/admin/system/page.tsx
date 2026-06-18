import AdminPanel from '@/components/admin/AdminPanel'

export default function AdminSystemPage() {
  const siteUrl = process.env.NEXT_PUBLIC_SITE_URL || 'https://techweblabs.com'

  return (
    <div className="admin-stack">
      <AdminPanel
        title="Cron Publishing"
        description="Vercel crons: 7 runs/day UTC at 01, 04, 07, 10, 13, 16, 19 — batch size 3 per run (up to 21/day). Queue 10 topics in Schedule."
      >
        <code className="admin-code">
          POST {siteUrl}/api/cron/publishing{'\n'}
          Authorization: Bearer CRON_SECRET
        </code>
      </AdminPanel>

      <AdminPanel title="Deployment Checklist">
        <ul className="admin-list">
          <li>Set env vars from <code>.env.example</code> in Vercel</li>
          <li>Run Supabase migration: <code>supabase/migrations/20260616120000_techweblabs_initial.sql</code></li>
          <li>Run <code>npm run migrate:mysql</code> to import existing blog posts</li>
          <li>Point <code>techweblabs.com</code> DNS to Vercel</li>
          <li>Create admin user in Supabase Auth</li>
        </ul>
      </AdminPanel>

      <AdminPanel title="Health Check">
        <p className="admin-panel__desc" style={{ margin: 0 }}>
          <a href="/api/health" target="_blank" rel="noopener noreferrer">
            /api/health
          </a>
        </p>
      </AdminPanel>
    </div>
  )
}
