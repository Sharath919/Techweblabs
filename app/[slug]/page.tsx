import { notFound } from 'next/navigation'
import ServicePageTemplate from '@/components/ServicePageTemplate'
import { createServerSupabase } from '@/lib/supabase'
import { SITE_URL } from '@/config/site'
import type { SitePage } from '@/types/blog'

export const revalidate = 3600

/** Slugs served by Next.js dynamic template (new pages forward). Existing PHP pages use static export. */
export const NEXTJS_DYNAMIC_SLUGS: string[] = []

export async function generateStaticParams() {
  const supabase = createServerSupabase()
  if (!supabase) return []
  const { data } = await supabase
    .from('site_pages')
    .select('slug')
    .eq('status', 'published')
  const dbSlugs = (data ?? []).map((r) => r.slug)
  return [...new Set([...NEXTJS_DYNAMIC_SLUGS, ...dbSlugs])].map((slug) => ({ slug }))
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params
  const page = await getSitePage(slug)
  if (!page) return { title: 'Not Found' }
  return {
    title: page.meta_title || page.title,
    description: page.meta_description || undefined,
    keywords: page.meta_keywords || undefined,
    alternates: { canonical: `${SITE_URL}/${slug}` },
  }
}

async function getSitePage(slug: string): Promise<SitePage | null> {
  const supabase = createServerSupabase()
  if (!supabase) return null
  const { data } = await supabase
    .from('site_pages')
    .select('*')
    .eq('slug', slug)
    .eq('status', 'published')
    .maybeSingle()
  return data as SitePage | null
}

export default async function DynamicServicePage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params
  const page = await getSitePage(slug)
  if (!page) notFound()
  return <ServicePageTemplate page={page} />
}
