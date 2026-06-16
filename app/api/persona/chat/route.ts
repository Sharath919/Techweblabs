import { createServerSupabase } from '@/lib/supabase'
import { callClaude } from '@/lib/server/claude'

export const maxDuration = 60

export async function POST(request: Request) {
  let body: { messages?: Array<{ role: string; content: string }> }
  try {
    body = await request.json()
  } catch {
    return Response.json({ error: 'Invalid JSON' }, { status: 400 })
  }

  const messages = body.messages ?? []
  if (!messages.length) {
    return Response.json({ error: 'messages required' }, { status: 400 })
  }

  const supabase = createServerSupabase()
  let systemPrompt = `You are a friendly sales assistant for TechWebLabs. Qualify leads for app development services. Be concise.`

  if (supabase) {
    const { data } = await supabase
      .from('ai_config')
      .select('value')
      .eq('key', 'persona_system_prompt')
      .maybeSingle()
    if (data?.value) {
      const v = typeof data.value === 'string' ? data.value : JSON.stringify(data.value)
      if (v.trim() && v !== '""') systemPrompt = v.replace(/^"|"$/g, '')
    }
  }

  const transcript = messages
    .map((m) => `${m.role === 'user' ? 'User' : 'Assistant'}: ${m.content}`)
    .join('\n')

  try {
    const claude = await callClaude({
      systemPrompt,
      userMessage: `${transcript}\n\nAssistant:`,
      maxTokens: 500,
      timeoutMs: 60_000,
    })

    return Response.json({ reply: claude.text })
  } catch (err) {
    const message = err instanceof Error ? err.message : 'Chat failed'
    return Response.json({ error: message }, { status: 500 })
  }
}
