/**
 * Generate sitemap XML files into public/
 */
import { writeFileSync, mkdirSync } from 'fs'
import { join } from 'path'
import { createClient } from '@supabase/supabase-js'
import { getStaticSitemapPaths } from '../src/config/static-slug-manifest'

const ROOT = join(import.meta.dirname, '..')
const PUBLIC = join(ROOT, 'public')
const SITE_URL = (process.env.SITE_URL || process.env.NEXT_PUBLIC_SITE_URL || 'https://techweblabs.com').replace(/\/$/, '')

function getSupabase() {
  const url = (process.env.SUPABASE_URL || process.env.NEXT_PUBLIC_SUPABASE_URL || '').trim()
  const key = (process.env.SUPABASE_SERVICE_ROLE_KEY || '').trim()
  if (!url || !key) return null
  return createClient(url, key)
}

function urlEntry(loc: string, opts?: { lastmod?: string; changefreq?: string; priority?: string; images?: string[] }) {
  let xml = '  <url>\n    <loc>' + loc + '</loc>\n'
  if (opts?.lastmod) xml += '    <lastmod>' + opts.lastmod + '</lastmod>\n'
  if (opts?.changefreq) xml += '    <changefreq>' + opts.changefreq + '</changefreq>\n'
  if (opts?.priority) xml += '    <priority>' + opts.priority + '</priority>\n'
  for (const img of opts?.images ?? []) {
    xml += '    <image:image><image:loc>' + img + '</image:loc></image:image>\n'
  }
  xml += '  </url>\n'
  return xml
}

async function main() {
  mkdirSync(PUBLIC, { recursive: true })

  const staticPaths = getStaticSitemapPaths()
  staticPaths.push({ loc: '/blogs', priority: '0.9', changefreq: 'daily' })

  let pagesXml = '<?xml version="1.0" encoding="UTF-8"?>\n'
  pagesXml += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n'
  for (const p of staticPaths) {
    pagesXml += urlEntry(SITE_URL + p.loc, {
      changefreq: p.changefreq,
      priority: p.priority,
      lastmod: new Date().toISOString().slice(0, 10),
    })
  }
  pagesXml += '</urlset>\n'

  let postsXml = '<?xml version="1.0" encoding="UTF-8"?>\n'
  postsXml +=
    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">\n'

  const supabase = getSupabase()
  if (supabase) {
    const { data: posts } = await supabase
      .from('blog_posts')
      .select('slug, updated_at, published_at, featured_image, og_image')
      .eq('status', 'published')
      .order('published_at', { ascending: false })
      .limit(5000)

    for (const post of posts ?? []) {
      const lastmod = (post.updated_at || post.published_at || new Date().toISOString()).slice(0, 10)
      const img = post.og_image || post.featured_image
      postsXml += urlEntry(SITE_URL + '/blogs/' + post.slug, {
        lastmod,
        changefreq: 'weekly',
        priority: '0.7',
        images: img ? [img.startsWith('http') ? img : SITE_URL + img] : undefined,
      })
    }
    console.log('Sitemap posts:', (posts ?? []).length)
  } else {
    console.warn('Supabase not configured — posts sitemap will be empty (runtime route will populate)')
  }

  postsXml += '</urlset>\n'

  const indexXml =
    '<?xml version="1.0" encoding="UTF-8"?>\n' +
    '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n' +
    '  <sitemap><loc>' +
    SITE_URL +
    '/sitemap-pages.xml</loc></sitemap>\n' +
    '  <sitemap><loc>' +
    SITE_URL +
    '/sitemap-posts.xml</loc></sitemap>\n' +
    '</sitemapindex>\n'

  writeFileSync(join(PUBLIC, 'sitemap.xml'), indexXml)
  writeFileSync(join(PUBLIC, 'sitemap-pages.xml'), pagesXml)
  writeFileSync(join(PUBLIC, 'sitemap-posts.xml'), postsXml)
  console.log('Sitemaps written to public/')
}

main().catch((err) => {
  console.error(err)
  process.exit(1)
})
