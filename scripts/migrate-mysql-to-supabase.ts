/**
 * One-time MySQL → Supabase blog migration.
 * Requires: MYSQL_* env vars + Supabase service role key.
 */
import { createClient } from '@supabase/supabase-js'

async function main() {
  const supabaseUrl = (process.env.SUPABASE_URL || process.env.NEXT_PUBLIC_SUPABASE_URL || '').trim()
  const serviceKey = (process.env.SUPABASE_SERVICE_ROLE_KEY || '').trim()
  if (!supabaseUrl || !serviceKey) {
    console.error('Set SUPABASE_URL and SUPABASE_SERVICE_ROLE_KEY')
    process.exit(1)
  }

  const supabase = createClient(supabaseUrl, serviceKey)

  let connection: mysql.Connection | null = null
  try {
    const mysql = await import('mysql2/promise')
    connection = await mysql.createConnection({
      host: process.env.MYSQL_HOST || 'localhost',
      user: process.env.MYSQL_USER || 'techweb_user',
      password: process.env.MYSQL_PASSWORD || '',
      database: process.env.MYSQL_DATABASE || 'techweb_blog',
    })
  } catch (err) {
    console.warn('MySQL connection failed — skipping migration:', err instanceof Error ? err.message : err)
    console.log('You can import posts manually via Supabase dashboard or run this script when MySQL is available.')
    process.exit(0)
  }

  const [posts] = await connection.execute(
    'SELECT * FROM blog_posts ORDER BY id ASC',
  ) as [Array<Record<string, unknown>>, unknown]

  console.log(`Found ${posts.length} posts in MySQL`)

  let migrated = 0
  let skipped = 0

  for (const post of posts) {
    const slug = String(post.slug)
    const { data: existing } = await supabase
      .from('blog_posts')
      .select('id')
      .eq('slug', slug)
      .maybeSingle()

    if (existing) {
      skipped++
      continue
    }

    const { error } = await supabase.from('blog_posts').insert({
      legacy_id: post.id as number,
      slug,
      title: String(post.title),
      meta_title: post.meta_title ? String(post.meta_title) : null,
      meta_description: post.meta_description ? String(post.meta_description) : null,
      meta_keywords: post.meta_keywords ? String(post.meta_keywords) : null,
      excerpt: post.excerpt ? String(post.excerpt) : null,
      content: String(post.content || ''),
      featured_image: post.featured_image ? String(post.featured_image) : null,
      og_image: post.og_image ? String(post.og_image) : null,
      author_name: 'TechWebLabs',
      status: String(post.status || 'draft'),
      published_at: post.published_at ? new Date(String(post.published_at)).toISOString() : null,
      views: Number(post.views ?? 0),
      seo_focus_keyword: post.seo_focus_keyword ? String(post.seo_focus_keyword) : null,
      schema_type: post.schema_type ? String(post.schema_type) : 'BlogPosting',
      reading_time: post.reading_time ? Number(post.reading_time) : null,
      created_at: post.created_at ? new Date(String(post.created_at)).toISOString() : new Date().toISOString(),
      updated_at: post.updated_at ? new Date(String(post.updated_at)).toISOString() : new Date().toISOString(),
    })

    if (error) {
      console.error(`Failed to migrate ${post.slug}:`, error.message)
    } else {
      migrated++
    }
  }

  await connection.end()
  console.log(`Migration complete: ${migrated} migrated, ${skipped} skipped (already exist)`)
}

main().catch((err) => {
  console.error(err)
  process.exit(1)
})
