import { handleGenerateArticle, maxDuration } from '@/lib/server/generate-article'

export { maxDuration }

export async function POST(request: Request) {
  return handleGenerateArticle(request)
}

export async function OPTIONS() {
  return new Response(null, {
    status: 204,
    headers: {
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Methods': 'POST, OPTIONS',
      'Access-Control-Allow-Headers': 'content-type, authorization',
    },
  })
}
