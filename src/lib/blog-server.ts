import { createServerSupabase } from '@/lib/supabase'
import type { BlogPost } from '@/types/blog'

export const BLOG_PAGE_SIZE = 10

export async function getPublishedBlogSlugs(): Promise<string[]> {
  const supabase = createServerSupabase()
  if (!supabase) return []
  const { data, error } = await supabase
    .from('blog_posts')
    .select('slug')
    .eq('status', 'published')
    .limit(5000)
  if (error) {
    console.error('[blog-server] slugs:', error.message)
    return []
  }
  return (data ?? []).map((r) => r.slug).filter(Boolean)
}

export async function getPublishedBlogBySlug(slug: string): Promise<BlogPost | null> {
  const supabase = createServerSupabase()
  if (!supabase) return null
  const { data, error } = await supabase
    .from('blog_posts')
    .select('*')
    .eq('slug', slug)
    .eq('status', 'published')
    .maybeSingle()
  if (error) {
    console.error('[blog-server] getBySlug:', error.message)
    return null
  }
  return data as BlogPost | null
}

export async function incrementBlogViews(id: string): Promise<void> {
  const supabase = createServerSupabase()
  if (!supabase) return
  const { data } = await supabase.from('blog_posts').select('views').eq('id', id).maybeSingle()
  const views = (data?.views ?? 0) + 1
  await supabase.from('blog_posts').update({ views }).eq('id', id)
}

export async function queryPublishedBlogs(options?: {
  page?: number
  pageSize?: number
  category?: string | null
}): Promise<{
  posts: BlogPost[]
  total: number
  page: number
  pageSize: number
  totalPages: number
}> {
  const supabase = createServerSupabase()
  const pageSize = options?.pageSize ?? BLOG_PAGE_SIZE
  const page = Math.max(1, options?.page ?? 1)

  if (!supabase) {
    return { posts: [], total: 0, page, pageSize, totalPages: 0 }
  }

  const from = (page - 1) * pageSize
  const to = from + pageSize - 1

  const { data, error, count } = await supabase
    .from('blog_posts')
    .select('*', { count: 'exact' })
    .eq('status', 'published')
    .order('published_at', { ascending: false })
    .range(from, to)

  const total = count ?? 0
  const totalPages = total > 0 ? Math.ceil(total / pageSize) : 0

  if (error) {
    console.error('[blog-server] query:', error.message)
    return { posts: [], total: 0, page, pageSize, totalPages: 0 }
  }

  return {
    posts: (data ?? []) as BlogPost[],
    total,
    page,
    pageSize,
    totalPages,
  }
}

export async function getRecentBlogPosts(limit = 5): Promise<BlogPost[]> {
  const supabase = createServerSupabase()
  if (!supabase) return []
  const { data } = await supabase
    .from('blog_posts')
    .select('id, slug, title, meta_description, excerpt, featured_image, published_at')
    .eq('status', 'published')
    .order('published_at', { ascending: false })
    .limit(limit)
  return (data ?? []) as BlogPost[]
}
