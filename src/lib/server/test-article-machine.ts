import { isAdminAccessToken } from '@/lib/server/admin-auth'
import { callClaude, getAnthropicModel } from '@/lib/server/claude'
import {
  getBuiltInArticlePrompt,
  templateTypeToPromptKey,
  TEMPLATE_TYPES,
} from '@/config/articleMachinePrompts'
import { createClient } from '@supabase/supabase-js'

export const maxDuration = 60

function getServerSupabase() {
  const url = (process.env.SUPABASE_URL || process.env.NEXT_PUBLIC_SUPABASE_URL || '').trim()
  const key = (process.env.SUPABASE_SERVICE_ROLE_KEY || '').trim()
  if (!url || !key) return null
  return createClient(url, key)
}

async function readPrompt(supabase: ReturnType<typeof createClient>, templateType: string) {
  const promptKey = templateTypeToPromptKey(templateType)
  const keys = [promptKey, 'article_machine_prompt_default']
  const { data } = await supabase.from('ai_config').select('key, value').in('key', keys)
  const map: Record<string, string> = {}
  for (const row of data ?? []) {
    map[row.key] = typeof row.value === 'string' ? row.value : JSON.stringify(row.value ?? '')
  }
  if (map[promptKey]?.trim()) return map[promptKey]
  if (map.article_machine_prompt_default?.trim()) return map.article_machine_prompt_default
  return getBuiltInArticlePrompt(templateType)
}

export async function handleTestArticleMachine(request: Request): Promise<Response> {
  const token = (request.headers.get('authorization') || '').replace(/^Bearer\s+/i, '').trim()
  const supabase = getServerSupabase()
  if (!supabase) return Response.json({ error: 'Server configuration error' }, { status: 500 })
  if (!(await isAdminAccessToken(supabase, token))) {
    return Response.json({ error: 'Unauthorized' }, { status: 401 })
  }

  let body: { topic?: string; template_type?: string }
  try {
    body = await request.json()
  } catch {
    return Response.json({ error: 'Invalid JSON' }, { status: 400 })
  }

  const topic = (body.topic || 'Flutter app development cost in India 2026').trim()
  const templateType = (body.template_type || 'seo-blog').trim()
  const templateLabel = TEMPLATE_TYPES[templateType as keyof typeof TEMPLATE_TYPES] || templateType

  try {
    const systemPrompt = await readPrompt(supabase, templateType)
    const claude = await callClaude({
      systemPrompt: `${systemPrompt}\n\nRespond with a short sample opening paragraph only (no JSON).`,
      userMessage: `Write a sample opening for: "${topic}" (${templateLabel}).`,
      maxTokens: 800,
    })
    return Response.json({
      output: claude.text,
      model: claude.model || getAnthropicModel(),
      input_tokens: claude.inputTokens,
      output_tokens: claude.outputTokens,
    })
  } catch (err) {
    const message = err instanceof Error ? err.message : 'Test failed'
    return Response.json({ error: message }, { status: 500 })
  }
}
