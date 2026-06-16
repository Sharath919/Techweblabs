import Link from 'next/link'
import { notFound } from 'next/navigation'
import SiteHeader from '@/components/SiteHeader'
import SiteFooter from '@/components/SiteFooter'
import {
  getPublishedBlogBySlug,
  getPublishedBlogSlugs,
  incrementBlogViews,
} from '@/lib/blog-server'
import { injectInlineCtas } from '@/lib/utils'
import { SITE_URL } from '@/config/site'

export const revalidate = 3600

export async function generateStaticParams() {
  const slugs = await getPublishedBlogSlugs()
  return slugs.map((slug) => ({ slug }))
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params
  const post = await getPublishedBlogBySlug(slug)
  if (!post) return { title: 'Not Found' }

  const title = post.meta_title || post.title
  const description = post.meta_description || post.excerpt || ''
  const ogImage = post.og_image || post.featured_image || `${SITE_URL}/images/mobile-app-development.jpg`

  return {
    title,
    description,
    alternates: { canonical: `${SITE_URL}/blogs/${slug}` },
    openGraph: {
      title: post.title,
      description,
      url: `${SITE_URL}/blogs/${slug}`,
      type: 'article',
      images: [{ url: ogImage }],
    },
  }
}

export default async function BlogPostPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params
  const post = await getPublishedBlogBySlug(slug)
  if (!post) notFound()

  await incrementBlogViews(post.id)

  const content = injectInlineCtas(post.content)
  const ogImage = post.og_image || post.featured_image || `${SITE_URL}/images/mobile-app-development.jpg`
  const canonical = `${SITE_URL}/blogs/${slug}`

  const schema = {
    '@context': 'https://schema.org',
    '@type': post.schema_type || 'BlogPosting',
    headline: post.title,
    description: post.meta_description || post.excerpt,
    image: ogImage,
    datePublished: post.published_at,
    dateModified: post.updated_at,
    author: {
      '@type': 'Person',
      name: post.author_name || 'TechWebLabs',
    },
    publisher: {
      '@type': 'Organization',
      name: 'TechWebLabs',
      logo: { '@type': 'ImageObject', url: `${SITE_URL}/images/logo.png` },
    },
    mainEntityOfPage: { '@type': 'WebPage', '@id': canonical },
  }

  return (
    <>
      <SiteHeader />
      <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }} />
      <article style={{ paddingTop: 100 }}>
        <div className="container py-5">
          <div className="row justify-content-center">
            <div className="col-lg-8">
              <nav aria-label="breadcrumb" className="mb-3">
                <Link href="/blogs">← Back to Blog</Link>
              </nav>
              <h1 className="mb-3">{post.title}</h1>
              <p className="text-muted mb-4">
                {post.published_at &&
                  new Date(post.published_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                  })}
                {post.reading_time ? ` · ${post.reading_time} min read` : ''}
                {post.author_name ? ` · By ${post.author_name}` : ''}
              </p>
              {post.featured_image && (
                <img
                  src={post.featured_image}
                  alt={post.title}
                  className="w-100 mb-4"
                  style={{ borderRadius: 12, maxHeight: 400, objectFit: 'cover' }}
                />
              )}
              <div className="blog-post-content" dangerouslySetInnerHTML={{ __html: content }} />
              <div className="blog-inline-cta mt-5">
                <h4>Ready to build your app?</h4>
                <p>Get a free consultation with TechWebLabs — trusted by startups worldwide.</p>
                <Link href="/contact" className="btn-main bg-btn lnk">
                  Get Free Consultation →
                </Link>
              </div>
            </div>
          </div>
        </div>
      </article>
      <SiteFooter />
    </>
  )
}
