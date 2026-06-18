import { clsx, type ClassValue } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function slugify(text: string): string {
  return text
    .toLowerCase()
    .trim()
    .replace(/[^\w\s-]/g, '')
    .replace(/[\s_-]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

export function estimateReadingTime(html: string): number {
  const text = html.replace(/<[^>]+>/g, ' ')
  const words = text.split(/\s+/).filter(Boolean).length
  return Math.max(1, Math.ceil(words / 200))
}

export function stripHtml(html: string): string {
  return html.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim()
}

export function parseArticleJson(raw: string): Record<string, unknown> | null {
  const trimmed = raw.trim()
  const jsonMatch = trimmed.match(/\{[\s\S]*\}/)
  if (!jsonMatch) return null
  try {
    return JSON.parse(jsonMatch[0]) as Record<string, unknown>
  } catch {
    return null
  }
}

export function injectInlineCtas(content: string): string {
  let count = 0
  return content.replace(/<div class="inline-cta"><\/div>/g, () => {
    count++
    return `<div class="inline-cta" data-cta-slot="${count}">
      <div class="blog-inline-cta">
        <h4>Ready to build your app?</h4>
        <p>Talk to TechWebLabs — trusted by startups worldwide for Flutter, React Native, and custom app development.</p>
        <a href="/contact" class="btn-main bg-btn lnk">Get Free Consultation →</a>
      </div>
    </div>`
  })
}
