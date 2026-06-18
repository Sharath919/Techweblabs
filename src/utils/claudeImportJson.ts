import { parseArticleJson } from '@/lib/utils'

export const JSON_IMPORT_FIELD_KEYS = [
  'title',
  'slug',
  'meta_title',
  'meta_description',
  'meta_keywords',
  'seo_focus_keyword',
  'excerpt',
  'content',
  'featured_image',
] as const

export type JsonImportFieldKey = (typeof JSON_IMPORT_FIELD_KEYS)[number]

export type ClaudeImportParseResult =
  | { kind: 'invalid' }
  | { kind: 'empty' }
  | { kind: 'ok'; fields: Partial<Record<JsonImportFieldKey, string>> }

function unwrapJsonText(raw: string): string {
  let text = raw.trim()
  if (!text) return text
  if (text.startsWith('```')) {
    text = text.replace(/^```(?:json)?\s*/i, '').replace(/\s*```$/i, '').trim()
  }
  return text
}

export function parseClaudeImportJson(raw: string): ClaudeImportParseResult {
  const text = unwrapJsonText(raw)
  if (!text) return { kind: 'empty' }

  let parsed: unknown
  try {
    parsed = JSON.parse(text)
  } catch {
    const fallback = parseArticleJson(text)
    if (!fallback) return { kind: 'invalid' }
    parsed = fallback
  }

  if (!parsed || typeof parsed !== 'object' || Array.isArray(parsed)) {
    return { kind: 'invalid' }
  }

  const source = parsed as Record<string, unknown>
  const fields: Partial<Record<JsonImportFieldKey, string>> = {}

  const map: Record<JsonImportFieldKey, string[]> = {
    title: ['title'],
    slug: ['slug'],
    meta_title: ['meta_title', 'seo_title'],
    meta_description: ['meta_description'],
    meta_keywords: ['meta_keywords'],
    seo_focus_keyword: ['seo_focus_keyword', 'focus_keyword'],
    excerpt: ['excerpt'],
    content: ['content', 'content_html'],
    featured_image: ['featured_image', 'hero_image_url', 'og_image'],
  }

  for (const [key, aliases] of Object.entries(map) as [JsonImportFieldKey, string[]][]) {
    for (const alias of aliases) {
      const value = source[alias]
      if (typeof value === 'string' && value.trim()) {
        fields[key] = value.trim()
        break
      }
    }
  }

  if (Object.keys(fields).length === 0) return { kind: 'empty' }
  return { kind: 'ok', fields }
}
