<?php
$title = "Modify Package - " . htmlspecialchars($event['title']);
require_once dirname(__DIR__) . '/client/partials/header.php';

$tierNameMap = [
    'basic' => 'Basic Tier',
    'standard' => 'Standard Tier',
    'premium' => 'Premium Tier'
];

$tierName = $tierNameMap[$packageTier] ?? 'Custom Package';

// Decode event packages to populate the defaults
$eventPackages = json_decode($event['packages'], true) ?? [];
$selectedPackageData = $eventPackages[$packageTier] ?? [];
$pkgItems = $selectedPackageData['items'] ?? [];
$pkgPrice = $selectedPackageData['price'] ?? ($selectedPackageData['price_range'] ?? '');

// Image path logic
if (!empty($event['image_path'])) {
    $eventImage = ($event['image_path'][0] === '/') ? $event['image_path'] : '/EventManagementSystem/public/assets/images/events/' . $event['image_path'];
} else {
    $eventImage = '/EventManagementSystem/public/assets/images/placeholder.jpg';
}
?>

<link rel="stylesheet" href="/EventManagementSystem/public/assets/css/create-event.css">
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #246A55 0%, #1a4d3e 100%);
        --accent-glow: 0 0 15px rgba(36, 106, 85, 0.15);
    }

    * { box-sizing: border-box; }

    body { background-color: #f8fafc; }

    .modify-container {
        max-width: 1100px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .form-section {
        background: #fff;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 32px;
        box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);
        border: 1px solid #eef2f6;
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .event-details-card {
        display: flex;
        gap: 40px;
        align-items: flex-start;
        background: #fcfdfe;
        padding: 24px;
        border-radius: 14px;
        border: 1px solid #f1f5f9;
        margin-top: -10px;
    }

    .event-thumbnail {
        width: 220px;
        height: 140px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    }

    .package-card { 
        margin-bottom: 0; 
        border: 1px solid #e2e8f0; 
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .package-body {
        padding: 24px;
    }

    .item-row {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 16px 20px;
        border-radius: 12px;
        background: #246A55;
        margin-bottom: 12px;
        color: white;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(36, 106, 85, 0.1);
    }

    .drag-handle {
        cursor: grab;
        opacity: 0.6;
        font-size: 18px;
        flex-shrink: 0;
    }

    .item-content {
        flex: 1;
    }

    .item-content strong {
        display: block;
        font-size: 15px;
        margin-bottom: 2px;
        font-weight: 700;
    }

    .item-content p {
        font-size: 13px;
        opacity: 0.8;
        margin: 0;
        line-height: 1.4;
    }

    .item-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .icon-action-btn {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
    }

    .icon-action-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.1);
    }

    .edit-item-btn:hover { color: #4ade80; }
    .delete-item-btn:hover { color: #f87171; }

    .btn-publish {
        background: var(--primary-gradient);
        border: none;
        box-shadow: 0 4px 12px rgba(36, 106, 85, 0.2);
        color: white;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-publish:hover {
        background: #1a4d3e;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(36, 106, 85, 0.3);
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-box {
        background: white;
        padding: 40px;
        border-radius: 20px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.25);
        margin: 20px;
    }

    .modal-box h3 { 
        margin-top: 0; 
        margin-bottom: 24px; 
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
    }

    .modal-box .form-group {
        margin-bottom: 20px;
    }

    .modal-box label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: block;
    }

    .modal-box input[type="text"] {
        padding: 14px 18px;
        border-radius: 12px;
        border: 1px solid #e1e7ed;
        background-color: #f8fafc;
        font-size: 15px;
        width: 100%;
        transition: all 0.2s ease;
        color: #1e293b;
    }

    .modal-box input[type="text"]:focus {
        background-color: #fff;
        border-color: #246A55;
        box-shadow: 0 0 0 4px rgba(36, 106, 85, 0.1);
        outline: none;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 32px;
    }

    .modal-actions .btn-cancel {
        padding: 12px 24px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-weight: 700;
        font-size: 14px;
    }

    .modal-actions .btn-publish {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
    }

    input[type="number"], textarea {
        transition: all 0.2s ease;
    }

    input[type="number"]:focus, textarea:focus {
        border-color: #246A55;
        box-shadow: 0 0 0 4px rgba(36, 106, 85, 0.1);
        outline: none;
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 24px;
        position: relative;
        padding-left: 15px;
    }

    .section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 20px;
        background: #246A55;
        border-radius: 2px;
    }
</style>

<div class="modify-container">
    <div style="margin-bottom: 30px;">
        <a href="/EventManagementSystem/public/client/events/view?id=<?php echo $event['id']; ?>" class="btn-cancel" style="display:inline-block; margin-bottom: 20px; text-decoration: none; padding: 10px 20px;">
            <i class="fa-solid fa-arrow-left"></i> Back to Event
        </a>
        <h1 style="font-size: 28px; color: #1e293b; margin-bottom: 8px;">Request Package Customization</h1>
        <p style="color: #64748b; font-size: 15px;">Modify the <?php echo $tierName; ?> package to suit your specific needs. Submit your proposed negotiated price to the organizer.</p>
    </div>

    <form id="modifyPackageForm">
        <input type="hidden" id="group_event_id" value="<?php echo htmlspecialchars($event['id']); ?>">
        <input type="hidden" id="organizer_id" value="<?php echo htmlspecialchars($event['organizer_id']); ?>">
        <input type="hidden" id="base_package_tier" value="<?php echo htmlspecialchars($packageTier); ?>">

        <!-- Event Summary (Read-Only) -->
        <div class="form-section">
            <h2 class="section-title">Event Details</h2>
            <div class="event-details-card">
                <img src="<?php echo htmlspecialchars($eventImage); ?>" class="event-thumbnail" alt="Event Thumbnail">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; flex: 1;">
                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: #64748b; letter-spacing: 1px; text-transform: uppercase;">Title</label>
                        <div style="font-size: 18px; font-weight: 600; color: #0f172a; margin-top: 4px;"><?php echo htmlspecialchars($event['title']); ?></div>
                    </div>
                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: #64748b; letter-spacing: 1px; text-transform: uppercase;">Category</label>
                        <div style="font-size: 16px; font-weight: 500; color: #0f172a; margin-top: 4px;"><?php echo htmlspecialchars(ucfirst($event['category'])); ?></div>
                    </div>
                    <div style="grid-column: span 2;">
                        <label style="font-size: 11px; font-weight: 700; color: #64748b; letter-spacing: 1px; text-transform: uppercase;">Venue</label>
                        <div style="font-size: 16px; font-weight: 500; color: #0f172a; margin-top: 4px;"><?php echo htmlspecialchars($event['venue_name'] . ', ' . $event['venue_location']); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package Curation (Editable) -->
        <div class="form-section">
            <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
                <h2 class="section-title" style="margin-bottom:0;">Customize Package Items</h2>
                <button type="button" class="btn-publish" onclick="openAddSectionModal()" style="padding: 10px 20px; font-size: 14px; border-radius: 8px;">+ Add New Item</button>
            </div>
            
            <div class="package-card tier-highlight" style="display: block;">
                <div class="package-body">
                    <div class="items-list" id="custom-items-list">
                        <?php foreach ($pkgItems as $idx => $item): ?>
                            <div class="item-row" data-id="<?php echo $idx; ?>">
                                <span class="drag-handle">⠿</span>
                                <div class="item-content">
                                    <strong class="item-title"><?php echo htmlspecialchars($item['title']); ?></strong>
                                    <p class="item-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                                </div>
                                <div class="item-actions">
                                    <button type="button" class="icon-action-btn edit-item-btn" onclick="openEditModal(this.closest('.item-row'))" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button type="button" class="icon-action-btn delete-item-btn" onclick="this.closest('.item-row').remove()" title="Delete"><i class="fa-solid fa-trash-can"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Proposal Section -->
        <div class="form-section">
            <h2 class="section-title" style="margin-bottom:0;">Your Proposal</h2>
            
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <div class="form-group">
                    <label style="margin-bottom: 8px; display: block;">Proposed Price (NPR)</label>
                    <div style="font-size: 13px; color: #64748b; margin-bottom: 12px; background: #f8fafc; padding: 8px 12px; border-radius: 6px; border-left: 3px solid #246A55;">Original Price: <span style="font-weight:700; color:#1e293b;">Rs. <?php echo htmlspecialchars($pkgPrice); ?></span></div>
                    <input type="number" id="proposedPrice" placeholder="e.g. 150000" required inputmode="numeric" style="font-size: 20px; padding: 16px; border-radius: 10px; border: 1px solid #cbd5e1; font-weight:700; color:#246A55;">
                </div>

                <div class="form-group">
                    <label style="margin-bottom: 8px; display: block;">Initial Message to Organizer (Optional)</label>
                    <textarea id="initialMessage" rows="5" placeholder="Explain your custom request or why you're offering this price..." style="width: 100%; padding: 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-family: inherit; resize: vertical; line-height:1.6;"></textarea>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 15px;">
            <button type="submit" class="btn-publish" style="font-size: 16px; padding: 14px 30px;">Send Custom Request <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i></button>
        </div>

    </form>
</div>

<!-- Modals for Editing Package Items -->
<div class="modal-overlay" id="addSectionModal" style="display:none;">
    <div class="modal-box">
        <h3>Add New Item</h3>
        <div class="form-group">
            <label>ITEM TITLE</label>
            <input type="text" id="newSectionTitle" placeholder="e.g. Extra Lighting Setup">
            <p id="addSectionError" style="color: #f87171; font-size: 11px; margin-top: 5px; display: none;">Title is required</p>
        </div>
        <div class="form-group">
            <label>ITEM DESCRIPTION</label>
            <input type="text" id="newSectionDesc" placeholder="Brief description of the item">
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="document.getElementById('addSectionModal').style.display='none'">Cancel</button>
            <button type="button" class="btn-publish" onclick="confirmAddSection()">Add Item</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="editSectionModal" style="display:none;">
    <div class="modal-box">
        <h3>Edit Item</h3>
        <div class="form-group">
            <label>ITEM TITLE</label>
            <input type="text" id="editSectionTitle">
            <p id="editSectionError" style="color: #f87171; font-size: 11px; margin-top: 5px; display: none;">Title is required</p>
        </div>
        <div class="form-group">
            <label>ITEM DESCRIPTION</label>
            <input type="text" id="editSectionDesc">
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="document.getElementById('editSectionModal').style.display='none'">Cancel</button>
            <button type="button" class="btn-publish" onclick="confirmEditSection()">Save Changes</button>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/client/partials/footer.php'; ?>

<script>
    // Editable List Logic
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
        row.innerHTML = `
            <span class="drag-handle">⠿</span>
            <div class="item-content">
                <strong class="item-title">${escapeHtml(title)}</strong>
                <p class="item-desc">${escapeHtml(desc)}</p>
            </div>
            <div class="item-actions">
                <button type="button" class="icon-action-btn edit-item-btn" onclick="openEditModal(this.closest('.item-row'))" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                <button type="button" class="icon-action-btn delete-item-btn" onclick="this.closest('.item-row').remove()" title="Delete"><i class="fa-solid fa-trash-can"></i></button>
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

    // Form Submission
    document.getElementById('modifyPackageForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Build Custom Packages JSON
        const items = [];
        document.querySelectorAll('#custom-items-list .item-row').forEach(row => {
            items.push({
                title: row.querySelector('.item-title').innerText,
                description: row.querySelector('.item-desc').innerText
            });
        });

        const customPackagesJSON = JSON.stringify({
            items: items
        });

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Sending Request...';

        const data = {
            group_event_id: document.getElementById('group_event_id').value,
            organizer_id: document.getElementById('organizer_id').value,
            base_package_tier: document.getElementById('base_package_tier').value,
            proposed_price: document.getElementById('proposedPrice').value,
            custom_packages: customPackagesJSON,
            initial_message: document.getElementById('initialMessage').value
        };

        if (window.emsApi) {
            window.emsApi.apiFetch('/api/v1/custom-events/request', {
                method: 'POST',
                body: data
            })
            .then(res => {
                if (res.success) {
                    alert('Request sent successfully!');
                    window.location.href = '/EventManagementSystem/public/client/requests';
                } else {
                    alert(res.message || 'Failed to send request.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            })
            .catch(err => {
                console.error(err);
                alert('A network error occurred: ' + (err.message || 'Unknown error'));
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            });
        } else {
            alert('System Error: API Client not initialized. Please refresh your browser.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        }
    });
</script>
