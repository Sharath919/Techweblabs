import { SITE_URL } from '@/config/site'

export const revalidate = 3600

export async function GET() {
  const xml =
    '<?xml version="1.0" encoding="UTF-8"?>\n' +
    '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n' +
    '  <sitemap><loc>' +
    SITE_URL +
    '/sitemap-pages.xml</loc></sitemap>\n' +
    '  <sitemap><loc>' +
    SITE_URL +
    '/sitemap-posts.xml</loc></sitemap>\n' +
    '</sitemapindex>'

  return new Response(xml, {
    headers: { 'Content-Type': 'application/xml', 'Cache-Control': 'public, max-age=3600' },
  })
}
