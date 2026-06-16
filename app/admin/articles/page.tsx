import Link from 'next/link'
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
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h1 className="h3 mb-0">Articles</h1>
        <Link href="/admin/schedule" className="btn btn-primary btn-sm">Schedule New</Link>
      </div>
      <div className="admin-card">
        <table className="table table-sm">
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
                <td><span className="badge bg-secondary">{post.status}</span></td>
                <td>{post.views}</td>
                <td>{post.published_at ? new Date(post.published_at).toLocaleDateString() : '—'}</td>
                <td>
                  {post.status === 'published' && (
                    <a href={`/blogs/${post.slug}`} target="_blank" rel="noopener noreferrer">View</a>
                  )}
                </td>
              </tr>
            ))}
            {posts.length === 0 && (
              <tr><td colSpan={5} className="text-muted">No articles yet</td></tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  )
}
