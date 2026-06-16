import AdminPanel from '@/components/admin/AdminPanel'
import StatusBadge from '@/components/admin/StatusBadge'
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
    <AdminPanel
      title="Lead Captures"
      description="Leads from the homepage persona widget and contact flows."
    >
      <div className="admin-table-wrap">
        <table className="admin-table">
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
                <td>{lead.requirement?.slice(0, 80) || '—'}</td>
                <td>
                  <StatusBadge status={lead.status} />
                </td>
                <td>{new Date(lead.created_at).toLocaleDateString()}</td>
              </tr>
            ))}
            {leads.length === 0 && (
              <tr>
                <td colSpan={6} style={{ color: 'var(--admin-muted)' }}>
                  No leads yet
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </AdminPanel>
  )
}
