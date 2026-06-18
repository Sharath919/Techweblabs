export async function GET() {
  return Response.json({
    status: 'ok',
    service: 'techweblabs',
    timestamp: new Date().toISOString(),
  })
}
