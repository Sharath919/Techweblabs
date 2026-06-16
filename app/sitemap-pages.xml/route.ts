import { getStaticSitemapPaths } from '@/config/static-slug-manifest'
import { createServerSupabase } from '@/lib/supabase'
import { SITE_URL } from '@/config/site'

export const revalidate = 3600

function urlEntry(
  loc: string,
  opts?: { lastmod?: string; changefreq?: string; priority?: string },
) {
  let xml = '  <url>\n    <loc>' + loc + '</loc>\n'
  if (opts?.lastmod) xml += '    <lastmod>' + opts.lastmod + '</lastmod>\n'
  if (opts?.changefreq) xml += '    <changefreq>' + opts.changefreq + '</changefreq>\n'
  if (opts?.priority) xml += '    <priority>' + opts.priority + '</priority>\n'
  xml += '  </url>\n'
  return xml
}

export async function GET() {
  const staticPaths = getStaticSitemapPaths()
  staticPaths.push({ loc: '/blogs', priority: '0.9', changefreq: 'daily' })

  let xml = '<?xml version="1.0" encoding="UTF-8"?>\n'
  xml += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n'
  for (const p of staticPaths) {
    xml += urlEntry(SITE_URL + p.loc, {
      changefreq: p.changefreq,
      priority: p.priority,
      lastmod: new Date().toISOString().slice(0, 10),
    })
  }
  xml += '</urlset>'

  return new Response(xml, {
    headers: { 'Content-Type': 'application/xml', 'Cache-Control': 'public, max-age=3600' },
  })
}
