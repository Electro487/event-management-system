<?php
$title = "Manage Custom Request - e.PLAN";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Manage Custom Request - e.PLAN'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
</head>
<body>
<?php
$activePage = 'requests';
include_once __DIR__ . '/partials/sidebar.php';

$customPackages = json_decode($request['custom_packages'], true) ?? [];
$items = $customPackages['items'] ?? [];
?>

<div class="split-layout">
    <div class="main-content">
        <div class="content-header" style="margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
            <div style="display: flex; flex-direction: column; gap: 5px;">
                <a href="/EventManagementSystem/public/organizer/requests" style="display:inline-block; text-decoration:none; color:var(--primary-color); font-weight: 600; font-size: 14px; margin-bottom: 5px;"><i class="fa-solid fa-arrow-left"></i> Back to Requests</a>
                <h1 class="page-title" style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.5px;">Manage Custom Request</h1>
            </div>
            <div style="background: #f1f5f9; padding: 12px 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; gap: 20px; align-items: center;">
                <div style="display: flex; flex-direction: column;">
                    <span style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase;">Client</span>
                    <span style="font-weight: 700; color: #1e293b; font-size: 15px;"><?php echo htmlspecialchars($request['client_name']); ?></span>
                </div>
                <div style="width: 1px; height: 30px; background: #cbd5e1;"></div>
                <div style="display: flex; flex-direction: column;">
                    <span style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase;">Event</span>
                    <span style="font-weight: 700; color: #1e293b; font-size: 15px;"><?php echo htmlspecialchars($request['event_title']); ?></span>
                </div>
            </div>
        </div>

        <div class="negotiation-layout">
            <!-- Left Column: Package Details & Editing -->
            <div class="package-details-col">
                <div class="card">
                    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                        <h3>Edit Proposed Package</h3>
                        <span class="status-badge status-<?php echo strtolower($request['status']); ?>"><?php echo ucfirst($request['status']); ?></span>
                    </div>
                    <div class="card-body" style="padding: 25px;">
                        <?php if (in_array(strtolower($request['status']), ['pending', 'negotiating'])) : ?>
                        <form id="updateOfferForm">
                            <div class="form-group" style="margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px;">
                                <label style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8; font-weight: 800; display: block; margin-bottom: 10px;">PROPOSED PRICE (NPR)</label>
                                <div style="position: relative;">
                                    <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b; font-weight: 700;">Rs.</span>
                                    <input type="number" id="proposedPrice" value="<?php echo htmlspecialchars($request['proposed_price']); ?>" required style="font-size: 24px; font-weight: 800; padding: 15px 15px 15px 45px; border-radius: 12px; width: 100%; border: 1px solid #e2e8f0; background: #f8fafc; color: #246A55; transition: all 0.2s ease;">
                                </div>
                            </div>

                            <div class="items-list-container">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                                    <label style="font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8;">Package Items</label>
                                    <button type="button" class="btn-publish" onclick="openAddSectionModal()" style="padding: 8px 15px; font-size: 12px; border-radius: 8px; background: white; color: #1e293b; border: 1px solid #e2e8f0; font-weight: 700; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: all 0.2s;">
                                        <i class="fa-solid fa-plus" style="margin-right: 5px; color: #246A55;"></i> Add Item
                                    </button>
                                </div>
                                <div class="items-list custom-scrollbar" id="custom-items-list" style="display: flex; flex-direction: column; gap: 12px; max-height: 250px; overflow-y: auto; padding-right: 8px; margin-right: -8px;">
                                    <?php foreach ($items as $idx => $item): ?>
                                        <div class="item-row" data-id="<?php echo $idx; ?>" style="background: white; padding: 15px; border-radius: 12px; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                            <span class="drag-handle" style="cursor:grab; color:#cbd5e1; font-size: 18px;">⠿</span>
                                            <div class="item-content" style="flex:1;">
                                                <strong class="item-title" style="display:block; font-size:15px; color: #1e293b; font-weight: 700;"><?php echo htmlspecialchars($item['title']); ?></strong>
                                                <p class="item-desc" style="margin:5px 0 0; font-size:12px; color:#64748b; line-height: 1.5;"><?php echo htmlspecialchars($item['description']); ?></p>
                                            </div>
                                            <div class="item-actions" style="display:flex; gap:10px;">
                                                <button type="button" onclick="openEditModal(this.closest('.item-row'))" style="background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; color: #64748b; transition: all 0.2s;"><i class="fa-solid fa-pen-to-square"></i></button>
                                                <button type="button" onclick="this.closest('.item-row').remove()" style="background: #fef2f2; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; color: #ef4444; transition: all 0.2s;"><i class="fa-solid fa-trash-can"></i></button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div style="margin-top: 35px; display:flex; gap:12px; flex-direction:column;">
                                <button type="submit" class="btn-primary" style="width: 100%; padding: 16px; border-radius: 12px; font-weight: 800; font-size: 15px; background: #246A55; border: none; box-shadow: 0 4px 12px rgba(36, 106, 85, 0.25);">
                                    Update Offer Profile
                                </button>
                                <div style="display: flex; gap: 12px;">
                                    <button type="button" class="btn-secondary" onclick="approveOffer()" style="flex: 1; padding: 14px; background: #10b981; color: white; border: none; font-weight: 700; border-radius: 12px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
                                        <i class="fa-solid fa-check-circle" style="margin-right: 8px;"></i> Approve
                                    </button>
                                    <button type="button" class="btn-secondary" onclick="rejectOffer()" style="flex: 1; padding: 14px; background: #ef4444; color: white; border: none; font-weight: 700; border-radius: 12px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);">
                                        <i class="fa-solid fa-times-circle" style="margin-right: 8px;"></i> Reject
                                    </button>
                                </div>
                            </div>
                        </form>
                        <?php else: ?>
                            <div class="info-group">
                                <label>Proposed Price</label>
                                <p style="font-size: 24px; font-weight: 700; color: var(--primary-color);">Rs. <?php echo number_format($request['proposed_price'], 2); ?></p>
                            </div>
                            <div class="items-list" style="margin-top: 20px;">
                                <label style="font-weight: 600; font-size: 14px; color: #64748b; margin-bottom: 10px; display: block;">Items Included</label>
                                <?php foreach ($items as $item): ?>
                                    <div style="padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 10px; background: #f8fafc;">
                                        <strong style="display:block; color:#1e293b;"><?php echo htmlspecialchars($item['title']); ?></strong>
                                        <p style="margin:4px 0 0; font-size:13px; color:#64748b;"><?php echo htmlspecialchars($item['description']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Chat Box -->
            <div class="chat-col">
                <div class="card chat-card" style="height: 700px;">
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

<!-- Modals for Editing Package Items -->
<div class="modal-overlay" id="addSectionModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center;">
    <div class="modal-box" style="background:white; padding:30px; border-radius:12px; width:400px; max-width:90%;">
        <h3 style="margin-top:0;">Add New Item</h3>
        <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; font-size:12px; margin-bottom:5px;">ITEM TITLE</label>
            <input type="text" id="newSectionTitle" placeholder="e.g. Extra Lighting Setup" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;">
            <p id="addSectionError" style="color: #ef4444; font-size: 11px; margin-top: 5px; display: none;">Title is required</p>
        </div>
        <div class="form-group" style="margin-bottom:20px;">
            <label style="display:block; font-size:12px; margin-bottom:5px;">ITEM DESCRIPTION</label>
            <input type="text" id="newSectionDesc" placeholder="Brief description of the item" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;">
        </div>
        <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:10px;">
            <button type="button" class="btn-cancel" onclick="document.getElementById('addSectionModal').style.display='none'" style="padding:8px 16px; border-radius:6px; border:1px solid #e2e8f0; background:white; cursor:pointer;">Cancel</button>
            <button type="button" class="btn-publish" onclick="confirmAddSection()" style="padding:8px 16px; border-radius:6px; background:var(--primary-color); color:white; border:none; cursor:pointer;">Add Item</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="editSectionModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center;">
    <div class="modal-box" style="background:white; padding:30px; border-radius:12px; width:400px; max-width:90%;">
        <h3 style="margin-top:0;">Edit Item</h3>
        <div class="form-group" style="margin-bottom:15px;">
            <label style="display:block; font-size:12px; margin-bottom:5px;">ITEM TITLE</label>
            <input type="text" id="editSectionTitle" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;">
            <p id="editSectionError" style="color: #ef4444; font-size: 11px; margin-top: 5px; display: none;">Title is required</p>
        </div>
        <div class="form-group" style="margin-bottom:20px;">
            <label style="display:block; font-size:12px; margin-bottom:5px;">ITEM DESCRIPTION</label>
            <input type="text" id="editSectionDesc" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;">
        </div>
        <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:10px;">
            <button type="button" class="btn-cancel" onclick="document.getElementById('editSectionModal').style.display='none'" style="padding:8px 16px; border-radius:6px; border:1px solid #e2e8f0; background:white; cursor:pointer;">Cancel</button>
            <button type="button" class="btn-publish" onclick="confirmEditSection()" style="padding:8px 16px; border-radius:6px; background:var(--primary-color); color:white; border:none; cursor:pointer;">Save Changes</button>
        </div>
    </div>
</div>

<style>
.split-layout { display: flex; width: 100%; min-height: 100vh; }
.main-content { flex: 1; padding: 40px; background: #f8fafc; width: 100%; overflow-x: hidden; }
.negotiation-layout { display: grid; grid-template-columns: 380px 1fr; gap: 30px; align-items: stretch; width: 100%; max-width: none; }
.card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid var(--border-color); overflow: hidden; display: flex; flex-direction: column; height: 100%; }
.card-header { padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
.card-header h3 { margin: 0; font-size: 16px; color: #1e293b; }
.card-body { padding: 20px; flex: 1; display: flex; flex-direction: column; }

.status-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; color: white; display: inline-block; }
.status-pending { background: #f59e0b; }
.status-negotiating { background: #3b82f6; }
.status-approved { background: #10b981; }
.status-rejected { background: #ef4444; }
.status-booked { background: #8b5cf6; }

.chat-card { display: flex; flex-direction: column; height: 700px; }
.chat-messages { flex: 1; overflow-y: auto; padding: 20px; background: #f1f5f9; display: flex; flex-direction: column; gap: 15px; }
.message { display: flex; max-width: 80%; }
.message-out { align-self: flex-end; }
.message-in { align-self: flex-start; }
.msg-bubble { padding: 12px 16px; border-radius: 12px; position: relative; }
.message-out .msg-bubble { background: #246A55; color: white; border-bottom-right-radius: 4px; box-shadow: 0 4px 12px rgba(36, 106, 85, 0.15); }
.message-in .msg-bubble { background: white; color: #1e293b; border-bottom-left-radius: 4px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
.msg-text { font-size: 14px; line-height: 1.5; }
.msg-time { font-size: 10px; opacity: 0.8; margin-top: 6px; text-align: right; font-weight: 500; }
.chat-input-area { padding: 20px; background: white; border-top: 1px solid #e2e8f0; }

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; transition: background 0.2s; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

@media (max-width: 900px) {
    .negotiation-layout { grid-template-columns: 1fr; }
}

@media (max-width: 576px) {
    .main-content { padding: 20px; }
    .content-header > div:last-child {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 10px !important;
        width: 100%;
    }
    .content-header > div:last-child > div[style*="width: 1px"] {
        width: 100% !important;
        height: 1px !important;
    }
    .items-list-container > div:first-child {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 10px;
    }
    .item-row {
        flex-direction: column;
        align-items: flex-start !important;
    }
    .item-actions {
        width: 100%;
        justify-content: flex-end;
        margin-top: 5px;
    }
}

@media (max-width: 380px) {
    .main-content { padding: 10px !important; }
    .card-body { padding: 15px !important; }
    .content-header { gap: 10px !important; margin-bottom: 20px !important; }
    .page-title { font-size: 24px !important; }
    
    /* Make Approve/Reject buttons stack */
    form .btn-secondary {
        width: 100%;
    }
    form > div:last-child > div {
        flex-direction: column !important;
    }
    
    .modal-box { padding: 20px !important; }
    #proposedPrice { font-size: 18px !important; }
}
</style>

<script>
    // Chat logic
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

    // Editable Items Logic
    let currentEditRow = null;

    function openAddSectionModal() {
        document.getElementById('newSectionTitle').value = '';
        document.getElementById('newSectionDesc').value = '';
        document.getElementById('addSectionError').style.display = 'none';
        document.getElementById('addSectionModal').style.display = 'flex';
    }

    function confirmAddSection() {
        const title = document.getElementById('newSectionTitle').value.trim();
        const desc = document.getElementById('newSectionDesc').value.trim();
        const errorEl = document.getElementById('addSectionError');
        
        if (!title) {
            errorEl.style.display = 'block';
            return;
        }
        errorEl.style.display = 'none';

        const list = document.getElementById('custom-items-list');
        const row = document.createElement('div');
        row.className = 'item-row';
        row.style.cssText = 'background: white; padding: 15px; border-radius: 12px; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: all 0.2s ease; margin-bottom: 2px;';
        row.innerHTML = `
            <span class="drag-handle" style="cursor:grab; color:#cbd5e1; font-size: 18px;">⠿</span>
            <div class="item-content" style="flex:1;">
                <strong class="item-title" style="display:block; font-size:15px; color: #1e293b; font-weight: 700;">${escapeHtml(title)}</strong>
                <p class="item-desc" style="margin:5px 0 0; font-size:12px; color:#64748b; line-height: 1.5;">${escapeHtml(desc)}</p>
            </div>
            <div class="item-actions" style="display:flex; gap:10px;">
                <button type="button" onclick="openEditModal(this.closest('.item-row'))" style="background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; color: #64748b; transition: all 0.2s;"><i class="fa-solid fa-pen-to-square"></i></button>
                <button type="button" onclick="this.closest('.item-row').remove()" style="background: #fef2f2; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; color: #ef4444; transition: all 0.2s;"><i class="fa-solid fa-trash-can"></i></button>
            </div>
        `;
        list.appendChild(row);
        document.getElementById('addSectionModal').style.display = 'none';
        makeListSortable();
    }

    function openEditModal(row) {
        currentEditRow = row;
        document.getElementById('editSectionTitle').value = row.querySelector('.item-title').innerText;
        document.getElementById('editSectionDesc').value = row.querySelector('.item-desc').innerText;
        document.getElementById('editSectionError').style.display = 'none';
        document.getElementById('editSectionModal').style.display = 'flex';
    }

    function confirmEditSection() {
        if (!currentEditRow) return;
        const title = document.getElementById('editSectionTitle').value.trim();
        const desc = document.getElementById('editSectionDesc').value.trim();
        const errorEl = document.getElementById('editSectionError');
        
        if (!title) {
            errorEl.style.display = 'block';
            return;
        }
        errorEl.style.display = 'none';

        currentEditRow.querySelector('.item-title').innerText = title;
        currentEditRow.querySelector('.item-desc').innerText = desc;
        document.getElementById('editSectionModal').style.display = 'none';
    }

    function escapeHtml(unsafe) {
        return (unsafe || "").toString()
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    // Sortable JS Init
    function makeListSortable() {
        <?php if(file_exists(dirname(__DIR__, 3).'/public/assets/js/Sortable.min.js')): ?>
        if (typeof Sortable !== 'undefined') {
            const list = document.getElementById('custom-items-list');
            if (list) {
                new Sortable(list, {
                    handle: '.drag-handle',
                    animation: 150
                });
            }
        }
        <?php endif; ?>
    }
    document.addEventListener('DOMContentLoaded', makeListSortable);

    // Form Update Submission
    const updateOfferForm = document.getElementById('updateOfferForm');
    if (updateOfferForm) {
        updateOfferForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const items = [];
            document.querySelectorAll('#custom-items-list .item-row').forEach(row => {
                items.push({
                    title: row.querySelector('.item-title').innerText,
                    description: row.querySelector('.item-desc').innerText
                });
            });

            const customPackagesJSON = JSON.stringify({ items: items });
            const data = {
                proposed_price: document.getElementById('proposedPrice').value,
                custom_packages: customPackagesJSON
            };

            window.emsApi.apiFetch(`/api/v1/custom-events/requests/<?php echo $request['id']; ?>/offer`, {
                method: 'POST',
                body: data,
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => {
                if (res.success) {
                    alert('Offer updated and sent to client!');
                    location.reload();
                } else {
                    alert(res.message || 'Failed to update offer.');
                }
            });
        });
    }

    function approveOffer() {
        if (!confirm('Are you sure you want to approve this proposal? No more negotiations can happen after approval.')) return;
        
        window.emsApi.apiFetch(`/api/v1/custom-events/requests/<?php echo $request['id']; ?>/status`, {
            method: 'POST',
            body: { status: 'approved' },
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => {
            if (res.success) {
                alert('Request approved!');
                location.reload();
            } else {
                alert(res.message || 'Error approving.');
            }
        });
    }

    function rejectOffer() {
        if (!confirm('Are you sure you want to reject this request?')) return;
        
        window.emsApi.apiFetch(`/api/v1/custom-events/requests/<?php echo $request['id']; ?>/status`, {
            method: 'POST',
            body: { status: 'rejected' },
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => {
            if (res.success) {
                alert('Request rejected.');
                location.reload();
            } else {
                alert(res.message || 'Error rejecting.');
            }
        });
    }

    function escapeHtml(unsafe) {
        return (unsafe || "").toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>

    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>
</html>
