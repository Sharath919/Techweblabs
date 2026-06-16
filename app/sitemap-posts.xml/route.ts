import { createServerSupabase } from '@/lib/supabase'
import { SITE_URL } from '@/config/site'

export const revalidate = 3600

function urlEntry(
  loc: string,
  opts?: { lastmod?: string; changefreq?: string; priority?: string; image?: string },
) {
  let xml = '  <url>\n    <loc>' + loc + '</loc>\n'
  if (opts?.lastmod) xml += '    <lastmod>' + opts.lastmod + '</lastmod>\n'
  if (opts?.changefreq) xml += '    <changefreq>' + opts.changefreq + '</changefreq>\n'
  if (opts?.priority) xml += '    <priority>' + opts.priority + '</priority>\n'
  if (opts?.image) {
    xml += '    <image:image xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'
    xml += '<image:loc>' + opts.image + '</image:loc></image:image>\n'
  }
  xml += '  </url>\n'
  return xml
}

export async function GET() {
  let xml =
    '<?xml version="1.0" encoding="UTF-8"?>\n' +
    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">\n'

  const supabase = createServerSupabase()
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
      xml += urlEntry(SITE_URL + '/blogs/' + post.slug, {
        lastmod,
        changefreq: 'weekly',
        priority: '0.7',
        image: img ? (img.startsWith('http') ? img : SITE_URL + img) : undefined,
      })
    }
  }

  xml += '</urlset>'

  return new Response(xml, {
    headers: { 'Content-Type': 'application/xml', 'Cache-Control': 'public, max-age=3600' },
  })
}
