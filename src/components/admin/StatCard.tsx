import type { LucideIcon } from 'lucide-react'

type StatCardProps = {
  icon: LucideIcon
  label: string
  value: string | number
  hint?: string
}

export default function StatCard({ icon: Icon, label, value, hint }: StatCardProps) {
  return (
    <div className="admin-stat-card">
      <div className="admin-stat-card__top">
        <span className="admin-stat-card__icon">
          <Icon size={18} strokeWidth={2} />
        </span>
        {hint && <span className="admin-stat-card__hint">{hint}</span>}
      </div>
      <p className="admin-stat-card__value">{value}</p>
      <p className="admin-stat-card__label">{label}</p>
    </div>
  )
}
