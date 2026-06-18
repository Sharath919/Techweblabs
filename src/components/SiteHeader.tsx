import Link from 'next/link'

export default function SiteHeader() {
  return (
    <header className="header-pr nav-bg-b nav-bg-w main-header navfix fixed-top menu-white">
      <div className="container-fluid m-pad">
        <div className="menu-header">
          <div className="dsk-logo">
            <Link className="nav-brand" href="/" aria-label="TechWebLabs Home">
              <img src="/images/logo.png" alt="TechWebLabs" width={180} height={48} />
            </Link>
          </div>
          <nav className="custom-nav" role="navigation">
            <ul className="nav-list">
              <li><Link href="/about">About</Link></li>
              <li><Link href="/blogs">Blog</Link></li>
              <li><Link href="/contact">Contact</Link></li>
              <li><Link href="/contact" className="btn-main bg-btn lnk">Get Quote</Link></li>
            </ul>
          </nav>
        </div>
      </div>
    </header>
  )
}
