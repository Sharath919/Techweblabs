'use client'

import { useEffect, useRef, useState } from 'react'
import { ChevronDown, ChevronRight, Sparkles } from 'lucide-react'
import { TEMPLATE_TYPES } from '@/config/articleMachinePrompts'
import { SITE_URL } from '@/config/site'
import { slugify } from '@/lib/utils'
import type { BlogPostFormData } from '@/types/blog-admin'
import { parseClaudeImportJson } from '@/utils/claudeImportJson'
import {
  isSlugAvailable,
  suggestAvailableSlug,
} from '@/utils/blog-posts-admin'
import { createBrowserSupabase } from '@/lib/supabase'

const EMPTY: BlogPostFormData = {
  title: '',
  slug: '',
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  seo_focus_keyword: '',
  excerpt: '',
  content: '',
  featured_image: '',
  status: 'draft',
}

type Props = {
  postId?: string
  initialData?: Partial<BlogPostFormData>
  onSubmit: (data: BlogPostFormData, action: 'save-draft' | 'publish') => Promise<void>
  isLoading?: boolean
  submitError?: string | null
}

export default function BlogPostForm({
  postId,
  initialData,
  onSubmit,
  isLoading,
  submitError,
}: Props) {
  const [form, setForm] = useState<BlogPostFormData>({ ...EMPTY, ...initialData })
  const [slugManual, setSlugManual] = useState(!!initialData?.slug)
  const [importOpen, setImportOpen] = useState(false)
  const [importJson, setImportJson] = useState('')
  const [importMsg, setImportMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null)
  const [slugStatus, setSlugStatus] = useState<'idle' | 'checking' | 'ok' | 'taken'>('idle')
  const [generating, setGenerating] = useState(false)
  const [templateType, setTemplateType] = useState('seo-blog')
  const contentRef = useRef<HTMLTextAreaElement>(null)

  useEffect(() => {
    if (initialData) setForm({ ...EMPTY, ...initialData })
  }, [initialData])

  useEffect(() => {
    if (!slugManual && form.title) {
      setForm((f) => ({ ...f, slug: slugify(form.title) }))
    }
  }, [form.title, slugManual])

  useEffect(() => {
    const slug = form.slug.trim()
    if (!slug) {
      setSlugStatus('idle')
      return
    }
    setSlugStatus('checking')
    const t = window.setTimeout(async () => {
      const ok = await isSlugAvailable(slug, postId)
      setSlugStatus(ok ? 'ok' : 'taken')
    }, 400)
    return () => window.clearTimeout(t)
  }, [form.slug, postId])

  function setField<K extends keyof BlogPostFormData>(key: K, value: BlogPostFormData[K]) {
    setForm((f) => ({ ...f, [key]: value }))
  }

  function runImport() {
    const result = parseClaudeImportJson(importJson)
    if (result.kind === 'invalid') {
      setImportMsg({ type: 'err', text: 'Invalid JSON — paste Claude output with ```json fences or raw JSON.' })
      return
    }
    if (result.kind === 'empty') {
      setImportMsg({ type: 'err', text: 'No recognizable fields in JSON.' })
      return
    }
    setForm((f) => ({
      ...f,
      ...result.fields,
      meta_title: result.fields.meta_title ?? f.meta_title,
    }))
    if (result.fields.slug) setSlugManual(true)
    setImportMsg({ type: 'ok', text: `Imported ${Object.keys(result.fields).length} fields from Claude JSON.` })
  }

  async function generateAndPublish() {
    const topic = form.title.trim() || form.seo_focus_keyword.trim()
    if (!topic) {
      setImportMsg({ type: 'err', text: 'Enter a title or focus keyword first.' })
      return
    }
    setGenerating(true)
    setImportMsg(null)
    try {
      const supabase = createBrowserSupabase()
      const { data: { session } } = await supabase.auth.getSession()
      const res = await fetch('/api/generate-article', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${session?.access_token || ''}`,
        },
        body: JSON.stringify({
          topic,
          template_type: templateType,
          keywords: form.seo_focus_keyword || form.meta_keywords,
        }),
      })
      const body = await res.json()
      if (!res.ok) throw new Error(body.error || 'Generation failed')
      window.location.href = `/admin/articles/${body.post_id}/edit`
    } catch (err) {
      setImportMsg({
        type: 'err',
        text: err instanceof Error ? err.message : 'Generation failed',
      })
    } finally {
      setGenerating(false)
    }
  }

  async function fixSlug() {
    const next = await suggestAvailableSlug(form.slug || form.title, postId)
    setForm((f) => ({ ...f, slug: next }))
    setSlugManual(true)
  }

  const slugTaken = slugStatus === 'taken'

  return (
    <div className="admin-stack">
      <section className="admin-panel">
        <button
          type="button"
          className="admin-import-toggle"
          onClick={() => setImportOpen((o) => !o)}
        >
          {importOpen ? <ChevronDown size={16} /> : <ChevronRight size={16} />}
          Import from Claude (paste JSON)
        </button>
        {importOpen && (
          <div className="admin-panel__body admin-import-body">
            <p className="admin-panel__desc">
              Paste Claude&apos;s JSON response. Fields map to title, slug, meta, excerpt, and HTML content.
            </p>
            <textarea
              className="admin-textarea"
              rows={8}
              value={importJson}
              onChange={(e) => setImportJson(e.target.value)}
              placeholder='{"title":"...","slug":"...","content":"<p>...</p>"}'
            />
            <div className="admin-form-actions">
              <button type="button" className="admin-btn admin-btn--ghost admin-btn--sm" onClick={runImport}>
                Import Fields
              </button>
            </div>
            {importMsg && (
              <p className={`admin-message${importMsg.type === 'err' ? ' admin-message--error' : ''}`}>
                {importMsg.text}
              </p>
            )}
          </div>
        )}
      </section>

      <section className="admin-panel">
        <header className="admin-panel__header">
          <div>
            <h2 className="admin-panel__title">One-click generate & publish</h2>
            <p className="admin-panel__desc">
              Uses Article Machine + Claude to write, publish, and save to Supabase (same as Schedule → Generate Now).
            </p>
          </div>
        </header>
        <div className="admin-panel__body">
          <div className="admin-form-grid admin-form-grid--2">
            <div>
              <label className="admin-label">Template</label>
              <select
                className="admin-select"
                value={templateType}
                onChange={(e) => setTemplateType(e.target.value)}
              >
                {Object.entries(TEMPLATE_TYPES).map(([k, v]) => (
                  <option key={k} value={k}>
                    {v}
                  </option>
                ))}
              </select>
            </div>
            <div className="admin-form-actions" style={{ alignSelf: 'end' }}>
              <button
                type="button"
                className="admin-btn admin-btn--primary"
                disabled={generating}
                onClick={generateAndPublish}
              >
                <Sparkles size={16} />
                {generating ? 'Generating…' : 'Generate & Publish'}
              </button>
            </div>
          </div>
        </div>
      </section>

      <section className="admin-panel">
        <header className="admin-panel__header">
          <h2 className="admin-panel__title">Article details</h2>
        </header>
        <div className="admin-panel__body admin-stack">
          <div>
            <label className="admin-label">Title</label>
            <input
              className="admin-input"
              value={form.title}
              onChange={(e) => setField('title', e.target.value)}
            />
          </div>
          <div>
            <label className="admin-label">
              Slug{' '}
              {slugStatus === 'checking' && <span className="admin-muted-inline">checking…</span>}
              {slugStatus === 'ok' && <span className="admin-ok-inline">available</span>}
              {slugTaken && (
                <button type="button" className="admin-link-btn" onClick={fixSlug}>
                  slug taken — suggest new
                </button>
              )}
            </label>
            <input
              className="admin-input"
              value={form.slug}
              onChange={(e) => {
                setSlugManual(true)
                setField('slug', e.target.value)
              }}
            />
            {form.slug && (
              <p className="admin-hint">
                Preview: {SITE_URL}/blogs/{form.slug}
              </p>
            )}
          </div>
          <div className="admin-form-grid admin-form-grid--2">
            <div>
              <label className="admin-label">Meta title</label>
              <input
                className="admin-input"
                value={form.meta_title}
                onChange={(e) => setField('meta_title', e.target.value)}
              />
            </div>
            <div>
              <label className="admin-label">Focus keyword</label>
              <input
                className="admin-input"
                value={form.seo_focus_keyword}
                onChange={(e) => setField('seo_focus_keyword', e.target.value)}
              />
            </div>
          </div>
          <div>
            <label className="admin-label">Meta description</label>
            <textarea
              className="admin-textarea"
              rows={2}
              value={form.meta_description}
              onChange={(e) => setField('meta_description', e.target.value)}
            />
          </div>
          <div>
            <label className="admin-label">Excerpt</label>
            <textarea
              className="admin-textarea"
              rows={2}
              value={form.excerpt}
              onChange={(e) => setField('excerpt', e.target.value)}
            />
          </div>
          <div>
            <label className="admin-label">Featured image URL</label>
            <input
              className="admin-input"
              value={form.featured_image}
              onChange={(e) => setField('featured_image', e.target.value)}
              placeholder={`${SITE_URL}/images/mobile-app-development.jpg`}
            />
          </div>
          <div>
            <label className="admin-label">Content (HTML)</label>
            <textarea
              ref={contentRef}
              className="admin-textarea admin-textarea--tall"
              rows={18}
              value={form.content}
              onChange={(e) => setField('content', e.target.value)}
            />
          </div>
        </div>
      </section>

      {submitError && <p className="admin-message admin-message--error">{submitError}</p>}

      <div className="admin-form-actions admin-form-actions--footer">
        <button
          type="button"
          className="admin-btn admin-btn--ghost"
          disabled={isLoading || slugTaken}
          onClick={() => onSubmit({ ...form, status: 'draft' }, 'save-draft')}
        >
          Save Draft
        </button>
        <button
          type="button"
          className="admin-btn admin-btn--primary"
          disabled={isLoading || slugTaken || !form.title.trim() || !form.slug.trim()}
          onClick={() => onSubmit({ ...form, status: 'published' }, 'publish')}
        >
          {isLoading ? 'Saving…' : 'Publish Now'}
        </button>
      </div>
    </div>
  )
}
