/**
 * Pre-generate static sitemap files into public/ at build time.
 * Runtime routes in app/sitemap*.xml/route.ts serve the same XML.
 */
import { mkdirSync, writeFileSync } from 'fs'
import { join } from 'path'
import { SITE_URL } from '../src/config/site'
import {
  buildSitemapIndexXml,
  buildUrlsetXml,
  fetchPublishedBlogUrls,
  getStaticSitemapUrls,
  SITEMAP_XSL_CONTENT,
} from '../src/lib/server/sitemap-xml'

const PUBLIC = join(import.meta.dirname, '..', 'public')

function writeFile(filename: string, content: string) {
  writeFileSync(join(PUBLIC, filename), content, 'utf8')
  console.log(`[sitemap] Written public/${filename}`)
}

async function main() {
  mkdirSync(PUBLIC, { recursive: true })
  console.log(`[sitemap] Generating sitemap for ${SITE_URL}…\n`)

  const pageUrls = getStaticSitemapUrls()
  const postUrls = await fetchPublishedBlogUrls()

  writeFile('sitemap.xsl', SITEMAP_XSL_CONTENT)
  writeFile('sitemap.xml', buildSitemapIndexXml())
  writeFile('sitemap-pages.xml', buildUrlsetXml(pageUrls, false))
  writeFile('sitemap-posts.xml', buildUrlsetXml(postUrls, true))

  const totalImages = postUrls.reduce((sum, u) => sum + (u.images?.length ?? 0), 0)
  console.log(`
[sitemap] Summary:
   Static pages     : ${pageUrls.length}
   Published posts  : ${postUrls.length}
   Total images     : ${totalImages}
   Total URLs       : ${pageUrls.length + postUrls.length}
`)
}

main().catch((err) => {
  console.error('[sitemap] Generation failed:', err)
  process.exit(1)
})
