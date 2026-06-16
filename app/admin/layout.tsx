'use client'

import Link from 'next/link'
import { usePathname, useRouter } from 'next/navigation'
import { AdminAuthGuard } from '@/components/admin/AdminAuthGuard'
import { createBrowserSupabase } from '@/lib/supabase'

const NAV = [
  { href: '/admin', label: 'Dashboard' },
  { href: '/admin/articles', label: 'Articles' },
  { href: '/admin/schedule', label: 'Schedule' },
  { href: '/admin/settings/article-machine', label: 'Article Machine' },
  { href: '/admin/leads', label: 'Leads' },
  { href: '/admin/system', label: 'System' },
]

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  const pathname = usePathname()
  const router = useRouter()

  if (pathname === '/admin/login') {
    return <>{children}</>
  }

  async function signOut() {
    const supabase = createBrowserSupabase()
    await supabase.auth.signOut()
    router.replace('/admin/login')
  }

  return (
    <AdminAuthGuard>
      <div className="admin-shell">
        <nav className="admin-nav d-flex align-items-center flex-wrap">
          <strong className="me-4">TechWebLabs Admin</strong>
          {NAV.map((item) => (
            <Link
              key={item.href}
              href={item.href}
              style={{ fontWeight: pathname === item.href ? 700 : 400 }}
            >
              {item.label}
            </Link>
          ))}
          <Link href="/" className="ms-auto me-3">
            View Site →
          </Link>
          <button type="button" className="btn btn-sm btn-outline-secondary" onClick={signOut}>
            Sign out
          </button>
        </nav>
        <main className="container py-4">{children}</main>
      </div>
    </AdminAuthGuard>
  )
}
