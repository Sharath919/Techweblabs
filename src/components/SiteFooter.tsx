import Link from 'next/link'

export default function SiteFooter() {
  return (
    <footer className="footer-section bg-light">
      <div className="container py-5">
        <div className="row">
          <div className="col-lg-4">
            <img src="/images/logo.png" alt="TechWebLabs" width={160} />
            <p className="mt-3">Leading mobile app and web development company in Hyderabad, India.</p>
          </div>
          <div className="col-lg-4">
            <h5>Quick Links</h5>
            <ul className="list-unstyled">
              <li><Link href="/about">About Us</Link></li>
              <li><Link href="/blogs">Blog</Link></li>
              <li><Link href="/contact">Contact</Link></li>
            </ul>
          </div>
          <div className="col-lg-4">
            <h5>Contact</h5>
            <p>info@techweblabs.com<br />+91-7670837961</p>
          </div>
        </div>
        <hr />
        <p className="text-center mb-0">&copy; {new Date().getFullYear()} TechWebLabs. All rights reserved.</p>
      </div>
    </footer>
  )
}
