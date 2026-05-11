<?php
$title = "Request Thread - e.PLAN";
$activePage = "requests";
require_once __DIR__ . '/partials/header.php';

$customPackages = json_decode($request['custom_packages'], true) ?? [];
$items = $customPackages['items'] ?? [];
?>

<div class="dashboard-container" style="padding: 40px; max-width: 1400px; margin: 0 auto; width: 100%;">
    <div class="main-content">
        <div class="content-header" style="margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
            <div style="display: flex; flex-direction: column; gap: 5px;">
                <a href="/EventManagementSystem/public/client/requests" style="display:inline-block; text-decoration:none; color:var(--primary-color); font-weight: 600; font-size: 14px; margin-bottom: 5px;"><i class="fa-solid fa-arrow-left"></i> Back to Requests</a>
                <h1 class="page-title" style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.5px;">Negotiation Thread</h1>
            </div>
            <div style="background: white; padding: 12px 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; gap: 20px; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="display: flex; flex-direction: column;">
                    <span style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase;">Organizer</span>
                    <span style="font-weight: 700; color: #1e293b; font-size: 15px;"><?php echo htmlspecialchars($request['organizer_name'] ?? 'System Admin'); ?></span>
                </div>
                <div style="width: 1px; height: 30px; background: #cbd5e1;"></div>
                <div style="display: flex; flex-direction: column;">
                    <span style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase;">Event</span>
                    <span style="font-weight: 700; color: #1e293b; font-size: 15px;"><?php echo htmlspecialchars($request['event_title']); ?></span>
                </div>
                <div style="margin-left: 10px;">
                    <a href="/EventManagementSystem/public/client/events/view?id=<?php echo $request['group_event_id']; ?>" target="_blank" class="btn-secondary" style="padding: 8px 12px; font-size: 12px; border-radius: 8px; text-decoration: none; border: 1px solid #e2e8f0; color: #64748b; font-weight: 600; transition: all 0.2s;">
                        <i class="fa-solid fa-external-link" style="margin-right: 5px;"></i> View Public Page
                    </a>
                </div>
            </div>
        </div>

        <div class="negotiation-layout">
            <!-- Left Column: Package Details -->
            <div class="package-details-col">
                <div class="card">
                    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                        <h3>Proposed Package</h3>
                        <span class="status-badge status-<?php echo strtolower($request['status']); ?>"><?php echo ucfirst($request['status']); ?></span>
                    </div>
                    <div class="card-body" style="padding: 25px;">
                        <div style="margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px;">
                            <div class="info-group" style="margin-bottom: 15px;">
                                <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 700; display: block; margin-bottom: 6px;">Base Tier</label>
                                <p style="font-weight: 700; color: #1e293b; font-size: 16px; margin: 0;"><?php echo ucfirst($request['base_package_tier']); ?></p>
                            </div>
                            <div class="info-group">
                                <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 700; display: block; margin-bottom: 6px;">Current Negotiated Price</label>
                                <p style="font-size: 28px; font-weight: 800; color: #246A55; margin: 0;">Rs. <?php echo number_format($request['proposed_price'], 2); ?></p>
                            </div>
                        </div>

                        <div class="info-group">
                            <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 700; display: block; margin-bottom: 15px;">Negotiated Items Included</label>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <?php foreach ($items as $item): ?>
                                    <div style="background: #f8fafc; padding: 14px 18px; border-radius: 12px; border: 1px solid #f1f5f9; position: relative; transition: all 0.2s ease;">
                                        <div style="font-weight: 700; color: #1e293b; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                                            <i class="fa-solid fa-circle-check" style="color: #246A55; font-size: 12px;"></i>
                                            <?php echo htmlspecialchars($item['title']); ?>
                                        </div>
                                        <?php if(!empty($item['description'])): ?>
                                            <div style="font-size: 12px; color: #64748b; margin-top: 6px; line-height: 1.5; padding-left: 20px;"><?php echo htmlspecialchars($item['description']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php if (strtolower($request['status']) === 'approved'): ?>
                            <div style="margin-top: 30px; padding: 25px; background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border: 1px solid #a7f3d0; border-radius: 15px; text-align: center; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.08);">
                                <div style="width: 50px; height: 50px; background: #10b981; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 20px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <h3 style="color: #065f46; margin: 0 0 8px; font-size: 18px; font-weight: 800;">Organizer Approved!</h3>
                                <p style="color: #047857; font-size: 13px; margin: 0 0 20px; line-height: 1.5;">The organizer has accepted your custom requirements. You can now finalize your booking.</p>
                                <button onclick="checkoutCustomEvent()" class="btn-primary" style="width: 100%; padding: 14px; border-radius: 10px; font-weight: 700; letter-spacing: 0.5px; border: none; background: #246A55; color: white; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(36, 106, 85, 0.2);">
                                    Proceed to Booking <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Chat Box -->
            <div class="chat-col">
                <div class="card chat-card" style="min-height: 500px; max-height: 800px;">
                    <div class="card-header">
                        <h3>Messages</h3>
                    </div>
                    <div class="chat-messages custom-scrollbar" id="chatMessages" style="flex: 1; overflow-y: auto;">
                        <?php foreach($messages as $msg): 
                            $isMe = ($msg['sender_id'] == $_SESSION['user_id']);
                        ?>
                            <div class="message <?php echo $isMe ? 'message-out' : 'message-in'; ?>">
                                <div class="msg-bubble">
                                    <div class="msg-text"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></div>
                                    <div class="msg-time"><?php echo date('M d, H:i', strtotime($msg['created_at'])); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if(empty($messages)): ?>
                            <p style="text-align:center; color:#94a3b8; font-size:14px; margin-top:20px;">No messages yet.</p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (in_array(strtolower($request['status']), ['pending', 'negotiating', 'approved'])): ?>
                        <div class="chat-input-area">
                            <form id="sendMessageForm" style="display:flex; gap:10px;">
                                <input type="text" id="messageInput" placeholder="Type your message..." required style="flex:1; padding:12px; border:1px solid #cbd5e1; border-radius:8px;">
                                <button type="submit" class="btn-primary" style="padding:12px 20px;"><i class="fa-solid fa-paper-plane"></i></button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.negotiation-layout { display: grid; grid-template-columns: 400px 1fr; gap: 30px; align-items: start; width: 100%; }
.card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid var(--border-color); overflow: hidden; display: flex; flex-direction: column; }
.card-header { padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
.card-header h3 { margin: 0; font-size: 16px; color: #1e293b; }
.card-body { padding: 20px; }
.info-group { margin-bottom: 15px; }
.info-group label { display: block; font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 4px; text-transform: uppercase; }
.info-group p { margin: 0; font-size: 15px; color: #0f172a; }

.status-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; color: white; display: inline-block; }
.status-pending { background: #f59e0b; }
.status-negotiating { background: #3b82f6; }
.status-approved { background: #10b981; }
.status-rejected { background: #ef4444; }
.status-booked { background: #8b5cf6; }

.chat-card { display: flex; flex-direction: column; min-height: 400px; max-height: 568px; }
.chat-messages { flex: 1; overflow-y: auto; padding: 20px; background: #f1f5f9; display: flex; flex-direction: column; gap: 15px; }

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; transition: background 0.2s; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.message { display: flex; max-width: 80%; }
.message-out { align-self: flex-end; }
.message-in { align-self: flex-start; }
.msg-bubble { padding: 12px 16px; border-radius: 12px; position: relative; }
.message-out .msg-bubble { background: #246A55; color: white; border-bottom-right-radius: 4px; box-shadow: 0 4px 12px rgba(36, 106, 85, 0.15); }
.message-in .msg-bubble { background: white; color: #1e293b; border-bottom-left-radius: 4px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
.msg-text { font-size: 14px; line-height: 1.5; }
.msg-time { font-size: 10px; opacity: 0.8; margin-top: 6px; text-align: right; font-weight: 500; }
.chat-input-area { padding: 20px; background: white; border-top: 1px solid #e2e8f0; }

@media (max-width: 900px) {
    .negotiation-layout { grid-template-columns: 1fr; }
}
</style>

<script>
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Polling for real-time messages
    let lastMessageCount = <?php echo count($messages); ?>;
    function pollMessages() {
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
                                <div class="msg-time">${new Date(msg.created_at).toLocaleDateString('en-US', {month:'short', day:'numeric'})}, ${new Date(msg.created_at).toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit', hour12:false})}</div>
                            </div>
                        </div>
                    `;
                    chatMessages.insertAdjacentHTML('beforeend', msgHtml);
                });
                lastMessageCount = messages.length;
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        })
        .catch(err => console.error('Poll error:', err));
    }
    // Poll every 3 seconds
    setInterval(pollMessages, 3000);

    function escapeHtml(unsafe) {
        return (unsafe || "").toString()
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    const sendMessageForm = document.getElementById('sendMessageForm');
    if (sendMessageForm) {
        sendMessageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('messageInput').value;
            if (!message) return;

            window.emsApi.apiFetch(`/api/v1/custom-events/requests/<?php echo $request['id']; ?>/message`, {
                method: 'POST',
                body: { message: message },
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => {
                if (res.success) {
                    document.getElementById('messageInput').value = '';
                    pollMessages();
                } else {
                    alert(res.message || 'Failed to send message.');
                }
            });
        });
    }

    function checkoutCustomEvent() {
        // First we mark it as booked and then send them to checkout
        // The normal app currently checks out from GET /book, but here we'll need to pass the request id
        // so it charges the right amount. For simplicity, we can redirect to a modified checkout flow.
        window.location.href = `/EventManagementSystem/public/client/book?id=<?php echo $request['group_event_id']; ?>&package=<?php echo $request['base_package_tier']; ?>&request_id=<?php echo $request['id']; ?>`;
    }
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
