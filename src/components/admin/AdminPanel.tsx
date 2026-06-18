import type { ReactNode } from 'react'

type AdminPanelProps = {
  title?: string
  description?: string
  actions?: ReactNode
  children: ReactNode
  className?: string
}

export default function AdminPanel({
  title,
  description,
  actions,
  children,
  className = '',
}: AdminPanelProps) {
  return (
    <section className={`admin-panel ${className}`.trim()}>
      {(title || actions) && (
        <header className="admin-panel__header">
          <div>
            {title && <h2 className="admin-panel__title">{title}</h2>}
            {description && <p className="admin-panel__desc">{description}</p>}
          </div>
          {actions && <div className="admin-panel__actions">{actions}</div>}
        </header>
      )}
      <div className="admin-panel__body">{children}</div>
    </section>
  )
}
