import { runCronPublishing } from '@/lib/server/run-cron-publishing'

export const maxDuration = 300

export async function GET(request: Request) {
  return runCronPublishing(request)
}

export async function POST(request: Request) {
  return runCronPublishing(request)
}
