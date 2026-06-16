export const BOOTSTRAP_ADMIN_EMAILS = [
  'admin@techweblabs.com',
  'sharathchand19141@gmail.com',
  'sharathbroyt@gmail.com',
]

export function isBootstrapAdminEmail(email: string | null | undefined): boolean {
  const normalized = email?.toLowerCase().trim()
  return Boolean(normalized && BOOTSTRAP_ADMIN_EMAILS.includes(normalized))
}
