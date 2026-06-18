import { handleAuditPublishing } from '@/lib/server/audit-publishing'

export const maxDuration = 60

export async function GET(request: Request) {
  return handleAuditPublishing(request)
}
