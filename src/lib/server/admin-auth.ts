import { createClient } from '@supabase/supabase-js'

const BOOTSTRAP_ADMIN_EMAILS = [
  'admin@techweblabs.com',
  'sharathchand19141@gmail.com',
  'sharathbroyt@gmail.com',
]

export async function isAdminAccessToken(
  supabase: ReturnType<typeof createClient>,
  accessToken: string,
): Promise<boolean> {
  const { data: userData, error } = await supabase.auth.getUser(accessToken)
  if (error || !userData.user) return false

  const email = userData.user.email?.toLowerCase().trim()
  if (email && BOOTSTRAP_ADMIN_EMAILS.includes(email)) return true

  const { data } = await supabase
    .from('admin_users')
    .select('id')
    .eq('id', userData.user.id)
    .maybeSingle()

  return !!data
}

export function isCronSecretToken(token: string): boolean {
  const secret = process.env.CRON_SECRET || process.env.ADMIN_SECRET || ''
  return Boolean(secret && token === secret)
}

export async function isCronAuthorizedRequest(request: Request): Promise<boolean> {
  if (request.headers.get('x-vercel-cron') === '1') return true

  const authHeader = request.headers.get('authorization') ?? ''
  const token = authHeader.replace(/^Bearer\s+/i, '').trim()
  if (!token) return false
  if (isCronSecretToken(token)) return true

  const supabaseUrl = process.env.SUPABASE_URL ?? process.env.NEXT_PUBLIC_SUPABASE_URL
  const serviceKey = process.env.SUPABASE_SERVICE_ROLE_KEY
  if (!supabaseUrl || !serviceKey) return false

  const supabase = createClient(supabaseUrl, serviceKey)
  return isAdminAccessToken(supabase, token)
}

export function isAdminSecretRequest(request: Request): boolean {
  const secret = process.env.ADMIN_SECRET || process.env.CRON_SECRET || ''
  if (!secret) return false
  const header = request.headers.get('x-admin-secret') || ''
  const auth = request.headers.get('authorization')?.replace(/^Bearer\s+/i, '') || ''
  return header === secret || auth === secret
}
