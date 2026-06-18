'use client'

import { useCallback, useEffect, useState } from 'react'

type Message = { role: 'user' | 'assistant'; content: string }

export default function PersonaChatWidget() {
  const [open, setOpen] = useState(false)
  const [messages, setMessages] = useState<Message[]>([])
  const [input, setInput] = useState('')
  const [loading, setLoading] = useState(false)
  const [welcome, setWelcome] = useState('Hi! I help startups plan mobile and web apps. What are you building?')

  useEffect(() => {
    fetch('/api/leads')
      .then((r) => r.json())
      .then((d) => {
        if (d.welcome) setWelcome(d.welcome)
        setMessages([{ role: 'assistant', content: d.welcome || welcome }])
      })
      .catch(() => {
        setMessages([{ role: 'assistant', content: welcome }])
      })
  }, [])

  const sendMessage = useCallback(async () => {
    const text = input.trim()
    if (!text || loading) return

    const nextMessages: Message[] = [...messages, { role: 'user', content: text }]
    setMessages(nextMessages)
    setInput('')
    setLoading(true)

    try {
      const res = await fetch('/api/persona/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ messages: nextMessages }),
      })
      const data = await res.json()
      const reply = data.reply || 'Thanks! Could you share your email so we can follow up?'
      setMessages([...nextMessages, { role: 'assistant', content: reply }])

      const emailMatch = text.match(/[\w.-]+@[\w.-]+\.\w+/)
      const phoneMatch = text.match(/\+?[\d\s-]{10,}/)
      if (emailMatch || phoneMatch || /consultation|contact|quote|call me/i.test(text)) {
        await fetch('/api/leads', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            email: emailMatch?.[0],
            phone: phoneMatch?.[0],
            requirement: nextMessages.filter((m) => m.role === 'user').map((m) => m.content).join('\n'),
            source_page: window.location.pathname,
            transcript: nextMessages,
          }),
        })
      }
    } catch {
      setMessages([...nextMessages, { role: 'assistant', content: 'Sorry, something went wrong. Please try again or contact us at info@techweblabs.com' }])
    } finally {
      setLoading(false)
    }
  }, [input, loading, messages])

  return (
    <div className="persona-widget">
      {open && (
        <div className="persona-panel">
          <div className="persona-header">TechWebLabs Assistant</div>
          <div className="persona-messages">
            {messages.map((m, i) => (
              <div key={i} className={`persona-msg ${m.role}`}>{m.content}</div>
            ))}
            {loading && <div className="persona-msg assistant">Typing…</div>}
          </div>
          <div className="persona-input-row">
            <input
              value={input}
              onChange={(e) => setInput(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && sendMessage()}
              placeholder="Type your message…"
              disabled={loading}
            />
            <button type="button" onClick={sendMessage} disabled={loading}>Send</button>
          </div>
        </div>
      )}
      <button type="button" className="persona-toggle" onClick={() => setOpen(!open)} aria-label="Chat with us">
        {open ? '×' : '💬'}
      </button>
    </div>
  )
}
