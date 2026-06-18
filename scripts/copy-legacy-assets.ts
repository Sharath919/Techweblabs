/**
 * Copy legacy PHP assets (css, js, images, fonts) into public/ for Vercel static serving.
 */
import { cpSync, existsSync, mkdirSync } from 'fs'
import { join } from 'path'

const ROOT = join(import.meta.dirname, '..')
const PUBLIC = join(ROOT, 'public')

const DIRS = ['css', 'js', 'images', 'fonts', 'favicons', 'homepage', 'components']

mkdirSync(PUBLIC, { recursive: true })

for (const dir of DIRS) {
  const src = join(ROOT, dir)
  const dest = join(PUBLIC, dir)
  if (existsSync(src)) {
    cpSync(src, dest, { recursive: true })
    console.log(`Copied ${dir}/ → public/${dir}/`)
  }
}

// Copy favicon if exists at root level files
for (const file of ['robots.txt']) {
  const src = join(ROOT, file)
  if (existsSync(src)) {
    cpSync(src, join(PUBLIC, file))
    console.log(`Copied ${file}`)
  }
}

console.log('Legacy assets copied to public/')
