export type BlogPostFormData = {
  title: string
  slug: string
  meta_title: string
  meta_description: string
  meta_keywords: string
  seo_focus_keyword: string
  excerpt: string
  content: string
  featured_image: string
  status: 'draft' | 'published' | 'archived'
}

export type AdminBlogPostRow = {
  id: string
  slug: string
  title: string
  status: string
  published_at: string | null
  updated_at: string
  created_at: string
  views: number
  author_name: string | null
  excerpt: string | null
}

export type AdminBlogPostSort = 'newest' | 'oldest' | 'title-asc' | 'title-desc' | 'updated'
export type AdminBlogPostSource = 'all' | 'generated' | 'manual'

export type AdminBlogPostQuery = {
  search?: string
  status?: string
  source?: AdminBlogPostSource
  sort?: AdminBlogPostSort
  page?: number
  pageSize?: number
}
