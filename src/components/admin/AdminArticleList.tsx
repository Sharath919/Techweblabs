'use client'

import Link from 'next/link'
import { useCallback, useEffect, useState } from 'react'
import { useRouter, useSearchParams } from 'next/navigation'
import AdminPanel from '@/components/admin/AdminPanel'
import StatusBadge from '@/components/admin/StatusBadge'
import type { AdminBlogPostRow, AdminBlogPostSort } from '@/types/blog-admin'
import {
  deleteBlogPost,
  isGeneratedPost,
  queryAdminBlogPosts,
  updateBlogPost,
} from '@/utils/blog-posts-admin'

function wordCount(excerpt: string | null, title: string): number {
  const text = (excerpt || title).trim()
  return text ? text.split(/\s+/).length : 0
}

export default function AdminArticleList() {
  const router = useRouter()
  const searchParams = useSearchParams()
  const [posts, setPosts] = useState<AdminBlogPostRow[]>([])
  const [total, setTotal] = useState(0)
  const [totalPages, setTotalPages] = useState(0)
  const [loading, setLoading] = useState(true)
  const [message, setMessage] = useState('')

  const page = Math.max(1, parseInt(searchParams.get('page') || '1', 10) || 1)
  const search = searchParams.get('q') || ''
  const status = searchParams.get('status') || 'all'
  const source = (searchParams.get('source') || 'all') as 'all' | 'generated' | 'manual'
  const sort = (searchParams.get('sort') || 'newest') as AdminBlogPostSort

  const [searchInput, setSearchInput] = useState(search)

  const load = useCallback(async () => {
    setLoading(true)
    const result = await queryAdminBlogPosts({
      page,
      search,
      status,
      source,
      sort,
    })
    setPosts(result.posts)
    setTotal(result.total)
    setTotalPages(result.totalPages)
    setLoading(false)
  }, [page, search, status, source, sort])

  useEffect(() => {
    load()
  }, [load])

  useEffect(() => {
    const t = window.setTimeout(() => {
      if (searchInput === search) return
      const params = new URLSearchParams(searchParams.toString())
      if (searchInput) params.set('q', searchInput)
      else params.delete('q')
      params.set('page', '1')
      router.push(`/admin/articles?${params.toString()}`)
    }, 300)
    return () => window.clearTimeout(t)
  }, [searchInput, search, router, searchParams])

  function setParam(key: string, value: string) {
    const params = new URLSearchParams(searchParams.toString())
    if (value && value !== 'all') params.set(key, value)
    else params.delete(key)
    params.set('page', '1')
    router.push(`/admin/articles?${params.toString()}`)
  }

  async function togglePublish(post: AdminBlogPostRow) {
    const next = post.status === 'published' ? 'draft' : 'published'
    const result = await updateBlogPost(post.id, { status: next })
    if ('error' in result) setMessage(result.error)
    else {
      setMessage(next === 'published' ? 'Published' : 'Unpublished')
      load()
    }
  }

  async function removePost(id: string, title: string) {
    if (!window.confirm(`Delete "${title}"? This cannot be undone.`)) return
    const result = await deleteBlogPost(id)
    if ('error' in result) setMessage(result.error)
    else {
      setMessage('Deleted')
      load()
    }
  }

  const from = total === 0 ? 0 : (page - 1) * 20 + 1
  const to = Math.min(page * 20, total)

  return (
    <div className="admin-stack">
      <AdminPanel
        title="Articles"
        description={`Showing ${from}–${to} of ${total} articles`}
        actions={
          <Link href="/admin/articles/new" className="admin-btn admin-btn--primary admin-btn--sm">
            + New Article
          </Link>
        }
      >
        <div className="admin-filters">
          <input
            className="admin-input"
            placeholder="Search title or slug…"
            value={searchInput}
            onChange={(e) => setSearchInput(e.target.value)}
          />
          <select className="admin-select" value={status} onChange={(e) => setParam('status', e.target.value)}>
            <option value="all">All statuses</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
          </select>
          <select className="admin-select" value={source} onChange={(e) => setParam('source', e.target.value)}>
            <option value="all">All sources</option>
            <option value="generated">Generated</option>
            <option value="manual">Manual</option>
          </select>
          <select className="admin-select" value={sort} onChange={(e) => setParam('sort', e.target.value)}>
            <option value="newest">Newest first</option>
            <option value="oldest">Oldest first</option>
            <option value="updated">Recently updated</option>
            <option value="title-asc">Title A–Z</option>
            <option value="title-desc">Title Z–A</option>
          </select>
        </div>
        {message && <p className="admin-message">{message}</p>}
      </AdminPanel>

      {loading ? (
        <p className="admin-message">Loading articles…</p>
      ) : posts.length === 0 ? (
        <AdminPanel title="No articles">
          <p className="admin-panel__desc" style={{ margin: 0 }}>
            Create one manually or run MySQL migration / Generate from Schedule.
          </p>
        </AdminPanel>
      ) : (
        <div className="admin-article-list">
          {posts.map((post) => (
            <article key={post.id} className="admin-article-card">
              <div className="admin-article-card__main">
                <div className="admin-article-card__badges">
                  <StatusBadge status={post.status} />
                  {isGeneratedPost(post.author_name) && (
                    <span className="admin-badge admin-badge--info">Generated</span>
                  )}
                  <span className="admin-badge admin-badge--muted">{post.views} views</span>
                </div>
                <h3 className="admin-article-card__title">{post.title}</h3>
                <p className="admin-article-card__meta">
                  /blogs/{post.slug} · {wordCount(post.excerpt, post.title)} words ·{' '}
                  {post.published_at
                    ? new Date(post.published_at).toLocaleDateString()
                    : new Date(post.created_at).toLocaleDateString()}
                </p>
              </div>
              <div className="admin-article-card__actions">
                <Link href={`/admin/articles/${post.id}/edit`} className="admin-btn admin-btn--ghost admin-btn--sm">
                  Edit
                </Link>
                {post.status === 'published' && (
                  <a
                    href={`/blogs/${post.slug}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="admin-btn admin-btn--ghost admin-btn--sm"
                  >
                    View
                  </a>
                )}
                <button
                  type="button"
                  className="admin-btn admin-btn--ghost admin-btn--sm"
                  onClick={() => togglePublish(post)}
                >
                  {post.status === 'published' ? 'Unpublish' : 'Publish'}
                </button>
                <button
                  type="button"
                  className="admin-btn admin-btn--ghost admin-btn--sm"
                  onClick={() => removePost(post.id, post.title)}
                >
                  Delete
                </button>
              </div>
            </article>
          ))}
        </div>
      )}

      {totalPages > 1 && (
        <div className="admin-pagination">
          {page > 1 && (
            <Link href={`/admin/articles?${new URLSearchParams({ ...Object.fromEntries(searchParams), page: String(page - 1) })}`} className="admin-btn admin-btn--ghost admin-btn--sm">
              ← Previous
            </Link>
          )}
          <span className="admin-message" style={{ margin: 0 }}>
            Page {page} of {totalPages}
          </span>
          {page < totalPages && (
            <Link href={`/admin/articles?${new URLSearchParams({ ...Object.fromEntries(searchParams), page: String(page + 1) })}`} className="admin-btn admin-btn--ghost admin-btn--sm">
              Next →
            </Link>
          )}
        </div>
      )}
    </div>
  )
}
