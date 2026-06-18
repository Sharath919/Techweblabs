import type { Metadata } from 'next'
import './globals.css'

export const metadata: Metadata = {
  metadataBase: new URL(process.env.NEXT_PUBLIC_SITE_URL || 'https://techweblabs.com'),
  title: {
    default: 'TechWebLabs - Mobile App & Web Development',
    template: '%s | TechWebLabs',
  },
  description: 'TechWebLabs is a leading mobile app and web development company.',
}

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <head>
        <link rel="icon" href="/favicons/favicon.ico" />
        <link href="/css/css-bootstrap.min.css" rel="stylesheet" />
        <link href="/css/css-plugin.min.css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <link href="/css/css-style.css" rel="stylesheet" />
        <link href="/css/css-responsive.css" rel="stylesheet" />
        <link href="/css/css-darkmode.css" rel="stylesheet" />
      </head>
      <body>{children}</body>
    </html>
  )
}
