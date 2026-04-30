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
</head>

<body>

    <?php
    $activePage = 'events';
    include_once dirname(__DIR__) . "/admin/partials/sidebar.php";

    $isEdit = isset($_GET['id']) && $_GET['id'] > 0;
    $eventId = $isEdit ? (int)$_GET['id'] : 0;
    ?>

    <main class="main-content">
        <header class="content-header">
            <div class="header-left">
                <div class="breadcrumb">
                    <a href="/EventManagementSystem/public/admin/events">Events</a>
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

        <form action="#" method="POST" enctype="multipart/form-data" class="create-event-form" id="createEventForm">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" id="event_id_input" value="<?php echo $eventId; ?>">
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
                        <input type="text" name="title" id="event_title_input" placeholder="e.g. The Glass Pavilion Gala" value="" required>
                    </div>
                    <div class="form-group">
                        <label>CATEGORY SELECTION</label>
                        <div class="custom-premium-dropdown" id="categoryDropdown" style="width: 100%;">
                            <div class="dropdown-trigger">
                                <span class="selected-val" id="cat-selected-val">-- Select Category --</span>
                                <i class="fa-solid fa-angle-down"></i>
                            </div>
                            <div class="dropdown-menu">
                                <div class="dropdown-item active" data-value="">-- Select Category --</div>
                                <?php
                                $categories = ["Weddings", "Meetings", "Cultural Events", "Family Functions", "Other Events and Programs"];
                                foreach ($categories as $cat):
                                ?>
                                    <div class="dropdown-item" data-value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="category" id="event_category_input" value="" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>DESCRIPTION</label>
                        <textarea name="description" id="event_description_input" placeholder="Describe the narrative and architectural vision..." rows="5" required></textarea>
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
                        <div id="upload-preview-wrap">
                            <div class="upload-icon">☁️</div>
                            <button type="button" class="btn-upload" id="upload-trigger">Upload Cover Image</button>
                            <p class="upload-note">Recommended: 1920×1080 (Max 10MB)</p>
                        </div>
                        <img id="image-preview" src="" alt="Cover Preview" style="display:none; max-height:220px; border-radius:10px; object-fit:cover; width:100%;" onerror="this.src='/EventManagementSystem/public/assets/images/placeholder.jpg';">
                        <input type="file" name="image" id="file-input" accept="image/*" hidden>
                        <button type="button" id="remove-image" style="display:none;" class="btn-remove-img">✕ Remove Image</button>
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
                        <input type="text" name="venue_name" id="event_venue_name_input" placeholder="The Grand Altius Pavilion" value="" required>
                    </div>
                    <div class="form-group">
                        <label>VENUE LOCATION</label>
                        <input type="text" name="venue_location" id="event_venue_location_input" placeholder="e.g. Royal Exhibition Hall, Kathmandu" value="" required>
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
                                <div class="tier-icon-box <?php echo 'tier-' . $tierKey; ?>">
                                    <i class="<?php echo $tierInfo['icon']; ?>"></i>
                                </div>
                                <div class="tier-info">
                                    <h3><?php echo $tierInfo['name']; ?></h3>
                                    <p><?php echo $tierInfo['subtitle']; ?></p>
                                </div>
                                <button type="button" class="add-section-btn" data-tier="<?php echo $tierKey; ?>">+ Add Section</button>
                            </div>
                            <div class="package-body">
                                <div class="form-group pkg-desc-group">
                                    <label>PACKAGE DESCRIPTION</label>
                                    <input type="text" name="packages[<?php echo $tierKey; ?>][description]" id="pkg_desc_<?php echo $tierKey; ?>" value="<?php echo htmlspecialchars($pkgData['description'] ?? ''); ?>" placeholder="Enter overview of <?php echo $tierKey; ?> package..." required>
                                </div>
                                <div class="form-group pkg-price-group">
                                    <label>PRICE (NPR)</label>
                                    <input type="number" class="package-price-input" data-tier="<?php echo $tierKey; ?>" name="packages[<?php echo $tierKey; ?>][price]" id="pkg_price_<?php echo $tierKey; ?>" value="<?php echo htmlspecialchars($pkgData['price'] ?? ($pkgData['price_range'] ?? '')); ?>" placeholder="e.g. 25000" required min="1" max="20000000" step="1" inputmode="numeric">
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
                    <button type="button" id="save-draft-btn" class="btn-draft">Save as Draft</button>
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
    <script>
        window.IS_EDIT = <?php echo (int)($isEdit ?? 0); ?>;
        window.EVENT_ID = <?php echo (int)($eventId ?? 0); ?>;
    </script>
    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/dropdown-manager.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/admin/create_event.js?v=<?php echo time(); ?>"></script>
</body>

</html>
