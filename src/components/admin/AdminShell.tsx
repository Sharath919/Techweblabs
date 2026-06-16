'use client'

import Link from 'next/link'
import { usePathname, useRouter } from 'next/navigation'
import { Menu } from 'lucide-react'
import { useState } from 'react'
import { AdminAuthGuard } from '@/components/admin/AdminAuthGuard'
import { ADMIN_NAV, adminPageTitle } from '@/config/admin-nav'
import { createBrowserSupabase } from '@/lib/supabase'

export default function AdminShell({ children }: { children: React.ReactNode }) {
  const pathname = usePathname()
  const router = useRouter()
  const [sidebarOpen, setSidebarOpen] = useState(false)
  const title = adminPageTitle(pathname)

  async function signOut() {
    const supabase = createBrowserSupabase()
    await supabase.auth.signOut()
    router.replace('/admin/login')
  }

  return (
    <AdminAuthGuard>
      <div className="admin-app">
        {sidebarOpen && (
          <button
            type="button"
            className="admin-overlay md:hidden"
            aria-label="Close menu"
            onClick={() => setSidebarOpen(false)}
          />
        )}

        <aside className={`admin-sidebar ${sidebarOpen ? 'admin-sidebar--open' : ''}`}>
          <div className="admin-sidebar__brand">
            <p className="admin-sidebar__logo">TechWebLabs</p>
            <p className="admin-sidebar__tag">Admin Panel</p>
          </div>

          <nav className="admin-sidebar__nav">
            {ADMIN_NAV.map(({ href, label, icon: Icon, end }) => {
              const active = end ? pathname === href : pathname === href || pathname.startsWith(`${href}/`)
              return (
                <Link
                  key={href}
                  href={href}
                  onClick={() => setSidebarOpen(false)}
                  className={`admin-nav-link${active ? ' admin-nav-link--active' : ''}`}
                >
                  <Icon size={18} strokeWidth={2} />
                  <span>{label}</span>
                </Link>
              )
            })}
          </nav>

          <div className="admin-sidebar__footer">
            <button type="button" className="admin-btn admin-btn--ghost admin-btn--block" onClick={signOut}>
              Sign out
            </button>
          </div>
        </aside>

        <div className="admin-main">
          <header className="admin-header">
            <div className="admin-header__left">
              <button
                type="button"
                className="admin-icon-btn md:hidden"
                aria-label="Open menu"
                onClick={() => setSidebarOpen(true)}
              >
                <Menu size={22} />
              </button>
              <h1 className="admin-header__title">{title}</h1>
            </div>
            <Link href="/" className="admin-header__link">
              View Site →
            </Link>
          </header>

          <main className="admin-content">{children}</main>
        </div>
      </div>
    </AdminAuthGuard>
  )
}
