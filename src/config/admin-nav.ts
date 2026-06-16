import type { LucideIcon } from 'lucide-react'
import {
  Bot,
  CalendarClock,
  FileText,
  LayoutDashboard,
  Settings,
  UserPlus,
  Wrench,
} from 'lucide-react'

export type AdminNavItem = {
  href: string
  label: string
  icon: LucideIcon
  end?: boolean
}

export const ADMIN_NAV: AdminNavItem[] = [
  { href: '/admin', label: 'Dashboard', icon: LayoutDashboard, end: true },
  { href: '/admin/articles', label: 'Articles', icon: FileText },
  { href: '/admin/schedule', label: 'Schedule', icon: CalendarClock },
  { href: '/admin/settings/article-machine', label: 'Article Machine', icon: Bot },
  { href: '/admin/leads', label: 'Leads', icon: UserPlus },
  { href: '/admin/system', label: 'System', icon: Wrench },
]

export const ADMIN_PAGE_TITLES: Record<string, string> = {
  '/admin': 'Dashboard',
  '/admin/articles': 'Articles',
  '/admin/schedule': 'Publishing Schedule',
  '/admin/settings/article-machine': 'Article Machine',
  '/admin/leads': 'Lead Captures',
  '/admin/system': 'System',
}

export function adminPageTitle(pathname: string): string {
  return ADMIN_PAGE_TITLES[pathname] ?? 'Admin'
}
