import Link from 'next/link'
import SiteHeader from '@/components/SiteHeader'
import SiteFooter from '@/components/SiteFooter'
import { queryPublishedBlogs } from '@/lib/blog-server'
import { SITE_URL } from '@/config/site'

export const revalidate = 3600

export const metadata = {
  title: 'Blog - Mobile App & Web Development Insights',
  description: 'Expert insights on mobile app development, web development, Flutter, React Native, and startup technology from TechWebLabs.',
  alternates: { canonical: `${SITE_URL}/blogs` },
}

export default async function BlogsPage({
  searchParams,
}: {
  searchParams: Promise<{ page?: string }>
}) {
  const params = await searchParams
  const page = Math.max(1, parseInt(params.page || '1', 10) || 1)
  const { posts, totalPages, page: currentPage } = await queryPublishedBlogs({ page })

  return (
    <>
      <SiteHeader />
      <section className="breadcrumb-area" style={{ paddingTop: 120, paddingBottom: 40, background: '#f8f9fa' }}>
        <div className="container">
          <h1>Blog</h1>
          <p>Insights on mobile app development, web development, and technology</p>
        </div>
      </section>
      <section className="py-5">
        <div className="container">
          <div className="row">
            {posts.length === 0 ? (
              <div className="col-12 text-center py-5">
                <p>No blog posts yet. Check back soon!</p>
              </div>
            ) : (
              posts.map((post) => (
                <div key={post.id} className="col-lg-6 mb-4">
                  <article className="admin-card h-100">
                    {post.featured_image && (
                      <img
                        src={post.featured_image}
                        alt={post.title}
                        className="w-100 mb-3"
                        style={{ borderRadius: 8, maxHeight: 200, objectFit: 'cover' }}
                      />
                    )}
                    <h2 style={{ fontSize: '1.25rem' }}>
                      <Link href={`/blogs/${post.slug}`}>{post.title}</Link>
                    </h2>
                    <p className="text-muted small">
                      {post.published_at
                        ? new Date(post.published_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                          })
                        : ''}
                      {post.reading_time ? ` · ${post.reading_time} min read` : ''}
                    </p>
                    <p>{post.excerpt || post.meta_description}</p>
                    <Link href={`/blogs/${post.slug}`} className="btn-main bg-btn lnk btn-sm">
                      Read More →
                    </Link>
                  </article>
                </div>
              ))
            )}
          </div>
          {totalPages > 1 && (
            <nav className="mt-4 d-flex justify-content-center gap-2">
              {currentPage > 1 && (
                <Link href={`/blogs?page=${currentPage - 1}`} className="btn btn-outline-primary btn-sm">
                  ← Previous
                </Link>
              )}
              <span className="align-self-center">
                Page {currentPage} of {totalPages}
              </span>
              {currentPage < totalPages && (
                <Link href={`/blogs?page=${currentPage + 1}`} className="btn btn-outline-primary btn-sm">
                  Next →
                </Link>
              )}
            </nav>
          )}
        </div>
      </section>
      <SiteFooter />
    </>
  )
}
