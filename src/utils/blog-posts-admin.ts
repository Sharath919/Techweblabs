import { createBrowserSupabase } from '@/lib/supabase'
import { slugify, estimateReadingTime } from '@/lib/utils'
import type {
  AdminBlogPostQuery,
  AdminBlogPostRow,
  AdminBlogPostSort,
  BlogPostFormData,
} from '@/types/blog-admin'

const GENERATED_AUTHORS = new Set(['TechWebLabs', 'Article Machine'])

function emptyToNull(value: string): string | null {
  const trimmed = value.trim()
  return trimmed === '' ? null : trimmed
}

export function formatBlogPostError(message: string): string {
  if (message.includes('blog_posts_slug_key') || message.includes('duplicate key')) {
    return 'This slug is already in use. Change the slug or edit the existing post.'
  }
  return message
}

export async function isSlugAvailable(slug: string, excludeId?: string): Promise<boolean> {
  const normalized = slug.trim()
  if (!normalized) return false
  const supabase = createBrowserSupabase()
  let query = supabase.from('blog_posts').select('id').eq('slug', normalized)
  if (excludeId) query = query.neq('id', excludeId)
  const { data } = await query.maybeSingle()
  return !data
}

export async function suggestAvailableSlug(base: string, excludeId?: string): Promise<string> {
  let candidate = slugify(base) || `article-${Date.now()}`
  for (let i = 2; i < 50; i++) {
    if (await isSlugAvailable(candidate, excludeId)) return candidate
    candidate = `${slugify(base)}-${i}`
  }
  return `${slugify(base)}-${Date.now()}`
}

function formToRow(form: BlogPostFormData, authorId: string, authorName: string) {
  const content = form.content.trim()
  return {
    title: form.title.trim(),
    slug: form.slug.trim(),
    meta_title: emptyToNull(form.meta_title),
    meta_description: emptyToNull(form.meta_description),
    meta_keywords: emptyToNull(form.meta_keywords),
    seo_focus_keyword: emptyToNull(form.seo_focus_keyword),
    excerpt: emptyToNull(form.excerpt),
    content,
    featured_image: emptyToNull(form.featured_image),
    og_image: emptyToNull(form.featured_image),
    reading_time: estimateReadingTime(content),
    status: form.status,
    author_id: authorId,
    author_name: authorName,
    published_at: form.status === 'published' ? new Date().toISOString() : null,
  }
}

export async function createBlogPost(form: BlogPostFormData): Promise<{ id: string } | { error: string }> {
  const supabase = createBrowserSupabase()
  const { data: { user } } = await supabase.auth.getUser()
  if (!user) return { error: 'Not signed in' }

  const row = formToRow(form, user.id, user.email?.split('@')[0] || 'Admin')
  const { data, error } = await supabase.from('blog_posts').insert(row).select('id').single()
  if (error) return { error: formatBlogPostError(error.message) }
  return { id: data.id }
}

export async function updateBlogPost(
  id: string,
  form: Partial<BlogPostFormData>,
): Promise<{ ok: true } | { error: string }> {
  const supabase = createBrowserSupabase()
  const updates: Record<string, unknown> = { updated_at: new Date().toISOString() }

  if (form.title !== undefined) updates.title = form.title.trim()
  if (form.slug !== undefined) updates.slug = form.slug.trim()
  if (form.meta_title !== undefined) updates.meta_title = emptyToNull(form.meta_title)
  if (form.meta_description !== undefined) updates.meta_description = emptyToNull(form.meta_description)
  if (form.meta_keywords !== undefined) updates.meta_keywords = emptyToNull(form.meta_keywords)
  if (form.seo_focus_keyword !== undefined) updates.seo_focus_keyword = emptyToNull(form.seo_focus_keyword)
  if (form.excerpt !== undefined) updates.excerpt = emptyToNull(form.excerpt)
  if (form.featured_image !== undefined) {
    updates.featured_image = emptyToNull(form.featured_image)
    updates.og_image = emptyToNull(form.featured_image)
  }
  if (form.content !== undefined) {
    updates.content = form.content
    updates.reading_time = estimateReadingTime(form.content)
  }
  if (form.status !== undefined) {
    updates.status = form.status
    if (form.status === 'published') {
      updates.published_at = new Date().toISOString()
    }
  }

  const { error } = await supabase.from('blog_posts').update(updates).eq('id', id)
  if (error) return { error: formatBlogPostError(error.message) }
  return { ok: true }
}

export async function deleteBlogPost(id: string): Promise<{ ok: true } | { error: string }> {
  const supabase = createBrowserSupabase()
  const { error } = await supabase.from('blog_posts').delete().eq('id', id)
  if (error) return { error: error.message }
  return { ok: true }
}

export async function getBlogPostById(id: string) {
  const supabase = createBrowserSupabase()
  const { data, error } = await supabase.from('blog_posts').select('*').eq('id', id).maybeSingle()
  if (error || !data) return null
  return data
}

export async function queryAdminBlogPosts(filters: AdminBlogPostQuery = {}): Promise<{
  posts: AdminBlogPostRow[]
  total: number
  page: number
  pageSize: number
  totalPages: number
}> {
  const supabase = createBrowserSupabase()
  const pageSize = filters.pageSize ?? 20
  const page = Math.max(1, filters.page ?? 1)
  const sort = filters.sort ?? 'newest'

  let query = supabase
    .from('blog_posts')
    .select('id, slug, title, status, published_at, updated_at, created_at, views, author_name, excerpt', {
      count: 'exact',
    })

  if (filters.status && filters.status !== 'all') {
    query = query.eq('status', filters.status)
  }

  if (filters.search?.trim()) {
    const q = `%${filters.search.trim()}%`
    query = query.or(`title.ilike.${q},slug.ilike.${q}`)
  }

  switch (sort) {
    case 'oldest':
      query = query.order('created_at', { ascending: true })
      break
    case 'title-asc':
      query = query.order('title', { ascending: true })
      break
    case 'title-desc':
      query = query.order('title', { ascending: false })
      break
    case 'updated':
      query = query.order('updated_at', { ascending: false })
      break
    default:
      query = query.order('created_at', { ascending: false })
  }

  const from = (page - 1) * pageSize
  const to = from + pageSize - 1
  const { data, error, count } = await query.range(from, to)

  if (error) {
    console.error('[blog-posts-admin]', error.message)
    return { posts: [], total: 0, page, pageSize, totalPages: 0 }
  }

  let posts = (data ?? []) as AdminBlogPostRow[]

  if (filters.source === 'generated') {
    posts = posts.filter((p) => GENERATED_AUTHORS.has(p.author_name || ''))
  } else if (filters.source === 'manual') {
    posts = posts.filter((p) => !GENERATED_AUTHORS.has(p.author_name || ''))
  }

  const total = count ?? posts.length
  return {
    posts,
    total,
    page,
    pageSize,
    totalPages: total > 0 ? Math.ceil(total / pageSize) : 0,
  }
}

export function isGeneratedPost(authorName: string | null): boolean {
  return GENERATED_AUTHORS.has(authorName || '')
}
