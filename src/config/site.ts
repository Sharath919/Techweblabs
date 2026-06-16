const DEFAULT_SITE_URL = 'https://techweblabs.com'

function normalizeSiteOrigin(raw: string | undefined): string {
  const trimmed = raw?.trim().replace(/\/$/, '')
  return trimmed || DEFAULT_SITE_URL
}

export function getSiteUrl(): string {
  return normalizeSiteOrigin(
    process.env.SITE_URL ??
      process.env.NEXT_PUBLIC_SITE_URL ??
      (typeof window !== 'undefined' ? (window as unknown as { __TWL_ENV__?: { siteUrl?: string } }).__TWL_ENV__?.siteUrl : undefined),
  )
}

export const SITE_URL = normalizeSiteOrigin(
  process.env.SITE_URL ?? process.env.NEXT_PUBLIC_SITE_URL,
)

export const SITE_NAME = 'TechWebLabs'

export function siteUrl(path = ''): string {
  if (!path || path === '/') return `${getSiteUrl()}/`
  const normalized = path.startsWith('/') ? path : `/${path}`
  return `${getSiteUrl()}${normalized}`
}

export function canonicalFromPathname(pathname: string): string {
  const path = pathname || '/'
  const trimmed = path.length > 1 && path.endsWith('/') ? path.slice(0, -1) : path
  return siteUrl(trimmed || '/')
}

export const PUBLISHING_BATCH_SIZE = 3

export const INTERNAL_LINKS = [
  { text: 'Food Delivery App Development', url: '/food-delivery-app-development' },
  { text: 'Grocery App Development', url: '/grocery-app-development' },
  { text: 'Taxi App Development', url: '/on-demand-taxi-booking-app-development' },
  { text: 'Flutter App Development', url: '/android-app-development' },
  { text: 'Custom App Development', url: '/custom-app-development' },
  { text: 'Contact Us', url: '/contact' },
  { text: 'About TechWebLabs', url: '/about' },
]
