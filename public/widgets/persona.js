(function () {
  var root = document.getElementById('twl-persona-root');
  if (!root) {
    root = document.createElement('div');
    root.id = 'twl-persona-root';
    document.body.appendChild(root);
  }

  var open = false;
  var messages = [];
  var welcome = 'Hi! I help startups plan mobile and web apps. What are you building?';

  var styles = document.createElement('style');
  styles.textContent =
    '.persona-widget{position:fixed;bottom:24px;right:24px;z-index:9999;font-family:Poppins,sans-serif}' +
    '.persona-toggle{width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);border:none;color:#fff;font-size:24px;cursor:pointer;box-shadow:0 4px 20px rgba(102,126,234,.4)}' +
    '.persona-panel{position:absolute;bottom:72px;right:0;width:360px;max-height:480px;background:#fff;border-radius:16px;box-shadow:0 8px 40px rgba(0,0,0,.15);display:flex;flex-direction:column;overflow:hidden}' +
    '.persona-header{background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:1rem;font-weight:600}' +
    '.persona-messages{flex:1;overflow-y:auto;padding:1rem;display:flex;flex-direction:column;gap:.75rem;max-height:320px}' +
    '.persona-msg{padding:.75rem 1rem;border-radius:12px;font-size:14px;line-height:1.5;max-width:90%}' +
    '.persona-msg.user{background:#667eea;color:#fff;align-self:flex-end}' +
    '.persona-msg.assistant{background:#f3f4f6;color:#374151;align-self:flex-start}' +
    '.persona-input-row{display:flex;padding:.75rem;border-top:1px solid #e5e7eb;gap:.5rem}' +
    '.persona-input-row input{flex:1;border:1px solid #d1d5db;border-radius:8px;padding:.5rem .75rem;font-size:14px}' +
    '.persona-input-row button{background:#667eea;color:#fff;border:none;border-radius:8px;padding:.5rem 1rem;cursor:pointer;font-size:14px}';
  document.head.appendChild(styles);

  var widget = document.createElement('div');
  widget.className = 'persona-widget';
  root.appendChild(widget);

  var panel = document.createElement('div');
  panel.className = 'persona-panel';
  panel.style.display = 'none';
  panel.innerHTML =
    '<div class="persona-header">TechWebLabs Assistant</div>' +
    '<div class="persona-messages"></div>' +
    '<div class="persona-input-row"><input type="text" placeholder="Type your message…" /><button type="button">Send</button></div>';
  widget.appendChild(panel);

  var toggle = document.createElement('button');
  toggle.className = 'persona-toggle';
  toggle.type = 'button';
  toggle.setAttribute('aria-label', 'Chat with us');
  toggle.textContent = '💬';
  widget.appendChild(toggle);

  var msgContainer = panel.querySelector('.persona-messages');
  var input = panel.querySelector('input');
  var sendBtn = panel.querySelector('button');

  function renderMessages() {
    if (!msgContainer) return;
    msgContainer.innerHTML = messages
      .map(function (m) {
        return '<div class="persona-msg ' + m.role + '">' + escapeHtml(m.content) + '</div>';
      })
      .join('');
    msgContainer.scrollTop = msgContainer.scrollHeight;
  }

  function escapeHtml(s) {
    return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function addMessage(role, content) {
    messages.push({ role: role, content: content });
    renderMessages();
  }

  fetch('/api/leads')
    .then(function (r) {
      return r.json();
    })
    .then(function (d) {
      welcome = d.welcome || welcome;
      addMessage('assistant', welcome);
    })
    .catch(function () {
      addMessage('assistant', welcome);
    });

  toggle.addEventListener('click', function () {
    open = !open;
    panel.style.display = open ? 'flex' : 'none';
    toggle.textContent = open ? '×' : '💬';
  });

  function sendMessage() {
    if (!input) return;
    var text = input.value.trim();
    if (!text) return;
    input.value = '';
    addMessage('user', text);

    fetch('/api/persona/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ messages: messages }),
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (d) {
        addMessage('assistant', d.reply || 'Thanks! Share your email for a free consultation.');
        var emailMatch = text.match(/[\w.-]+@[\w.-]+\.\w+/);
        if (emailMatch || /consultation|contact|quote/i.test(text)) {
          fetch('/api/leads', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              email: emailMatch ? emailMatch[0] : null,
              requirement: messages
                .filter(function (m) {
                  return m.role === 'user';
                })
                .map(function (m) {
                  return m.content;
                })
                .join('\n'),
              source_page: window.location.pathname,
              transcript: messages,
            }),
          });
        }
      })
      .catch(function () {
        addMessage('assistant', 'Sorry, please contact us at info@techweblabs.com');
      });
  }

  sendBtn.addEventListener('click', sendMessage);
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') sendMessage();
  });
})();
