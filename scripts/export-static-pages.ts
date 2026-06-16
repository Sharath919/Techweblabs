/**
 * Export PHP pages to static HTML via local PHP dev server.
 * Usage: npm run export-static (requires PHP in PATH)
 */
import { spawn, type ChildProcess } from 'child_process'
import { mkdirSync, writeFileSync, readFileSync, existsSync } from 'fs'
import { join } from 'path'
import { STATIC_SLUG_MAP, DIRECT_PHP_SLUGS } from '../src/config/static-slug-manifest'

const ROOT = join(import.meta.dirname, '..')
const STATIC_DIR = join(ROOT, 'public', 'static')
const PORT = 8765
const BASE = `http://127.0.0.1:${PORT}`

const PERSONA_SNIPPET = `
<div id="twl-persona-root"></div>
<script src="/widgets/persona.js" defer></script>
`

function sleep(ms: number) {
  return new Promise((r) => setTimeout(r, ms))
}

async function waitForServer(maxAttempts = 30): Promise<boolean> {
  for (let i = 0; i < maxAttempts; i++) {
    try {
      const res = await fetch(`${BASE}/api/health`).catch(() => fetch(BASE))
      if (res.ok || res.status === 404) return true
    } catch {
      // retry
    }
    await sleep(500)
  }
  try {
    const res = await fetch(BASE)
    return res.ok
  } catch {
    return false
  }
}

async function fetchPage(path: string): Promise<string | null> {
  try {
    const res = await fetch(`${BASE}${path}`, { redirect: 'follow' })
    if (!res.ok) {
      console.warn(`  ⚠ ${path} → HTTP ${res.status}`)
      return null
    }
    return await res.text()
  } catch (err) {
    console.warn(`  ⚠ ${path} → ${err instanceof Error ? err.message : 'failed'}`)
    return null
  }
}

function injectPersona(html: string): string {
  if (html.includes('twl-persona-root')) return html
  if (html.includes('</body>')) {
    return html.replace('</body>', `${PERSONA_SNIPPET}\n</body>`)
  }
  return html + PERSONA_SNIPPET
}

async function main() {
  mkdirSync(STATIC_DIR, { recursive: true })

  let phpServer: ChildProcess | null = null

  console.log('Starting PHP server for static export…')
  phpServer = spawn('php', ['-S', `127.0.0.1:${PORT}`, 'router.php'], {
    cwd: ROOT,
    stdio: 'pipe',
  })

  await sleep(1500)
  const ready = await waitForServer()
  if (!ready) {
    console.error('PHP server failed to start. Ensure PHP is installed.')
    phpServer.kill()
    process.exit(1)
  }

  const slugsToExport: Array<{ file: string; path: string }> = [
    { file: 'index.html', path: '/' },
    ...Object.entries(STATIC_SLUG_MAP)
      .filter(([k]) => k !== '/')
      .map(([slug, path]) => ({ file: `${slug}.html`, path })),
    ...DIRECT_PHP_SLUGS.map((slug) => ({ file: `${slug}.html`, path: `/${slug}` })),
  ]

  const seen = new Set<string>()
  let exported = 0

  for (const { file, path } of slugsToExport) {
    if (seen.has(file)) continue
    seen.add(file)

    console.log(`Exporting ${path} → static/${file}`)
    let html = await fetchPage(path)
    if (!html) continue

    if (file === 'index.html') {
      html = injectPersona(html)
    }

    writeFileSync(join(STATIC_DIR, file), html, 'utf8')
    exported++
  }

  phpServer.kill()
  console.log(`\nExported ${exported} static pages to public/static/`)

  generateVercelRewrites(Array.from(seen))
}

function generateVercelRewrites(files: string[]) {
  const rewrites: Array<{ source: string; destination: string }> = []

  for (const file of files) {
    const slug = file === 'index.html' ? '/' : `/${file.replace('.html', '')}`
    const dest = `/static/${file}`
    rewrites.push({ source: slug, destination: dest })
  }

  const vercelPath = join(ROOT, 'vercel.json')
  let existing: Record<string, unknown> = {}
  if (existsSync(vercelPath)) {
    existing = JSON.parse(readFileSync(vercelPath, 'utf8'))
  }

  const merged = {
    ...existing,
    rewrites: [
      ...(Array.isArray(existing.rewrites) ? existing.rewrites : []),
      ...rewrites.filter(
        (r) =>
          !(Array.isArray(existing.rewrites) &&
            (existing.rewrites as Array<{ source: string }>).some((e) => e.source === r.source)),
      ),
    ],
  }

  writeFileSync(
    join(ROOT, 'scripts', 'generated-static-rewrites.json'),
    JSON.stringify(rewrites, null, 2),
    'utf8',
  )
  console.log(`Wrote scripts/generated-static-rewrites.json (${rewrites.length} routes)`)
}

main().catch((err) => {
  console.error(err)
  process.exit(1)
})
