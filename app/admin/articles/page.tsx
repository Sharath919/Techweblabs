import Link from 'next/link'
import AdminPanel from '@/components/admin/AdminPanel'
import StatusBadge from '@/components/admin/StatusBadge'
import { createServerSupabase } from '@/lib/supabase'

export const dynamic = 'force-dynamic'

export default async function AdminArticlesPage() {
  const supabase = createServerSupabase()
  let posts: Array<{ id: string; slug: string; title: string; status: string; published_at: string | null; views: number }> = []

  if (supabase) {
    const { data } = await supabase
      .from('blog_posts')
      .select('id, slug, title, status, published_at, views')
      .order('created_at', { ascending: false })
      .limit(100)
    posts = data ?? []
  }

  return (
    <AdminPanel
      title="All Articles"
      description="Blog posts stored in Supabase. Published posts appear on /blogs."
      actions={
        <Link href="/admin/schedule" className="admin-btn admin-btn--primary admin-btn--sm">
          Schedule New
        </Link>
      }
    >
      <div className="admin-table-wrap">
        <table className="admin-table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Status</th>
              <th>Views</th>
              <th>Published</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {posts.map((post) => (
              <tr key={post.id}>
                <td>{post.title}</td>
                <td>
                  <StatusBadge status={post.status} />
                </td>
                <td>{post.views}</td>
                <td>{post.published_at ? new Date(post.published_at).toLocaleDateString() : '—'}</td>
                <td>
                  {post.status === 'published' && (
                    <a href={`/blogs/${post.slug}`} target="_blank" rel="noopener noreferrer">
                      View
                    </a>
                  )}
                </td>
              </tr>
            ))}
            {posts.length === 0 && (
              <tr>
                <td colSpan={5} style={{ color: 'var(--admin-muted)' }}>
                  No articles yet — migrate from MySQL or queue in Schedule.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </AdminPanel>
  )
}
