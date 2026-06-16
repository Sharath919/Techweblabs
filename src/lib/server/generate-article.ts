/** TechWebLabs automated blog article pipeline (OpenAI). */

import OpenAI from 'openai'
import { createClient } from '@supabase/supabase-js'
import {
  getBuiltInArticlePrompt,
  templateTypeToPromptKey,
} from '@/config/articleMachinePrompts'
import { SITE_URL } from '@/config/site'
import { isAdminAccessToken, isCronSecretToken } from '@/lib/server/admin-auth'
import { estimateReadingTime, parseArticleJson, slugify } from '@/lib/utils'

export const maxDuration = 180

function getServerSupabase() {
  const url = (process.env.SUPABASE_URL || process.env.NEXT_PUBLIC_SUPABASE_URL || '')
    .trim()
    .replace(/\/$/, '')
  const serviceKey = (process.env.SUPABASE_SERVICE_ROLE_KEY || '').trim()
  if (!url || !serviceKey) return null
  return createClient(url, serviceKey)
}

function extractBearerToken(request: Request): string {
  return (request.headers.get('authorization') || '').replace(/^Bearer\s+/i, '').trim()
}

async function canRunPipeline(supabase: ReturnType<typeof createClient>, token: string): Promise<boolean> {
  if (!token) return false
  if (isCronSecretToken(token)) return true
  return isAdminAccessToken(supabase, token)
}

async function readAiConfigMap(supabase: ReturnType<typeof createClient>, keys: string[]) {
  const { data, error } = await supabase.from('ai_config').select('key, value').in('key', keys)
  if (error) throw new Error(`ai_config read failed: ${error.message}`)
  const out: Record<string, string> = {}
  for (const row of data ?? []) {
    const v = row.value
    out[row.key] = typeof v === 'string' ? v : JSON.stringify(v ?? '')
  }
  return out
}

async function resolvePrompt(supabase: ReturnType<typeof createClient>, templateType: string) {
  const promptKey = templateTypeToPromptKey(templateType)
  const config = await readAiConfigMap(supabase, [promptKey, 'article_machine_prompt_default'])
  if (config[promptKey]?.trim()) {
    return { systemPrompt: config[promptKey], promptKey }
  }
  if (config.article_machine_prompt_default?.trim()) {
    return { systemPrompt: config.article_machine_prompt_default, promptKey: 'default' }
  }
  return { systemPrompt: getBuiltInArticlePrompt(templateType), promptKey: templateType }
}

async function uniqueSlug(supabase: ReturnType<typeof createClient>, base: string): Promise<string> {
  const normalized = slugify(base) || `article-${Date.now()}`
  let candidate = normalized
  for (let i = 2; i < 50; i++) {
    const { data } = await supabase.from('blog_posts').select('id').eq('slug', candidate).limit(1)
    if (!data?.length) return candidate
    candidate = `${normalized}-${i}`
  }
  return `${normalized}-${Date.now()}`
}

async function logUsage(
  supabase: ReturnType<typeof createClient>,
  row: {
    post_id?: string | null
    schedule_id?: string | null
    model: string
    input_tokens: number
    output_tokens: number
    duration_ms: number
    success: boolean
    error_text?: string
    prompt_key?: string
  },
) {
  const costUsd =
    (row.input_tokens / 1_000_000) * 0.15 + (row.output_tokens / 1_000_000) * 0.6
  await supabase.from('api_usage_log').insert({
    post_id: row.post_id ?? null,
    schedule_id: row.schedule_id ?? null,
    provider: 'openai',
    model: row.model,
    operation: 'article_generation',
    input_tokens: row.input_tokens,
    output_tokens: row.output_tokens,
    total_tokens: row.input_tokens + row.output_tokens,
    cost_usd: costUsd,
    duration_ms: row.duration_ms,
    success: row.success,
    error_text: row.error_text ?? null,
    prompt_key: row.prompt_key ?? null,
  })
}

export async function handleGenerateArticle(request: Request): Promise<Response> {
  const supabase = getServerSupabase()
  if (!supabase) {
    return Response.json({ error: 'Server configuration error' }, { status: 500 })
  }

  const token = extractBearerToken(request)
  if (!(await canRunPipeline(supabase, token))) {
    return Response.json({ error: 'Unauthorized' }, { status: 401 })
  }

  let body: {
    topic?: string
    card_name?: string
    template_type?: string
    schedule_id?: string
    keywords?: string
  }

  try {
    body = await request.json()
  } catch {
    return Response.json({ error: 'Invalid JSON body' }, { status: 400 })
  }

  const topic = (body.topic || body.card_name || '').trim()
  const templateType = (body.template_type || 'seo-blog').trim()
  const scheduleId = body.schedule_id?.trim() || null
  const keywords = body.keywords?.trim() || ''

  if (!topic) {
    return Response.json({ error: 'topic or card_name is required' }, { status: 400 })
  }

  const openaiKey = (process.env.OPENAI_API_KEY || '').trim()
  if (!openaiKey) {
    return Response.json({ error: 'OPENAI_API_KEY not configured' }, { status: 500 })
  }

  if (scheduleId) {
    await supabase
      .from('publishing_schedule')
      .update({ status: 'processing', updated_at: new Date().toISOString() })
      .eq('id', scheduleId)
  }

  const { systemPrompt, promptKey } = await resolvePrompt(supabase, templateType)
  const model = process.env.OPENAI_MODEL || 'gpt-4o-mini'
  const openai = new OpenAI({ apiKey: openaiKey })
  const start = Date.now()

  try {
    const completion = await openai.chat.completions.create({
      model,
      messages: [
        { role: 'system', content: systemPrompt },
        {
          role: 'user',
          content: `Write an SEO blog article on this topic: "${topic}"${keywords ? `\nTarget keywords: ${keywords}` : ''}`,
        },
      ],
      temperature: 0.7,
      max_tokens: 8000,
      response_format: { type: 'json_object' },
    })

    const raw = completion.choices[0]?.message?.content || ''
    const parsed = parseArticleJson(raw)
    if (!parsed) {
      throw new Error('Failed to parse article JSON from OpenAI')
    }

    const title = String(parsed.title || topic)
    const slug = await uniqueSlug(supabase, String(parsed.slug || title))
    const content = String(parsed.content || '')
    const readingTime =
      typeof parsed.reading_time === 'number'
        ? parsed.reading_time
        : estimateReadingTime(content)

    const featuredImage = `${SITE_URL}/images/mobile-app-development.jpg`

    const { data: post, error: insertError } = await supabase
      .from('blog_posts')
      .insert({
        slug,
        title,
        meta_title: String(parsed.title || title),
        meta_description: String(parsed.meta_description || ''),
        meta_keywords: String(parsed.meta_keywords || keywords),
        seo_focus_keyword: String(parsed.seo_focus_keyword || keywords.split(',')[0] || ''),
        excerpt: String(parsed.excerpt || ''),
        content,
        featured_image: featuredImage,
        og_image: featuredImage,
        author_name: 'TechWebLabs',
        status: 'published',
        published_at: new Date().toISOString(),
        reading_time: readingTime,
        schema_type: 'BlogPosting',
      })
      .select('id, slug, title')
      .single()

    if (insertError) throw new Error(insertError.message)

    const durationMs = Date.now() - start
    await logUsage(supabase, {
      post_id: post.id,
      schedule_id: scheduleId,
      model,
      input_tokens: completion.usage?.prompt_tokens ?? 0,
      output_tokens: completion.usage?.completion_tokens ?? 0,
      duration_ms: durationMs,
      success: true,
      prompt_key: promptKey,
    })

    if (scheduleId) {
      await supabase
        .from('publishing_schedule')
        .update({
          status: 'done',
          post_id: post.id,
          error_text: null,
          updated_at: new Date().toISOString(),
        })
        .eq('id', scheduleId)
    }

    return Response.json({
      success: true,
      post_id: post.id,
      slug: post.slug,
      title: post.title,
      url: `${SITE_URL}/blogs/${post.slug}`,
    })
  } catch (err) {
    const message = err instanceof Error ? err.message : 'Generation failed'
    const durationMs = Date.now() - start

    await logUsage(supabase, {
      schedule_id: scheduleId,
      model,
      input_tokens: 0,
      output_tokens: 0,
      duration_ms: durationMs,
      success: false,
      error_text: message,
      prompt_key: promptKey,
    })

    if (scheduleId) {
      await supabase
        .from('publishing_schedule')
        .update({
          status: 'failed',
          error_text: message,
          updated_at: new Date().toISOString(),
        })
        .eq('id', scheduleId)
    }

    return Response.json({ error: message }, { status: 500 })
  }
}
