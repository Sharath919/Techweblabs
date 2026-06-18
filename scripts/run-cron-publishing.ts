/**
 * Trigger cron publishing locally or from CI.
 * Usage: CRON_SECRET=xxx npm run cron:publishing
 */
const siteUrl = (
  process.env.NEXT_PUBLIC_SITE_URL ||
  process.env.SITE_URL ||
  'http://localhost:3000'
).replace(/\/$/, '')

const secret = process.env.CRON_SECRET || ''

async function main() {
  if (!secret) {
    console.error('Set CRON_SECRET env var')
    process.exit(1)
  }

  const res = await fetch(`${siteUrl}/api/cron/publishing`, {
    method: 'POST',
    headers: { Authorization: `Bearer ${secret}` },
  })

  const body = await res.json()
  console.log(JSON.stringify(body, null, 2))
  process.exit(res.ok ? 0 : 1)
}

main().catch((err) => {
  console.error(err)
  process.exit(1)
})
