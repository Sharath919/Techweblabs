'use client'

import { Suspense, useState } from 'react'
import { useSearchParams } from 'next/navigation'
import { createBrowserSupabase } from '@/lib/supabase'

function AdminLoginForm() {
  const searchParams = useSearchParams()
  const unauthorized = searchParams.get('error') === 'unauthorized'
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  async function handleLogin(e: React.FormEvent) {
    e.preventDefault()
    setLoading(true)
    setError('')
    const supabase = createBrowserSupabase()
    const { error: authError } = await supabase.auth.signInWithPassword({ email, password })
    if (authError) {
      setError(authError.message)
      setLoading(false)
      return
    }
    window.location.href = '/admin'
  }

  return (
    <div className="admin-login-page">
      <div className="admin-login-card">
        <h1>TechWebLabs Admin</h1>
        <p>Sign in to manage articles, schedule, and leads.</p>
        {unauthorized && (
          <p className="admin-message admin-message--error">Your account is not authorized for admin access.</p>
        )}
        <form onSubmit={handleLogin} className="admin-stack">
          <div>
            <label className="admin-label" htmlFor="email">
              Email
            </label>
            <input
              id="email"
              type="email"
              className="admin-input"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </div>
          <div>
            <label className="admin-label" htmlFor="password">
              Password
            </label>
            <input
              id="password"
              type="password"
              className="admin-input"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>
          {error && <p className="admin-message admin-message--error">{error}</p>}
          <button type="submit" className="admin-btn admin-btn--primary admin-btn--block" disabled={loading}>
            {loading ? 'Signing in…' : 'Sign In'}
          </button>
        </form>
      </div>
    </div>
  )
}

export default function AdminLoginPage() {
  return (
    <Suspense fallback={<div className="admin-loading">Loading…</div>}>
      <AdminLoginForm />
    </Suspense>
  )
}
