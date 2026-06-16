import OpenAI from 'openai'
import { createServerSupabase } from '@/lib/supabase'

export const maxDuration = 60

export async function POST(request: Request) {
  const openaiKey = (process.env.OPENAI_API_KEY || '').trim()
  if (!openaiKey) {
    return Response.json({ error: 'AI not configured' }, { status: 500 })
  }

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

  const openai = new OpenAI({ apiKey: openaiKey })
  const model = process.env.OPENAI_MODEL || 'gpt-4o-mini'

  try {
    const completion = await openai.chat.completions.create({
      model,
      messages: [{ role: 'system', content: systemPrompt }, ...messages.map((m) => ({ role: m.role as 'user' | 'assistant', content: m.content }))],
      temperature: 0.7,
      max_tokens: 500,
    })

    const reply = completion.choices[0]?.message?.content || 'Thanks for sharing! Could you tell me more about your project?'

    return Response.json({ reply })
  } catch (err) {
    const message = err instanceof Error ? err.message : 'Chat failed'
    return Response.json({ error: message }, { status: 500 })
  }
}
