export interface BlogPost {
  id: string
  slug: string
  title: string
  meta_title: string | null
  meta_description: string | null
  meta_keywords: string | null
  excerpt: string | null
  content: string
  featured_image: string | null
  og_image: string | null
  author_id: string | null
  author_name: string | null
  status: 'draft' | 'published' | 'archived'
  published_at: string | null
  views: number
  seo_focus_keyword: string | null
  schema_type: string | null
  reading_time: number | null
  created_at: string
  updated_at: string
}

export interface PublishingScheduleItem {
  id: string
  card_name: string
  template_type: string
  scheduled_date: string
  status: 'pending' | 'processing' | 'done' | 'failed'
  post_id: string | null
  error_text: string | null
  created_at: string
  updated_at: string
}

export interface LeadCapture {
  id: string
  name: string | null
  email: string | null
  phone: string | null
  company: string | null
  requirement: string | null
  budget: string | null
  timeline: string | null
  source_page: string
  qualification_score: number | null
  status: string
  created_at: string
}

export interface SitePage {
  id: string
  slug: string
  page_type: 'pillar' | 'service' | 'clone'
  title: string
  meta_title: string | null
  meta_description: string | null
  meta_keywords: string | null
  target_keyword: string | null
  hero_title: string | null
  hero_subtitle: string | null
  content_json: Record<string, unknown>
  status: 'draft' | 'published'
}
