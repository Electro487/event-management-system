<!-- ═══ e-Plan Chatbot Widget ═══ -->
<link rel="stylesheet" href="/EventManagementSystem/public/assets/css/chatbot.css?v=<?php echo time(); ?>">

<div id="eplan-chat-bubble" onclick="toggleChat()" title="Chat with e-Plan Assistant">
    <svg class="bubble-icon" width="26" height="26" viewBox="0 0 24 24" fill="white">
        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
    </svg>
    <svg class="bubble-close" width="22" height="22" viewBox="0 0 24 24" fill="white">
        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
    </svg>
</div>

<div id="eplan-chat-window" style="display:none">
    <div id="chat-header">
        <div class="chat-header-info">
            <div class="chat-avatar">🤖</div>
            <div class="chat-header-text">
                <strong>e-Plan Assistant</strong>
                <span id="chat-status">Online</span>
            </div>
        </div>
        <button onclick="toggleChat()" title="Close chat">&times;</button>
    </div>
    <div id="chat-messages"></div>
    <div id="chat-input-area">
        <input id="chat-input" type="text" placeholder="Ask me anything about events…" maxlength="1000"
               onkeydown="if(event.key==='Enter'){sendChatMessage()}" autocomplete="off" />
        <button id="chat-send-btn" onclick="sendChatMessage()" title="Send message">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
            </svg>
        </button>
    </div>
    <div id="chat-powered">Powered by <span>OpenRouter AI</span></div>
</div>

<script src="/EventManagementSystem/public/assets/js/chatbot.js?v=<?php echo time(); ?>"></script>
