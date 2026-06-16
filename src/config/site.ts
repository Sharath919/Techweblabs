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

/** Matches vercel.json publishing crons (UTC, minute 0) — same pattern as Limansa. */
export const PUBLISHING_CRON_UTC_HOURS = [1, 4, 7, 10, 13, 16, 19] as const

export const PUBLISHING_BATCH_SIZE = 3

export const PUBLISHING_DAILY_TARGET = 10

export const PUBLISHING_CRON_RUNS_PER_DAY = PUBLISHING_CRON_UTC_HOURS.length

/** 7 crons × 3 articles = up to 21/day on Vercel Hobby (Limansa uses same pattern for ~20/day). */
export const PUBLISHING_THEORETICAL_DAILY_MAX =
  PUBLISHING_BATCH_SIZE * PUBLISHING_CRON_RUNS_PER_DAY

export const INTERNAL_LINKS = [
  { text: 'Food Delivery App Development', url: '/food-delivery-app-development' },
  { text: 'Grocery App Development', url: '/grocery-app-development' },
  { text: 'Taxi App Development', url: '/on-demand-taxi-booking-app-development' },
  { text: 'Flutter App Development', url: '/android-app-development' },
  { text: 'Custom App Development', url: '/custom-app-development' },
  { text: 'Contact Us', url: '/contact' },
  { text: 'About TechWebLabs', url: '/about' },
]
