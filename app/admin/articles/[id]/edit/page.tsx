'use client'

import { useEffect, useState } from 'react'
import { useParams, useRouter } from 'next/navigation'
import BlogPostForm from '@/components/admin/BlogPostForm'
import type { BlogPostFormData } from '@/types/blog-admin'
import { getBlogPostById, updateBlogPost } from '@/utils/blog-posts-admin'

export default function EditArticlePage() {
  const params = useParams()
  const router = useRouter()
  const id = String(params.id)
  const [initial, setInitial] = useState<Partial<BlogPostFormData> | null>(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    getBlogPostById(id).then((post) => {
      if (!post) {
        router.replace('/admin/articles')
        return
      }
      setInitial({
        title: post.title,
        slug: post.slug,
        meta_title: post.meta_title || '',
        meta_description: post.meta_description || '',
        meta_keywords: post.meta_keywords || '',
        seo_focus_keyword: post.seo_focus_keyword || '',
        excerpt: post.excerpt || '',
        content: post.content || '',
        featured_image: post.featured_image || '',
        status: post.status,
      })
    })
  }, [id, router])

  async function handleSubmit(data: BlogPostFormData, _action: 'save-draft' | 'publish') {
    setLoading(true)
    setError(null)
    const result = await updateBlogPost(id, data)
    setLoading(false)
    if ('error' in result) {
      setError(result.error)
      return
    }
    router.refresh()
  }

  if (!initial) {
    return <p className="admin-message">Loading article…</p>
  }

  return (
    <BlogPostForm
      postId={id}
      initialData={initial}
      onSubmit={handleSubmit}
      isLoading={loading}
      submitError={error}
    />
  )
}
