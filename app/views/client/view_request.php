<?php
$customPackages = json_decode($request['custom_packages'], true) ?? [];
$items = $customPackages['items'] ?? [];

$title = "Negotiation Thread - e.PLAN";
$activePage = "requests";
$extra_head = '
<style>
.negotiation-layout { display: grid; grid-template-columns: 400px 1fr; gap: 30px; align-items: start; width: 100%; margin-top: 20px; }
.card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid var(--border-color); overflow: hidden; display: flex; flex-direction: column; }
.card-header { padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; }
.card-header h3 { margin: 0; font-size: 16px; color: #1e293b; font-weight: 700; }
.card-body { padding: 20px; }
.info-group { margin-bottom: 20px; }
.info-group:last-child { margin-bottom: 0; }
.info-group label { display: block; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
.info-group p { margin: 0; font-size: 15px; color: #0f172a; font-weight: 500; }

.status-badge { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; color: white; display: inline-block; text-transform: uppercase; }
.status-pending { background: #f59e0b; }
.status-negotiating { background: #3b82f6; }
.status-approved { background: #10b981; }
.status-rejected { background: #ef4444; }
.status-booked { background: #8b5cf6; }

.chat-card { display: flex; flex-direction: column; min-height: 500px; max-height: 700px; }
.chat-messages { flex: 1; overflow-y: auto; padding: 25px; background: #f1f5f9; display: flex; flex-direction: column; gap: 18px; }

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; transition: background 0.2s; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

.message { display: flex; max-width: 85%; }
.message-out { align-self: flex-end; }
.message-in { align-self: flex-start; }
.msg-bubble { padding: 12px 18px; border-radius: 16px; position: relative; }
.message-out .msg-bubble { background: #246A55; color: white; border-bottom-right-radius: 4px; box-shadow: 0 4px 12px rgba(36, 106, 85, 0.15); }
.message-in .msg-bubble { background: white; color: #1e293b; border-bottom-left-radius: 4px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
.msg-text { font-size: 14.5px; line-height: 1.6; }
.msg-time { font-size: 10px; opacity: 0.8; margin-top: 8px; text-align: right; font-weight: 500; }

.chat-input-area { padding: 20px; background: white; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; }
.chat-input-wrapper { display: flex; gap: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 6px 6px 6px 18px; transition: all 0.2s; align-items: center; }
.chat-input-wrapper:focus-within { border-color: #246A55; box-shadow: 0 0 0 3px rgba(36, 106, 85, 0.1); background: white; }
.chat-input-wrapper textarea { flex: 1; border: none; background: transparent; padding: 10px 0; font-family: inherit; font-size: 14.5px; outline: none; resize: none; color: #1e293b; max-height: 120px; line-height: 1.5; }
.btn-send { width: 40px; height: 40px; border-radius: 12px; background: #246A55; color: white; border: none; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
.btn-send:hover { transform: scale(1.05); background: #1a4d3e; box-shadow: 0 4px 12px rgba(36, 106, 85, 0.2); }

.item-list { list-style: none; padding: 0; margin: 15px 0 0 0; display: flex; flex-direction: column; gap: 12px; }
.item-pill { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; display: flex; align-items: flex-start; gap: 12px; font-size: 13px; transition: all 0.2s; }
.item-pill i { color: #10b981; font-size: 14px; margin-top: 2px; flex-shrink: 0; }
.item-pill-content { display: flex; flex-direction: column; gap: 4px; }
.item-pill-title { font-weight: 700; color: #1e293b; font-size: 13.5px; }
.item-pill-desc { font-size: 12px; color: #64748b; line-height: 1.4; }

.action-row { margin-top: 25px; padding-top: 20px; border-top: 1px solid #e2e8f0; width: 100%; }
.btn-book-now { display: block; width: 100%; padding: 14px; background: #246A55; color: white; text-align: center; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 15px; transition: all 0.2s; box-sizing: border-box; border: none; cursor: pointer; }
.btn-book-now:hover { background: #1a4d3e; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(36, 106, 85, 0.2); opacity: 0.95; }

@media (max-width: 900px) {
    .negotiation-layout { grid-template-columns: 1fr; }
}

@media (max-width: 576px) {
    .dashboard-container { padding: 15px; }
    .page-header-title { font-size: 24px !important; }
    .message { max-width: 95%; }
    .chat-messages { padding: 15px; }
    .chat-input-area { padding: 15px; }
    .item-pill { padding: 10px; }
}
</style>
';
include 'partials/header.php';
?>

<div class="dashboard-container">
    <div class="page-header-row clearfix">
        <div class="headings">
            <a href="/EventManagementSystem/public/client/requests" style="text-decoration: none; color: #64748b; font-size: 14px; display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                <i class="fa-solid fa-arrow-left"></i> Back to Requests
            </a>
            <h1 class="page-header-title">Negotiation Thread</h1>
            <p class="page-header-desc">Communicate with the organizer to finalize your custom package details.</p>
        </div>
    </div>

    <div class="negotiation-layout">
        <!-- Sidebar: Request Info -->
        <div class="card">
            <div class="card-header">
                <h3>Request Summary</h3>
                <span class="status-badge status-<?php echo strtolower($request['status']); ?>">
                    <?php echo strtoupper($request['status']); ?>
                </span>
            </div>
            <div class="card-body">
                <div class="info-group">
                    <label>Event Name</label>
                    <p><?php echo htmlspecialchars($request['event_title']); ?></p>
                </div>
                <div class="info-group">
                    <label>Proposed Date</label>
                    <p><?php echo !empty($request['event_date']) ? date('F d, Y', strtotime($request['event_date'])) : 'TBD'; ?></p>
                </div>
                <div class="info-group">
                    <label>Planned Quantity</label>
                    <p><?php echo !empty($request['guest_count']) ? htmlspecialchars($request['guest_count']) . ((strtolower($request['event_category'] ?? '') === 'concert') ? ' Tickets' : ' Guests') : 'TBD'; ?></p>
                </div>
                <div class="info-group">
                    <label>Proposed Total Price</label>
                    <p style="font-size: 20px; font-weight: 700; color: #246A55;">Rs. <?php echo number_format($request['proposed_price'], 2); ?></p>
                </div>
                
                <div class="info-group" style="margin-top: 30px;">
                    <label>Included in Custom Package</label>
                    <ul class="item-list">
                        <?php foreach ($items as $item): ?>
                            <li class="item-pill">
                                <i class="fa-solid fa-circle-check"></i>
                                <div class="item-pill-content">
                                    <span class="item-pill-title"><?php echo htmlspecialchars($item['title']); ?></span>
                                    <?php if (!empty($item['description'])): ?>
                                        <span class="item-pill-desc"><?php echo htmlspecialchars($item['description']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php if ($request['status'] === 'approved'): ?>
                    <div class="action-row">
                        <a href="javascript:void(0)" onclick="checkoutCustomEvent()" class="btn-book-now">
                            Accept & Book Now
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main: Chat -->
        <div class="card chat-card">
            <div class="card-header">
                <h3>Conversation with <?php echo htmlspecialchars($request['organizer_name']); ?></h3>
            </div>
            <div class="chat-messages custom-scrollbar" id="chatMessages">
                <?php if (empty($messages)): ?>
                    <div style="text-align: center; padding: 40px; color: #94a3b8;">
                        <i class="fa-solid fa-comments" style="font-size: 40px; margin-bottom: 15px; opacity: 0.3;"></i>
                        <p>No messages yet. Send a message to start the negotiation.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $msg): 
                        $isMe = ($msg['sender_id'] == $_SESSION['user_id']);
                    ?>
                        <div class="message <?php echo $isMe ? 'message-out' : 'message-in'; ?>">
                            <div class="msg-bubble">
                                <div class="msg-text"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
                                <div class="msg-time"><?php echo date('M d, H:i', strtotime($msg['created_at'])); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="chat-input-area">
                <form id="sendMessageForm">
                    <div class="chat-input-wrapper">
                        <textarea id="messageInput" placeholder="Type your message here..." rows="1"></textarea>
                        <button type="submit" class="btn-send">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Auto-expand textarea
        const textarea = document.getElementById('messageInput');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Polling for real-time messages
        let lastMessageCount = <?php echo count($messages); ?>;
        function pollMessages() {
            if (!window.emsApi) return;
            
            window.emsApi.apiFetch(`/api/v1/custom-events/requests/<?php echo $request['id']; ?>`)
            .then(res => {
                const messages = res.data && res.data.messages ? res.data.messages : [];
                if (messages.length > lastMessageCount) {
                    const newMessages = messages.slice(lastMessageCount);
                    newMessages.forEach(msg => {
                        const isMe = (msg.sender_id == <?php echo $_SESSION['user_id']; ?>);
                        const msgHtml = `
                            <div class="message ${isMe ? 'message-out' : 'message-in'}">
                                <div class="msg-bubble">
                                    <div class="msg-text">${escapeHtml(msg.message).replace(/\n/g, '<br>')}</div>
                                    <div class="msg-time">${formatDate(msg.created_at)}</div>
                                </div>
                            </div>
                        `;
                        chatMessages.insertAdjacentHTML('beforeend', msgHtml);
                    });
                    lastMessageCount = messages.length;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    
                    // Remove empty state if present
                    const emptyState = chatMessages.querySelector('div[style*="text-align: center"]');
                    if (emptyState) emptyState.remove();
                }
            })
            .catch(err => console.error('Poll error:', err));
        }
        // Poll every 5 seconds
        setInterval(pollMessages, 5000);

        function escapeHtml(unsafe) {
            return (unsafe || "").toString()
                .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', {month:'short', day:'numeric'}) + ', ' + 
                   date.toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit', hour12:false});
        }

        const sendMessageForm = document.getElementById('sendMessageForm');
        if (sendMessageForm) {
            sendMessageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const input = document.getElementById('messageInput');
                const message = input.value.trim();
                if (!message) return;

                const submitBtn = this.querySelector('.btn-send');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';

                window.emsApi.apiFetch(`/api/v1/custom-events/requests/<?php echo $request['id']; ?>/message`, {
                    method: 'POST',
                    body: { message: message }
                })
                .then(res => {
                    if (res.success) {
                        input.value = '';
                        input.style.height = 'auto';
                        pollMessages();
                    } else {
                        alert(res.message || 'Failed to send message.');
                    }
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
                });
            });

            // Enter to send
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessageForm.dispatchEvent(new Event('submit'));
                }
            });
        }
    });

    function checkoutCustomEvent() {
        window.location.href = `/EventManagementSystem/public/client/book?id=<?php echo $request['group_event_id']; ?>&package=<?php echo $request['base_package_tier']; ?>&request_id=<?php echo $request['id']; ?>`;
    }
</script>

<?php include 'partials/footer.php'; ?>
