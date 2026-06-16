import { handleTestArticleMachine } from '@/lib/server/test-article-machine'

export const maxDuration = 60

export async function POST(request: Request) {
  return handleTestArticleMachine(request)
}
