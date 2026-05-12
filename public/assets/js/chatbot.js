/**
 * e.PLAN Chatbot Widget
 * Uses window.emsApi.apiFetch() for API calls (requires apiClient.js loaded first).
 */
(() => {
  const CHATBOT_API_PATH = '/api/v1/chat';
  const MAX_HISTORY = 20;

  let chatOpen    = false;
  let chatHistory = [];
  let isLoading   = false;

  // ── Toggle chat window ──────────────────────────────────────────────
  window.toggleChat = function () {
    chatOpen = !chatOpen;
    const win = document.getElementById('eplan-chat-window');
    const bubble = document.getElementById('eplan-chat-bubble');
    
    if (chatOpen) {
      win.style.display = 'flex';
      // Trigger animation
      requestAnimationFrame(() => win.classList.add('open'));
      bubble.classList.add('active');
      
      // Greet on first open
      if (chatHistory.length === 0) {
        addBotMessage('Hello! 👋 I\'m the e.PLAN Assistant. How can I help you today?');
      }
      document.getElementById('chat-input').focus();
    } else {
      win.classList.remove('open');
      bubble.classList.remove('active');
      // Wait for animation to finish then hide
      setTimeout(() => {
        if (!chatOpen) win.style.display = 'none';
      }, 250);
    }
  };

  // ── Send message ────────────────────────────────────────────────────
  window.sendChatMessage = async function () {
    if (isLoading) return;

    const input = document.getElementById('chat-input');
    const text  = input.value.trim();
    if (!text) return;

    input.value = '';
    isLoading   = true;
    document.getElementById('chat-send-btn').disabled = true;

    addUserMessage(text);
    const typingId = addTyping();

    try {
      // Use the existing emsApi client if available, otherwise fall back to raw fetch
      let data;
      if (window.emsApi && window.emsApi.apiFetch) {
        data = await window.emsApi.apiFetch(CHATBOT_API_PATH, {
          method: 'POST',
          body: { message: text, history: chatHistory.slice(-MAX_HISTORY) },
        });
      } else {
        // Fallback for pages without apiClient.js
        const base = '/EventManagementSystem/public';
        const res = await fetch(base + CHATBOT_API_PATH, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ message: text, history: chatHistory.slice(-MAX_HISTORY) }),
        });
        data = await res.json();
      }

      removeTyping(typingId);

      if (data.success === false) {
        const errMsg = data.error?.message || data.error || 'Something went wrong. Please try again.';
        addBotMessage(errMsg);
        return;
      }

      const reply = data.data?.reply || data.reply || 'Sorry, I couldn\'t generate a response.';
      addBotMessage(reply);
      chatHistory.push({ role: 'user',  text });
      chatHistory.push({ role: 'model', text: reply });

    } catch (err) {
      removeTyping(typingId);
      const msg = err?.message || 'Connection error. Please try again.';
      addBotMessage(msg);
      console.error('[e.PLAN Chatbot]', err);
    } finally {
      isLoading = false;
      document.getElementById('chat-send-btn').disabled = false;
      document.getElementById('chat-input').focus();
    }
  };

  // ── DOM helpers ─────────────────────────────────────────────────────
  function addUserMessage(text) {
    const d = document.createElement('div');
    d.className = 'chat-msg user';
    d.textContent = text;
    appendMsg(d);
  }

  function addBotMessage(text) {
    const d = document.createElement('div');
    d.className = 'chat-msg bot';
    // Parse basic markdown-style formatting
    d.innerHTML = formatBotMessage(text);
    appendMsg(d);
  }

  function formatBotMessage(text) {
    // Sanitize HTML first
    const div = document.createElement('div');
    div.textContent = text;
    let safe = div.innerHTML;
    // Bold: **text**
    safe = safe.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    // Line breaks
    safe = safe.replace(/\n/g, '<br>');
    return safe;
  }

  function addTyping() {
    const id = 'typing-' + Date.now();
    const d  = document.createElement('div');
    d.className = 'chat-msg bot typing';
    d.id = id;
    d.innerHTML = '<span class="typing-dots"><span></span><span></span><span></span></span> Thinking…';
    appendMsg(d);
    return id;
  }

  function removeTyping(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
  }

  function appendMsg(el) {
    const c = document.getElementById('chat-messages');
    c.appendChild(el);
    c.scrollTop = c.scrollHeight;
  }
})();
