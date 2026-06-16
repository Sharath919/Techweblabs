export default function AdminSystemPage() {
  return (
    <div>
      <h1 className="h3 mb-4">System</h1>
      <div className="admin-card mb-3">
        <h2 className="h5">Cron Publishing</h2>
        <p className="small text-muted">
          Vercel crons (same as Limansa): 7 runs/day UTC at 01, 04, 07, 10, 13, 16, 19 — batch size 3 per run (up to 21/day).
          Queue topics in Schedule; each run processes up to 3 pending items.
        </p>
        <code className="d-block p-2 bg-light rounded">
          POST {process.env.NEXT_PUBLIC_SITE_URL || 'https://techweblabs.com'}/api/cron/publishing
          <br />
          Authorization: Bearer CRON_SECRET
        </code>
        <p className="small mt-2">Target: 10/day — queue 10 topics; crons spread publishing across the day.</p>
      </div>
      <div className="admin-card mb-3">
        <h2 className="h5">Vercel Setup</h2>
        <ul className="small">
          <li>Create a new Vercel Hobby account (separate from Limansa)</li>
          <li>Connect this repo and set env vars from <code>.env.example</code></li>
          <li>Run Supabase migration: <code>supabase/migrations/20260616120000_techweblabs_initial.sql</code></li>
          <li>Run <code>npm run migrate:mysql</code> to import existing blog posts</li>
          <li>Run <code>npm run build:full</code> before first deploy</li>
          <li>Point techweblabs.com DNS to Vercel</li>
        </ul>
      </div>
      <div className="admin-card">
        <h2 className="h5">Health Check</h2>
        <p><a href="/api/health" target="_blank" rel="noopener noreferrer">/api/health</a></p>
      </div>
    </div>
  )
}
