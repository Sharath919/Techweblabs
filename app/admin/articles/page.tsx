'use client'

import { Suspense } from 'react'
import AdminArticleList from '@/components/admin/AdminArticleList'

export default function AdminArticlesPage() {
  return (
    <Suspense fallback={<p className="admin-message">Loading articles…</p>}>
      <AdminArticleList />
    </Suspense>
  )
}
