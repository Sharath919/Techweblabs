import { createServerSupabase } from '@/lib/supabase'

export const dynamic = 'force-dynamic'

export default async function AdminLeadsPage() {
  const supabase = createServerSupabase()
  let leads: Array<{
    id: string
    name: string | null
    email: string | null
    phone: string | null
    requirement: string | null
    status: string
    created_at: string
  }> = []

  if (supabase) {
    const { data } = await supabase
      .from('lead_captures')
      .select('id, name, email, phone, requirement, status, created_at')
      .order('created_at', { ascending: false })
      .limit(100)
    leads = data ?? []
  }

  return (
    <div>
      <h1 className="h3 mb-4">Lead Captures</h1>
      <div className="admin-card">
        <table className="table table-sm">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Requirement</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            {leads.map((lead) => (
              <tr key={lead.id}>
                <td>{lead.name || '—'}</td>
                <td>{lead.email || '—'}</td>
                <td>{lead.phone || '—'}</td>
                <td>{lead.requirement?.slice(0, 60) || '—'}</td>
                <td><span className="badge bg-secondary">{lead.status}</span></td>
                <td>{new Date(lead.created_at).toLocaleDateString()}</td>
              </tr>
            ))}
            {leads.length === 0 && (
              <tr><td colSpan={6} className="text-muted">No leads yet</td></tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  )
}
