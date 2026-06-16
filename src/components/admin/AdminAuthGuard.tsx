'use client'

import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import { isBootstrapAdminEmail } from '@/config/admin'
import { createBrowserSupabase } from '@/lib/supabase'

export function AdminAuthGuard({ children }: { children: React.ReactNode }) {
  const router = useRouter()
  const [ready, setReady] = useState(false)

  useEffect(() => {
    const supabase = createBrowserSupabase()
    let active = true

    async function verifySession() {
      const { data: { session } } = await supabase.auth.getSession()
      if (!active) return

      if (!session?.user) {
        router.replace('/admin/login')
        return
      }

      const email = session.user.email
      if (isBootstrapAdminEmail(email)) {
        setReady(true)
        return
      }

      const { data } = await supabase
        .from('admin_users')
        .select('id')
        .eq('id', session.user.id)
        .maybeSingle()

      if (!active) return

      if (!data) {
        await supabase.auth.signOut()
        router.replace('/admin/login?error=unauthorized')
        return
      }

      setReady(true)
    }

    verifySession()

    const { data: { subscription } } = supabase.auth.onAuthStateChange((_event, session) => {
      if (!session) {
        setReady(false)
        router.replace('/admin/login')
      }
    })

    return () => {
      active = false
      subscription.unsubscribe()
    }
  }, [router])

  if (!ready) {
    return (
      <div className="admin-shell d-flex align-items-center justify-content-center">
        <p className="text-muted mb-0">Checking session…</p>
      </div>
    )
  }

  return <>{children}</>
}
