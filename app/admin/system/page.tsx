export default function AdminSystemPage() {
  return (
    <div>
      <h1 className="h3 mb-4">System</h1>
      <div className="admin-card mb-3">
        <h2 className="h5">Cron Publishing</h2>
        <p className="small text-muted">
          On Vercel Hobby, use external cron (cron-job.org or GitHub Actions) to hit:
        </p>
        <code className="d-block p-2 bg-light rounded">
          POST {process.env.NEXT_PUBLIC_SITE_URL || 'https://techweblabs.com'}/api/cron/publishing
          <br />
          Authorization: Bearer CRON_SECRET
        </code>
        <p className="small mt-2">Recommended: every 3 hours (batch size 3 ≈ 8-12 posts/day)</p>
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
