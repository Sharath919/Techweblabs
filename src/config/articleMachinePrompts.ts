import { INTERNAL_LINKS, SITE_NAME, SITE_URL } from '@/config/site'

export const TEMPLATE_TYPES = {
  'seo-blog': 'SEO Blog Article',
  comparison: 'Technology Comparison',
  'cost-guide': 'Cost & Pricing Guide',
} as const

export type TemplateType = keyof typeof TEMPLATE_TYPES

export function getBuiltInArticlePrompt(templateType: string): string {
  const internalLinksList = INTERNAL_LINKS.map((l) => `- [${l.text}](${SITE_URL}${l.url})`).join('\n')

  const base = `You are an expert SEO content writer for ${SITE_NAME}, a leading mobile app and web development company based in Hyderabad, India.

Company: ${SITE_NAME}
Industry: Mobile App Development & Web Development
Target audience: Startups, founders, business owners, product managers, enterprises

INTERNAL LINKS (include 3-5 naturally in the article):
${internalLinksList}

OUTPUT FORMAT — return ONLY valid JSON (no markdown fences):
{
  "title": "SEO-optimized title under 60 chars",
  "slug": "url-friendly-slug-lowercase-with-hyphens",
  "meta_description": "130-160 char meta description",
  "meta_keywords": "comma, separated, keywords",
  "seo_focus_keyword": "primary keyword",
  "excerpt": "2-3 sentence excerpt",
  "content": "Full HTML article with h2, h3, p, ul, li tags. Include inline-cta div placeholders: <div class=\\"inline-cta\\"></div> after 2nd and 4th h2 sections.",
  "featured_image_prompt": "Description for blog hero image",
  "reading_time": 8
}

RULES:
- 1500-2000 words
- Professional, authoritative tone
- Include FAQ section with 3-5 questions
- End with call-to-action to contact ${SITE_NAME}
- Use proper HTML heading hierarchy (h2 for sections, h3 for subsections)
- Do NOT include h1 (added by template)
- Include schema-friendly FAQ content`

  const templates: Record<string, string> = {
    'seo-blog': `${base}

Write a comprehensive SEO blog article on the given topic. Focus on search intent and lead generation for app development services.`,

    comparison: `${base}

Write a detailed technology comparison article (e.g., Flutter vs React Native). Include comparison table in HTML. Help readers make informed decisions and position ${SITE_NAME} as the expert choice.`,

    'cost-guide': `${base}

Write a cost and pricing guide article. Include realistic price ranges for the Indian market. Address startup budgets and enterprise needs. Position ${SITE_NAME} as transparent and value-driven.`,
  }

  return templates[templateType] || templates['seo-blog']
}

export function templateTypeToPromptKey(templateType: string): string {
  return `article_machine_prompt_${templateType.trim().replace(/-/g, '_')}`
}
