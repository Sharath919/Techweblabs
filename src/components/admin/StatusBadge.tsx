const STYLES: Record<string, string> = {
  published: 'admin-badge admin-badge--success',
  done: 'admin-badge admin-badge--success',
  pending: 'admin-badge admin-badge--warn',
  processing: 'admin-badge admin-badge--info',
  draft: 'admin-badge admin-badge--muted',
  failed: 'admin-badge admin-badge--danger',
  new: 'admin-badge admin-badge--info',
  archived: 'admin-badge admin-badge--muted',
}

export default function StatusBadge({ status }: { status: string }) {
  const cls = STYLES[status] ?? 'admin-badge admin-badge--muted'
  return <span className={cls}>{status}</span>
}
