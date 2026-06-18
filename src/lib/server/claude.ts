/** Anthropic Claude API helper (same pattern as Outdoor Deals / Limansa). */

const DEFAULT_MODEL = 'claude-sonnet-4-20250514'

const CLAUDE_COSTS: Record<string, { input: number; output: number }> = {
  'claude-sonnet-4-20250514': { input: 3.0, output: 15.0 },
  'claude-haiku-4-5-20251001': { input: 0.8, output: 4.0 },
}

export function getAnthropicApiKey(): string {
  return (process.env.ANTHROPIC_API_KEY || '').trim()
}

export function getAnthropicModel(): string {
  return (process.env.ANTHROPIC_MODEL || DEFAULT_MODEL).trim()
}

export function calculateClaudeCost(
  model: string,
  inputTokens: number,
  outputTokens: number,
): number {
  const pricing = CLAUDE_COSTS[model] ?? CLAUDE_COSTS[DEFAULT_MODEL]
  return (
    (inputTokens / 1_000_000) * pricing.input +
    (outputTokens / 1_000_000) * pricing.output
  )
}

export async function callClaude(params: {
  systemPrompt: string
  userMessage: string
  maxTokens?: number
  timeoutMs?: number
}): Promise<{
  text: string
  inputTokens: number
  outputTokens: number
  model: string
}> {
  const apiKey = getAnthropicApiKey()
  if (!apiKey) {
    throw new Error('ANTHROPIC_API_KEY not configured')
  }

  const model = getAnthropicModel()
  const res = await fetch('https://api.anthropic.com/v1/messages', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'x-api-key': apiKey,
      'anthropic-version': '2023-06-01',
    },
    body: JSON.stringify({
      model,
      max_tokens: params.maxTokens ?? 8000,
      system: params.systemPrompt,
      messages: [{ role: 'user', content: params.userMessage }],
    }),
    signal: AbortSignal.timeout(params.timeoutMs ?? 120_000),
  })

  const data = (await res.json().catch(() => ({}))) as {
    content?: Array<{ type: string; text?: string }>
    error?: { message?: string }
    usage?: { input_tokens?: number; output_tokens?: number }
  }

  if (!res.ok) {
    throw new Error(data.error?.message || `Claude API returned ${res.status}`)
  }

  const text =
    data.content?.find((block) => block.type === 'text')?.text?.trim() || ''

  if (!text) {
    throw new Error('Empty response from Claude')
  }

  return {
    text,
    inputTokens: data.usage?.input_tokens ?? 0,
    outputTokens: data.usage?.output_tokens ?? 0,
    model,
  }
}
