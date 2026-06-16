/**
 * Fetch static HTML from the live TechWebLabs site into public/static/.
 * Used on Vercel build where PHP export is unavailable.
 */
import { existsSync, mkdirSync, writeFileSync } from 'fs'
import { join } from 'path'
import { DIRECT_PHP_SLUGS, STATIC_SLUG_MAP } from '../src/config/static-slug-manifest'

const ROOT = join(import.meta.dirname, '..')
const STATIC_DIR = join(ROOT, 'public', 'static')
const ORIGIN = (process.env.STATIC_FETCH_ORIGIN || 'https://techweblabs.com').replace(/\/$/, '')

const PERSONA_SNIPPET = `
<div id="twl-persona-root"></div>
<script src="/widgets/persona.js" defer></script>
`

const BATCH_SIZE = 8

function injectPersona(html: string): string {
  if (html.includes('twl-persona-root')) return html
  if (html.includes('</body>')) {
    return html.replace('</body>', `${PERSONA_SNIPPET}\n</body>`)
  }
  return html + PERSONA_SNIPPET
}

async function fetchPage(path: string): Promise<string | null> {
  const url = `${ORIGIN}${path}`
  try {
    const res = await fetch(url, {
      headers: { 'User-Agent': 'TechWebLabs-StaticExport/1.0' },
      redirect: 'follow',
    })
    if (!res.ok) {
      console.warn(`  ⚠ ${path} → HTTP ${res.status}`)
      return null
    }
    const html = await res.text()
    if (!html.includes('<html') && !html.includes('<HTML')) {
      console.warn(`  ⚠ ${path} → not HTML`)
      return null
    }
    return html
  } catch (err) {
    console.warn(`  ⚠ ${path} → ${err instanceof Error ? err.message : 'failed'}`)
    return null
  }
}

async function runBatch<T, R>(items: T[], fn: (item: T) => Promise<R>): Promise<R[]> {
  const results: R[] = []
  for (let i = 0; i < items.length; i += BATCH_SIZE) {
    const batch = items.slice(i, i + BATCH_SIZE)
    const batchResults = await Promise.all(batch.map(fn))
    results.push(...batchResults)
  }
  return results
}

async function main() {
  mkdirSync(STATIC_DIR, { recursive: true })

  const slugsToExport: Array<{ file: string; path: string }> = [
    { file: 'index.html', path: '/' },
    ...Object.entries(STATIC_SLUG_MAP)
      .filter(([k]) => k !== '/')
      .map(([slug, path]) => ({ file: `${slug}.html`, path })),
    ...DIRECT_PHP_SLUGS.map((slug) => ({ file: `${slug}.html`, path: `/${slug}` })),
  ]

  const seen = new Set<string>()
  const unique: Array<{ file: string; path: string }> = []
  for (const item of slugsToExport) {
    if (seen.has(item.file)) continue
    seen.add(item.file)
    unique.push(item)
  }

  console.log(`Fetching ${unique.length} pages from ${ORIGIN}…`)

  let exported = 0
  await runBatch(unique, async ({ file, path }) => {
    console.log(`  ${path} → static/${file}`)
    let html = await fetchPage(path)
    if (!html) return

    if (file === 'index.html') {
      html = injectPersona(html)
    }

    writeFileSync(join(STATIC_DIR, file), html, 'utf8')
    exported++
  })

  if (exported === 0) {
    console.error('No static pages fetched — check network or STATIC_FETCH_ORIGIN')
    process.exit(1)
  }

  const indexPath = join(STATIC_DIR, 'index.html')
  if (!existsSync(indexPath)) {
    console.error('Homepage (index.html) was not fetched')
    process.exit(1)
  }

  console.log(`\nFetched ${exported} static pages to public/static/`)
}

main().catch((err) => {
  console.error(err)
  process.exit(1)
})
