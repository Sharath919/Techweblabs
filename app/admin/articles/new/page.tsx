'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import BlogPostForm from '@/components/admin/BlogPostForm'
import type { BlogPostFormData } from '@/types/blog-admin'
import { createBlogPost } from '@/utils/blog-posts-admin'

export default function NewArticlePage() {
  const router = useRouter()
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  async function handleSubmit(data: BlogPostFormData, _action: 'save-draft' | 'publish') {
    setLoading(true)
    setError(null)
    const result = await createBlogPost(data)
    setLoading(false)
    if ('error' in result) {
      setError(result.error)
      return
    }
    router.push(`/admin/articles/${result.id}/edit`)
  }

  return (
    <BlogPostForm
      onSubmit={handleSubmit}
      isLoading={loading}
      submitError={error}
    />
  )
}
