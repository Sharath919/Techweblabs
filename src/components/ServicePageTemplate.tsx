import Link from 'next/link'
import SiteHeader from '@/components/SiteHeader'
import SiteFooter from '@/components/SiteFooter'
import { getRecentBlogPosts } from '@/lib/blog-server'
import { SITE_URL } from '@/config/site'
import type { SitePage } from '@/types/blog'

export interface ServicePageProps {
  page: SitePage
}

export default async function ServicePageTemplate({ page }: ServicePageProps) {
  const recentPosts = await getRecentBlogPosts(3)
  const content = page.content_json as {
    sections?: Array<{ title: string; body: string }>
    faqs?: Array<{ question: string; answer: string }>
    features?: string[]
  }

  const canonical = `${SITE_URL}/${page.slug}`

  return (
    <>
      <SiteHeader />
      <section className="service-hero">
        <div className="container">
          <h1>{page.hero_title || page.title}</h1>
          {page.hero_subtitle && <p className="lead">{page.hero_subtitle}</p>}
          <Link href="/contact" className="btn-main bg-btn lnk mt-3">Get Free Quote</Link>
        </div>
      </section>
      <section className="service-section">
        <div className="container">
          <div className="row">
            <div className="col-lg-8">
              {(content.sections ?? []).map((section, i) => (
                <div key={i} className="mb-5">
                  <h2>{section.title}</h2>
                  <div dangerouslySetInnerHTML={{ __html: section.body }} />
                </div>
              ))}
              {content.features && content.features.length > 0 && (
                <ul className="list-unstyled">
                  {content.features.map((f, i) => (
                    <li key={i} className="mb-2"><i className="fas fa-check text-primary me-2" />{f}</li>
                  ))}
                </ul>
              )}
              {(content.faqs ?? []).length > 0 && (
                <div className="mt-5">
                  <h2>Frequently Asked Questions</h2>
                  {(content.faqs ?? []).map((faq, i) => (
                    <div key={i} className="mb-3">
                      <h3 style={{ fontSize: '1.1rem' }}>{faq.question}</h3>
                      <p>{faq.answer}</p>
                    </div>
                  ))}
                </div>
              )}
            </div>
            <div className="col-lg-4">
              <div className="admin-card">
                <h3 style={{ fontSize: '1.1rem' }}>Get a Free Consultation</h3>
                <p className="small">Talk to our experts about your {page.title.toLowerCase()} project.</p>
                <Link href="/contact" className="btn btn-primary w-100">Contact Us</Link>
              </div>
              {recentPosts.length > 0 && (
                <div className="admin-card mt-3">
                  <h3 style={{ fontSize: '1.1rem' }}>Related Articles</h3>
                  <ul className="list-unstyled small">
                    {recentPosts.map((post) => (
                      <li key={post.id} className="mb-2">
                        <Link href={`/blogs/${post.slug}`}>{post.title}</Link>
                      </li>
                    ))}
                  </ul>
                </div>
              )}
            </div>
          </div>
        </div>
      </section>
      <SiteFooter />
      <script type="application/ld+json" dangerouslySetInnerHTML={{
        __html: JSON.stringify({
          '@context': 'https://schema.org',
          '@type': 'Service',
          name: page.title,
          description: page.meta_description,
          url: canonical,
          provider: { '@type': 'Organization', name: 'TechWebLabs', url: SITE_URL },
        }),
      }} />
    </>
  )
}
