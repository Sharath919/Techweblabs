import { NextResponse } from 'next/server'
import type { NextRequest } from 'next/server'
import { getAllStaticSlugs } from '@/config/static-slug-manifest'

const STATIC_SLUGS = new Set(getAllStaticSlugs())

/** Reserved paths handled by Next.js or static assets — never use dynamic [slug] route */
const RESERVED = new Set([
  'admin',
  'api',
  'blogs',
  'widgets',
  'static',
  'css',
  'js',
  'images',
  'fonts',
  'favicons',
  'homepage',
  'components',
  '_next',
  'sitemap.xml',
  'sitemap-pages.xml',
  'sitemap-posts.xml',
  'sitemap.xsl',
  'robots.txt',
])

export function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl
  const segment = pathname.split('/').filter(Boolean)[0] || ''

  if (RESERVED.has(segment)) {
    return NextResponse.next()
  }

  if (pathname === '/') {
    const url = request.nextUrl.clone()
    url.pathname = '/static/index.html'
    return NextResponse.rewrite(url)
  }

  if (STATIC_SLUGS.has(segment)) {
    const url = request.nextUrl.clone()
    url.pathname = `/static/${segment}.html`
    return NextResponse.rewrite(url)
  }

  return NextResponse.next()
}

export const config = {
  matcher: ['/((?!_next/static|_next/image|favicon.ico|widgets/persona.js).*)'],
}
