# TechWebLabs — Next.js + Supabase Automation

Hybrid Vercel deployment: static PHP-exported service pages + Next.js ISR blogs, admin, cron, and persona widget.

## Quick Start

```bash
cp .env.example .env.local
# Fill in Supabase + Anthropic (Claude) keys

npm install
npm run copy-assets
npm run dev
```

## Supabase Setup

1. Create a **new** Supabase project (separate from Limansa)
2. Run migration: `supabase/migrations/20260616120000_techweblabs_initial.sql`
3. Create an admin user in Supabase Auth
4. Add env vars to `.env.local` and Vercel

## Migrate Existing Blogs (MySQL → Supabase)

```bash
npm run migrate:mysql
```

## Static Page Export (before deploy)

Requires PHP locally:

```bash
npm run export-static
```

Exports all service pages to `public/static/` and injects persona widget on homepage.

## Full Production Build

```bash
npm run build:full
```

## Cron (10 blogs/day on Vercel Hobby)

Same pattern as Limansa: **7 Vercel crons/day × batch of 3 = up to 21/day** (configured in `vercel.json` at UTC 01, 04, 07, 10, 13, 16, 19).

Queue **10 topics** in `/admin/schedule` for the day — each cron run processes up to 3 pending items until the queue is empty.

Set `CRON_SECRET` in Vercel env vars. Vercel cron calls `/api/cron/publishing` automatically.

Optional backup: GitHub Actions workflow in `.github/workflows/cron-publishing.yml` if you want a redundant trigger.

## Vercel Deployment

1. Create new Vercel Hobby account (separate from Limansa)
2. Connect this repo
3. Set env vars from `.env.example`
4. Point `techweblabs.com` DNS to Vercel
5. Run `npm run export-static` before first deploy (or in CI)

## Routes

| Path | Handler |
|------|---------|
| `/` | Static export (homepage + persona widget) |
| `/about`, service pages | Static export via middleware |
| `/blogs`, `/blogs/[slug]` | Next.js ISR |
| `/admin/*` | Next.js admin |
| `/api/*` | Serverless API |
| New pillar pages | `site_pages` table + `app/[slug]` |

## Admin

- `/admin/login` — Supabase auth
- `/admin/schedule` — queue automated articles
- `/admin/settings/article-machine` — prompts & automation toggle
- `/admin/leads` — persona widget captures
