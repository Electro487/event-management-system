<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Event - <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/create-event.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/dropdown-manager.js?v=<?php echo time(); ?>" defer></script>
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            font-weight: 600;
            color: #1e293b;
        }
    </style>
</head>

<body>
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">Loading...</div>

    <?php
    $activePage = 'events';
    include_once dirname(__DIR__) . "/organizer/partials/sidebar.php";

    $isEdit = isset($isEdit) && $isEdit;
    $event = $event ?? [];
    $formAction = $isEdit ? "/EventManagementSystem/public/organizer/events/update" : "/EventManagementSystem/public/organizer/events/store";
    ?>

    <main class="main-content">
        <header class="content-header">
            <div class="header-left">
                <div class="breadcrumb">
                    <a href="/EventManagementSystem/public/organizer/events">Events</a>
                    <span class="separator">›</span>
                    <span class="current"><?php echo $isEdit ? 'Edit Event' : 'Create New Event'; ?></span>
                </div>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <div class="notifications-wrapper">
                        <div class="notification-bell-btn" id="notification-bell">
                            <i class="fa-regular fa-bell"></i>
                            <span class="unread-badge" id="unread-badge" style="display: none;">0</span>
                        </div>
                        <!-- Notifications Dropdown -->
                        <div class="notifications-dropdown" id="notifications-dropdown">
                            <div class="nd-header">
                                <h3>Notifications <span class="nd-unread-tag" id="nd-unread-status">0 New</span></h3>
                                <a href="javascript:void(0)" class="nd-mark-all" id="mark-all-read">Mark all as read</a>
                            </div>
                            <div class="nd-content" id="nd-list">
                                <div class="nd-empty">
                                    <i class="fa-regular fa-bell-slash"></i>
                                    <p>No new notifications</p>
                                </div>
                            </div>
                            <div class="nd-footer">
                                <a href="/EventManagementSystem/public/notifications/all" class="nd-view-all">View All Notifications <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="user-avatar-small">
                        <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
                    </div>
                </div>
            </div>
        </header>

        <section class="page-title-section">
            <h1><?php echo $isEdit ? 'Edit Event' : 'Create New Event'; ?></h1>
            <p><?php echo $isEdit ? 'Modify the details and curation of your existing event.' : 'Set up the structural foundation for your next event. Define identity, schedule, and curate package structures for your clients.'; ?></p>
        </section>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="margin: 0 0 18px; padding: 12px 14px; border-radius: 10px; border: 1px solid #fecaca; background: #fef2f2; color: #b91c1c; font-weight: 600;">
                <?php echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="javascript:void(0)" method="POST" enctype="multipart/form-data" class="create-event-form" id="createEventForm">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                <input type="hidden" name="organizer_id" value="<?php echo $event['organizer_id']; ?>">
            <?php endif; ?>

            <!-- Event Identity -->
            <div class="form-section">
                <div class="section-info">
                    <h2>Event Identity</h2>
                    <p>Core details that define the purpose and scope of this curation.</p>
                </div>
                <div class="section-fields">
                    <div class="form-group">
                        <label>EVENT NAME</label>
                        <input type="text" name="title" placeholder="e.g. The Glass Pavilion Gala" value="<?php echo htmlspecialchars($event['title'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>CATEGORY SELECTION</label>
                        <div class="custom-premium-dropdown" id="categoryDropdown" style="width: 100%; height: 50px; background: white; border: 1.5px solid #e5e7eb; border-radius: 10px;">
                            <div class="dropdown-trigger" style="padding: 0 15px; font-weight: 500;">
                                <span class="selected-val"><?php echo $event['category'] ?? '-- Select Category --'; ?></span>
                                <i class="fa-solid fa-angle-down"></i>
                            </div>
                            <div class="dropdown-menu">
                                <?php
                                $categories = ["Weddings", "Meetings", "Concert", "Cultural Events", "Family Functions", "Other Events and Programs"];
                                foreach ($categories as $cat):
                                ?>
                                    <div class="dropdown-item <?php echo (strtolower($event['category'] ?? '') == strtolower($cat)) ? 'active' : ''; ?>" data-value="<?php echo $cat; ?>">
                                        <?php echo $cat; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($event['category'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>DESCRIPTION</label>
                        <textarea name="description" placeholder="Describe the narrative and architectural vision..." rows="5" required><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Visual Foundation -->
            <div class="form-section">
                <div class="section-info">
                    <h2>Visual Foundation</h2>
                    <p>Main reference image that anchors the aesthetic direction.</p>
                </div>
                <div class="section-fields">
                    <div class="upload-box" id="drop-area">
                        <div id="upload-preview-wrap" style="<?php echo ($isEdit && !empty($event['image_path'])) ? 'display:none;' : ''; ?>">
                            <div class="upload-icon">☁️</div>
                            <button type="button" class="btn-upload" id="upload-trigger">Upload Cover Image</button>
                            <p class="upload-note">Recommended: 1920×1080 (Max 10MB)</p>
                        </div>
                        <?php 
                            $previewPath = '';
                            if (!empty($event['image_path'])) {
                                $previewPath = ($event['image_path'][0] === '/') ? $event['image_path'] : '/EventManagementSystem/public/assets/images/events/' . $event['image_path'];
                            }
                        ?>
                        <img id="image-preview" src="<?php echo $previewPath; ?>" alt="Cover Preview" style="<?php echo ($isEdit && !empty($event['image_path'])) ? 'display:block;' : 'display:none;'; ?> max-height:220px; border-radius:10px; object-fit:cover; width:100%;">
                        <input type="file" name="image" id="file-input" accept="image/*" hidden>
                        <button type="button" id="remove-image" style="<?php echo ($isEdit && !empty($event['image_path'])) ? 'display:inline-block;' : 'display:none;'; ?>" class="btn-remove-img">✕ Remove Image</button>
                    </div>
                </div>
            </div>

            <!-- Schedule & Location -->
            <div class="form-section">
                <div class="section-info">
                    <h2>Schedule &amp; Location</h2>
                    <p>Spatial and temporal parameters for the physical event.</p>
                </div>
                <div class="section-fields">

                    <div class="form-group">
                        <label>VENUE NAME</label>
                        <input type="text" name="venue_name" placeholder="The Grand Altius Pavilion" value="<?php echo htmlspecialchars($event['venue_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>VENUE LOCATION</label>
                        <input type="text" name="venue_location" placeholder="e.g. Royal Exhibition Hall, Kathmandu" value="<?php echo htmlspecialchars($event['venue_location'] ?? ''); ?>" required>
                    </div>

                    <!-- Concert Based Date & Time: Only for Concert Category -->
                    <div id="ticketScheduleFields" style="<?php echo (isset($event['category']) && strtolower($event['category']) === 'concert') ? 'display: block;' : 'display: none;'; ?> width: 100%;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>EVENT DATE</label>
                                <input type="date" name="event_date" value="<?php echo (isset($event['event_date'])) ? date('Y-m-d', strtotime($event['event_date'])) : ''; ?>" style="width: 100%;">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>EVENT TIME</label>
                                <input type="time" name="event_time" value="<?php echo (isset($event['event_date'])) ? date('H:i', strtotime($event['event_date'])) : ''; ?>" style="width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package Curation -->
            <div class="form-section">
                <div class="section-info">
                    <h2>Package Curation</h2>
                    <p>Curate the foundational service structures for each tier.</p>
                </div>
                <div class="section-fields packages-list">
                    <?php
                    $existingPackages = [];
                    if ($isEdit && !empty($event['packages'])) {
                        $existingPackages = json_decode($event['packages'], true);
                    } else if (!$isEdit) {
                        $existingPackages = [
                            'basic' => [
                                'description' => '',
                                'price' => '',
                                'items' => [
                                    ['title' => 'Venue Setup', 'description' => 'Standard seating and basic ambient lighting.'],
                                    ['title' => 'Essential Coordination', 'description' => 'On-the-day event management and support.']
                                ]
                            ],
                            'standard' => [
                                'description' => '',
                                'price' => '',
                                'items' => [
                                    ['title' => 'Decor Templates', 'description' => 'Choice of 5 thematic floral arrangements.'],
                                    ['title' => 'Entertainment Selection', 'description' => 'Live acoustic band or professional DJ.']
                                ]
                            ],
                            'premium' => [
                                'description' => '',
                                'price' => '',
                                'items' => [
                                    ['title' => 'Full Management', 'description' => 'End-to-end event concierge and coordination.'],
                                    ['title' => 'Exclusive Catering & Decor', 'description' => 'Premium 5-course meal and luxury imported floral arrangements.']
                                ]
                            ]
                        ];
                    }
                    $tiers = [
                        'basic' => ['name' => 'Basic Tier', 'icon' => 'fa-solid fa-box', 'subtitle' => 'Foundational services', 'class' => ''],
                        'standard' => ['name' => 'Standard Tier', 'icon' => 'fa-solid fa-certificate', 'subtitle' => 'Recommended architecture', 'class' => 'tier-highlight'],
                        'premium' => ['name' => 'Premium Tier', 'icon' => 'fa-solid fa-award', 'subtitle' => 'Full luxury curation', 'class' => 'tier-premium']
                    ];

                    foreach ($tiers as $tierKey => $tierInfo):
                        $pkgData = $existingPackages[$tierKey] ?? [];
                        $items = $pkgData['items'] ?? [];
                    ?>
                        <!-- <?php echo $tierInfo['name']; ?> -->
                        <div class="package-card <?php echo $tierInfo['class']; ?>" data-tier="<?php echo $tierKey; ?>">
                            <div class="package-header">
                                <span class="tier-icon-box tier-<?php echo $tierKey; ?>">
                                    <i class="<?php echo $tierInfo['icon']; ?>"></i>
                                </span>
                                <div class="tier-info">
                                    <h3><?php echo $tierInfo['name']; ?></h3>
                                    <p><?php echo $tierInfo['subtitle']; ?></p>
                                </div>
                                <button type="button" class="add-section-btn" data-tier="<?php echo $tierKey; ?>">+ Add Section</button>
                            </div>
                            <div class="package-body">
                                <div class="form-group pkg-desc-group">
                                    <label>PACKAGE DESCRIPTION</label>
                                    <input type="text" name="packages[<?php echo $tierKey; ?>][description]" value="<?php echo htmlspecialchars($pkgData['description'] ?? ''); ?>" placeholder="Enter overview of <?php echo $tierKey; ?> package..." required>
                                </div>
                                <div class="form-group pkg-price-group">
                                    <label>PRICE (NPR) <span class="premium-cap-label" style="<?php echo (strtolower($event['category'] ?? '') === 'concert') ? 'display: inline;' : 'display: none;'; ?> color:#ef4444; font-size:10px;">(MAX 100K FOR CONCERT)</span></label>
                                    <input type="number" class="package-price-input" data-tier="<?php echo $tierKey; ?>" name="packages[<?php echo $tierKey; ?>][price]" value="<?php echo htmlspecialchars($pkgData['price'] ?? ($pkgData['price_range'] ?? '')); ?>" placeholder="e.g. 25000" required min="1" max="20000000" step="1" inputmode="numeric">
                                </div>
                                <div class="items-list" data-tier="<?php echo $tierKey; ?>">
                                    <?php foreach ($items as $idx => $item): ?>
                                        <div class="item-row">
                                            <span class="drag-handle">⠿</span>
                                            <div class="item-content">
                                                <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                                <p><?php echo htmlspecialchars($item['description']); ?></p>
                                                <input type="hidden" name="packages[<?php echo $tierKey; ?>][items][<?php echo $idx; ?>][title]" value="<?php echo htmlspecialchars($item['title']); ?>">
                                                <input type="hidden" name="packages[<?php echo $tierKey; ?>][items][<?php echo $idx; ?>][description]" value="<?php echo htmlspecialchars($item['description']); ?>">
                                            </div>
                                            <div class="item-actions">
                                                <button type="button" class="icon-action-btn edit-item-btn" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                                <button type="button" class="icon-action-btn delete-item-btn" title="Delete"><i class="fa-solid fa-trash-can"></i></button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-footer">
                <button type="button" class="btn-cancel" onclick="history.back()">Cancel</button>
                <div class="footer-right">
                    <button type="submit" name="status" value="draft" class="btn-draft">Save as Draft</button>
                    <button type="submit" name="status" value="active" class="btn-publish">Publish Event</button>
                </div>
            </div>

        </form>

        <!-- Add Section Modal -->
        <div class="modal-overlay" id="addSectionModal" style="display:none;">
            <div class="modal-box">
                <h3>Add New Section</h3>
                <div class="form-group">
                    <label>SECTION TITLE</label>
                    <input type="text" id="newSectionTitle" placeholder="e.g. Photography Package">
                </div>
                <div class="form-group">
                    <label>SECTION DESCRIPTION</label>
                    <input type="text" id="newSectionDesc" placeholder="Brief description of what's included">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="closeModal">Cancel</button>
                    <button type="button" class="btn-publish" id="confirmAddSection">Add Section</button>
                </div>
            </div>
        </div>

        <!-- Edit Section Modal -->
        <div class="modal-overlay" id="editSectionModal" style="display:none;">
            <div class="modal-box">
                <h3>Edit Section</h3>
                <div class="form-group">
                    <label>SECTION TITLE</label>
                    <input type="text" id="editSectionTitle" placeholder="Section title">
                </div>
                <div class="form-group">
                    <label>SECTION DESCRIPTION</label>
                    <input type="text" id="editSectionDesc" placeholder="Section description">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="closeEditModal">Cancel</button>
                    <button type="button" class="btn-publish" id="confirmEditSection">Save Changes</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        // ─────────────── IMAGE UPLOAD ───────────────
        const uploadTrigger = document.getElementById('upload-trigger');
        const fileInput = document.getElementById('file-input');
        const imagePreview = document.getElementById('image-preview');
        const removeImageBtn = document.getElementById('remove-image');
        const uploadPreviewWrap = document.getElementById('upload-preview-wrap');
        const dropArea = document.getElementById('drop-area');

        uploadTrigger.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    uploadPreviewWrap.style.display = 'none';
                    removeImageBtn.style.display = 'inline-block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        removeImageBtn.addEventListener('click', () => {
            fileInput.value = '';
            imagePreview.style.display = 'none';
            removeImageBtn.style.display = 'none';
            uploadPreviewWrap.style.display = 'flex';
        });

        // Drag & Drop
        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('drag-over');
        });
        dropArea.addEventListener('dragleave', () => dropArea.classList.remove('drag-over'));
        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });

        // ─────────────── ADD SECTION ───────────────
        let currentTierForAdd = null;

        document.querySelectorAll('.add-section-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                currentTierForAdd = this.dataset.tier;
                document.getElementById('newSectionTitle').value = '';
                document.getElementById('newSectionDesc').value = '';
                document.getElementById('addSectionModal').style.display = 'flex';
                document.getElementById('newSectionTitle').focus();
            });
        });

        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('addSectionModal').style.display = 'none';
        });

        document.getElementById('addSectionModal').addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });

        document.getElementById('confirmAddSection').addEventListener('click', () => {
            const title = document.getElementById('newSectionTitle').value.trim();
            const desc = document.getElementById('newSectionDesc').value.trim();
            if (!title) {
                alert('Please enter a section title.');
                return;
            }

            const itemsList = document.querySelector(`.items-list[data-tier="${currentTierForAdd}"]`);
            const newRow = buildItemRow(currentTierForAdd, title, desc || 'Description here...');
            itemsList.appendChild(newRow);
            bindRowEvents(newRow, currentTierForAdd);
            document.getElementById('addSectionModal').style.display = 'none';
        });

        // ─────────────── EDIT & DELETE ───────────────
        let currentEditRow = null;

        function buildItemRow(tier, title, desc) {
            const row = document.createElement('div');
            row.className = 'item-row';
            const index = document.querySelectorAll(`.items-list[data-tier="${tier}"] .item-row`).length;
            row.innerHTML = `
        <span class="drag-handle">⠿</span>
        <div class="item-content">
            <strong>${escapeHtml(title)}</strong>
            <p>${escapeHtml(desc)}</p>
            <input type="hidden" name="packages[${tier}][items][${index}][title]" value="${escapeHtml(title)}">
            <input type="hidden" name="packages[${tier}][items][${index}][description]" value="${escapeHtml(desc)}">
        </div>
        <div class="item-actions">
            <button type="button" class="icon-action-btn edit-item-btn" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
            <button type="button" class="icon-action-btn delete-item-btn" title="Delete"><i class="fa-solid fa-trash-can"></i></button>
        </div>
    `;
            return row;
        }

        function escapeHtml(text) {
            const d = document.createElement('div');
            d.appendChild(document.createTextNode(text));
            return d.innerHTML;
        }

        function bindRowEvents(row, tier) {
            row.querySelector('.edit-item-btn').addEventListener('click', function() {
                currentEditRow = row;
                const strong = row.querySelector('.item-content strong');
                const p = row.querySelector('.item-content p');
                document.getElementById('editSectionTitle').value = strong.textContent;
                document.getElementById('editSectionDesc').value = p.textContent;
                document.getElementById('editSectionModal').style.display = 'flex';
                document.getElementById('editSectionTitle').focus();
            });

            row.querySelector('.delete-item-btn').addEventListener('click', function() {
                if (confirm('Remove this section?')) {
                    row.style.transition = 'all 0.2s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        row.remove();
                        renumberItems(tier);
                    }, 200);
                }
            });
        }

        function renumberItems(tier) {
            document.querySelectorAll(`.items-list[data-tier="${tier}"] .item-row`).forEach((row, index) => {
                const titleInput = row.querySelector('input[name*="[title]"]');
                const descInput = row.querySelector('input[name*="[description]"]');
                if (titleInput) titleInput.name = `packages[${tier}][items][${index}][title]`;
                if (descInput) descInput.name = `packages[${tier}][items][${index}][description]`;
            });
        }

        // Bind existing rows
        document.querySelectorAll('.items-list').forEach(list => {
            const tier = list.dataset.tier;
            list.querySelectorAll('.item-row').forEach((row, index) => {
                // Add hidden inputs to pre-rendered rows if they don't have them
                if (!row.querySelector('input[type="hidden"]')) {
                    const title = row.querySelector('.item-content strong').textContent;
                    const desc = row.querySelector('.item-content p').textContent;
                    row.querySelector('.item-content').insertAdjacentHTML('beforeend', `
                <input type="hidden" name="packages[${tier}][items][${index}][title]" value="${escapeHtml(title)}">
                <input type="hidden" name="packages[${tier}][items][${index}][description]" value="${escapeHtml(desc)}">
            `);
                }
                bindRowEvents(row, tier);
            });
        });

        // Edit modal
        document.getElementById('closeEditModal').addEventListener('click', () => {
            document.getElementById('editSectionModal').style.display = 'none';
        });
        document.getElementById('editSectionModal').addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });

        document.getElementById('confirmEditSection').addEventListener('click', () => {
            const title = document.getElementById('editSectionTitle').value.trim();
            const desc = document.getElementById('editSectionDesc').value.trim();
            if (!title) {
                alert('Please enter a section title.');
                return;
            }
            if (currentEditRow) {
                currentEditRow.querySelector('.item-content strong').textContent = title;
                currentEditRow.querySelector('.item-content p').textContent = desc;
                currentEditRow.querySelector('input[name*="[title]"]').value = title;
                currentEditRow.querySelector('input[name*="[description]"]').value = desc;
            }
            document.getElementById('editSectionModal').style.display = 'none';
        });

        // ─────────────── PACKAGE PRICE VALIDATION ───────────────
        const eventForm = document.getElementById('createEventForm');
        const MAX_PACKAGE_PRICE = 20000000;

        document.querySelectorAll('.package-price-input').forEach((input) => {
            input.addEventListener('input', () => {
                input.value = input.value.replace(/[^\d]/g, '');
            });
        });

        eventForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // ─────────────── VALIDATION ───────────────
            const basicVal = document.querySelector('input[name="packages[basic][price]"]').value;
            const standardVal = document.querySelector('input[name="packages[standard][price]"]').value;
            const premiumVal = document.querySelector('input[name="packages[premium][price]"]').value;

            const basic = parseInt(basicVal, 10);
            const standard = parseInt(standardVal, 10);
            const premium = parseInt(premiumVal, 10);

            const category = document.querySelector('input[name="category"]').value;
            const isConcert = category.toLowerCase() === 'concert';

            if (![basicVal, standardVal, premiumVal].every(v => /^\d+$/.test(v))) {
                alert('Package prices must be numbers only (no decimals or symbols).');
                return;
            }

            if ([basic, standard, premium].some(v => v <= 0)) {
                alert('All package prices must be greater than 0.');
                return;
            }

            // Concert specific caps (Applies to all tiers for consistency)
            if (isConcert) {
                if (premium > 100000 || standard > 100000 || basic > 100000) {
                    alert('Ticket prices cannot exceed Rs. 1,00,000 for the Concert category.');
                    return;
                }
            } else {
                if ([basic, standard, premium].some(v => v > MAX_PACKAGE_PRICE)) {
                    alert('Package price cannot exceed NPR 2,00,00,000.');
                    return;
                }
            }

            if (!(basic < standard && standard < premium)) {
                alert('Price order must be: Basic < Standard < Premium. Please adjust the prices accordingly.');
                return;
            }

            // ─────────────── API SUBMISSION ───────────────
            const form = this;
            const formData = new FormData(form);
            const isEdit = <?php echo $isEdit ? 'true' : 'false'; ?>;
            const eventId = <?php echo $isEdit ? (int)$event['id'] : 'null'; ?>;
            
            // Show loading state
            const submitBtns = form.querySelectorAll('button[type="submit"]');
            submitBtns.forEach(btn => btn.disabled = true);
            const overlay = document.getElementById('loadingOverlay');
            if(overlay) {
                overlay.textContent = isEdit ? 'Updating event...' : 'Creating event...';
                overlay.style.display = 'flex';
            }

            // Add status from the button clicked
            const status = e.submitter ? e.submitter.value : 'draft';
            formData.set('status', status);

            if (window.emsApi) {
                const endpoint = isEdit ? `/api/v1/events/${eventId}` : '/api/v1/events';
                
                if (isEdit) {
                    formData.append('_method', 'PUT'); 
                }

                try {
                    const res = await window.emsApi.apiFetch(endpoint, {
                        method: 'POST', 
                        body: formData
                    });
                    
                    if (res.success) {
                        window.location.href = '/EventManagementSystem/public/organizer/events?success=1';
                    } else {
                        throw new Error(res.error?.message || 'Submission failed');
                    }
                } catch (err) {
                    alert('Error: ' + err.message);
                    if(overlay) overlay.style.display = 'none';
                    submitBtns.forEach(btn => btn.disabled = false);
                }
            } else {
                form.submit(); // Fallback
            }
        });

        // ─────────────── TICKET FIELDS TOGGLE ───────────────
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof DropdownManager !== 'undefined') {
                const handleToggle = (value) => {
                    const scheduleFields = document.getElementById('ticketScheduleFields');
                    if(!scheduleFields) return;
                    
                    const dateInput = scheduleFields.querySelector('input[name="event_date"]');
                    const timeInput = scheduleFields.querySelector('input[name="event_time"]');
                    const premiumCaps = document.querySelectorAll('.premium-cap-label');

                    if (value && value.toLowerCase() === 'concert') {
                        scheduleFields.style.display = 'block';
                        if(dateInput) dateInput.required = true;
                        if(timeInput) timeInput.required = true;
                        premiumCaps.forEach(l => l.style.display = 'inline');
                    } else {
                        scheduleFields.style.display = 'none';
                        if(dateInput) dateInput.required = false;
                        if(timeInput) timeInput.required = false;
                        premiumCaps.forEach(l => l.style.display = 'none');
                    }
                };

                // Listen for changes
                DropdownManager.onSelect('categoryDropdown', handleToggle);

                // Check initial state
                const currentCat = document.querySelector('input[name="category"]').value;
                if(currentCat) handleToggle(currentCat);
            }
        });
    </script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script>
        window.addEventListener('load', () => {
            const overlay = document.getElementById('loadingOverlay');
            if(overlay) overlay.style.display = 'none';
        });
    </script>
</body>

</html>
