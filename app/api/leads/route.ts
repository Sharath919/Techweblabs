import { createServerSupabase } from '@/lib/supabase'
import { isAdminSecretRequest } from '@/lib/server/admin-auth'
import { getBuiltInArticlePrompt } from '@/config/articleMachinePrompts'

export async function POST(request: Request) {
  if (!isAdminSecretRequest(request)) {
    const authHeader = request.headers.get('authorization')
    if (!authHeader) {
      return Response.json({ error: 'Unauthorized' }, { status: 401 })
    }
  }

  const supabase = createServerSupabase()
  if (!supabase) {
    return Response.json({ error: 'Database not configured' }, { status: 500 })
  }

  let body: { name?: string; email?: string; phone?: string; company?: string; requirement?: string; budget?: string; timeline?: string; source_page?: string; transcript?: unknown[]; qualification_score?: number }

  try {
    body = await request.json()
  } catch {
    return Response.json({ error: 'Invalid JSON' }, { status: 400 })
  }

  const { error } = await supabase.from('lead_captures').insert({
    name: body.name?.trim() || null,
    email: body.email?.trim() || null,
    phone: body.phone?.trim() || null,
    company: body.company?.trim() || null,
    requirement: body.requirement?.trim() || null,
    budget: body.budget?.trim() || null,
    timeline: body.timeline?.trim() || null,
    source_page: body.source_page || '/',
    persona_transcript: body.transcript ?? [],
    qualification_score: body.qualification_score ?? null,
  })

  if (error) {
    return Response.json({ error: error.message }, { status: 500 })
  }

  return Response.json({ success: true })
}

export async function GET() {
  const supabase = createServerSupabase()
  if (!supabase) {
    return Response.json({ welcome: 'Hi! I help startups plan mobile and web apps. What are you looking to build?' })
  }

  const { data } = await supabase
    .from('ai_config')
    .select('key, value')
    .in('key', ['persona_welcome_message', 'persona_system_prompt'])

  const config: Record<string, string> = {}
  for (const row of data ?? []) {
    const v = row.value
    config[row.key] = typeof v === 'string' ? v.replace(/^"|"$/g, '') : String(v ?? '')
  }

  return Response.json({
    welcome: config.persona_welcome_message || 'Hi! I\'m here to help you find the right app development solution. What are you building?',
    systemPrompt: config.persona_system_prompt || getDefaultPersonaPrompt(),
  })
}

function getDefaultPersonaPrompt(): string {
  return `You are a friendly sales assistant for TechWebLabs, a mobile app and web development company in Hyderabad, India.

Your goal: qualify leads through natural conversation. Ask about:
1. What they want to build (app type, platform)
2. Timeline and budget range
3. Their role (founder, product manager, etc.)
4. Contact info when they're ready (name, email, phone)

Be concise (2-3 sentences per reply). Professional but warm. Don't be pushy.
When you have enough info, summarize and ask if they'd like a free consultation.
Company strengths: Flutter, React Native, custom web apps, 6+ years experience, startup-friendly.`
}
