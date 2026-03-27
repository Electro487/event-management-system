<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Event - <?php echo SITE_NAME; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/create-event.css">
</head>
<body>

    <?php 
        $activePage = 'events';
        include_once __DIR__ . '/partials/sidebar.php'; 
    ?>

    <main class="main-content">
        <header class="content-header">
            <div class="header-left">
                <div class="breadcrumb">
                    <a href="/EventManagementSystem/public/organizer/events">Events</a> 
                    <span class="separator">›</span> 
                    <span class="current">Create New Event</span>
                </div>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <button class="icon-btn">🔔</button>
                    <div class="user-avatar-small">
                        <img src="/EventManagementSystem/public/assets/images/avatar-placeholder.png" alt="Profile">
                    </div>
                </div>
            </div>
        </header>

        <section class="page-title-section">
            <h1>Create New Event</h1>
            <p>Set up the structural foundation for your next event. Define identity, schedule, and curate package structures for your clients.</p>
        </section>

        <form action="/EventManagementSystem/public/organizer/events/store" method="POST" enctype="multipart/form-data" class="create-event-form">
            
            <!-- Event Identity -->
            <div class="form-section">
                <div class="section-info">
                    <h2>Event Identity</h2>
                    <p>Core details that define the purpose and scope of this curation.</p>
                </div>
                <div class="section-fields">
                    <div class="form-group">
                        <label>EVENT NAME</label>
                        <input type="text" name="title" placeholder="e.g. The Glass Pavilion Gala" required>
                    </div>
                    <div class="form-group">
                        <label>CATEGORY SELECTION</label>
                        <select name="category">
                            <option value="Weddings">Weddings</option>
                            <option value="Meetings">Meetings</option>
                            <option value="Cultural Events">Cultural Events</option>
                            <option value="Family Functions">Family Functions</option>
                            <option value="Other Events and Programs">Other Events and Programs</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>DESCRIPTION</label>
                        <textarea name="description" placeholder="Describe the narrative and architectural vision..." rows="5"></textarea>
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
                        <div class="upload-icon">☁️</div>
                        <button type="button" class="btn-upload">Upload Cover Image</button>
                        <p class="upload-note">Recommended: 1920×1080 (Max 10MB)</p>
                        <input type="file" name="image" id="file-input" hidden>
                    </div>
                </div>
            </div>

            <!-- Schedule & Location -->
            <div class="form-section">
                <div class="section-info">
                    <h2>Schedule & Location</h2>
                    <p>Spatial and temporal parameters for the physical event.</p>
                </div>
                <div class="section-fields">
                    <div class="form-group">
                        <label>RECOMMENDED PLANNING LEAD TIME</label>
                        <select name="lead_time">
                            <option>At least 1 month before</option>
                            <option>At least 3 months before</option>
                            <option>At least 6 months before</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>VENUE NAME</label>
                        <input type="text" name="venue_name" placeholder="The Grand Altius Pavilion">
                    </div>
                    <div class="map-placeholder">
                        <div class="pin-btn">📍 Pin Point Location on Map</div>
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
                    
                    <!-- Basic Tier -->
                    <div class="package-card">
                        <div class="package-header">
                            <span class="tier-icon">📗</span>
                            <div class="tier-info">
                                <h3>Basic Tier</h3>
                                <p>Foundational services</p>
                            </div>
                            <button type="button" class="add-section-btn">+ Add Section</button>
                        </div>
                        <div class="package-body">
                            <div class="form-group">
                                <label>PACKAGE DESCRIPTION</label>
                                <input type="text" placeholder="Enter overview of basic package...">
                            </div>
                            <div class="form-group">
                                <label>PRICE RANGE</label>
                                <input type="text" placeholder="e.g. Rs. 20,000 - Rs. 40,000">
                            </div>
                            <div class="item-row">
                                <span class="drag-handle">⠿</span>
                                <div class="item-content">
                                    <strong>Vendors List</strong>
                                    <p>Standard set of reliable local vendors.</p>
                                </div>
                                <div class="item-actions">
                                    <span class="edit-icon">✏️</span>
                                    <span class="delete-icon">🗑️</span>
                                </div>
                            </div>
                            <div class="item-row">
                                <span class="drag-handle">⠿</span>
                                <div class="item-content">
                                    <strong>Catering Menu</strong>
                                    <p>Fixed menu with 3 main course options.</p>
                                </div>
                                <div class="item-actions">
                                    <span class="edit-icon">✏️</span>
                                    <span class="delete-icon">🗑️</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Standard Tier -->
                    <div class="package-card tier-highlight">
                         <div class="package-header">
                            <span class="tier-icon">📘</span>
                            <div class="tier-info">
                                <h3>Standard Tier</h3>
                                <p>Recommended architecture</p>
                            </div>
                            <button type="button" class="add-section-btn">+ Add Section</button>
                        </div>
                        <div class="package-body">
                            <div class="form-group">
                                <label>PACKAGE DESCRIPTION</label>
                                <input type="text" placeholder="Enter overview of standard package...">
                            </div>
                            <div class="form-group">
                                <label>PRICE RANGE</label>
                                <input type="text" placeholder="e.g. Rs. 50,000 - Rs. 80,000">
                            </div>
                            <div class="item-row">
                                <span class="drag-handle">⠿</span>
                                <div class="item-content">
                                    <strong>Decor Templates</strong>
                                    <p>Choice of 5 thematic floral arrangements.</p>
                                </div>
                                <div class="item-actions">
                                    <span class="edit-icon">✏️</span>
                                    <span class="delete-icon">🗑️</span>
                                </div>
                            </div>
                            <div class="item-row">
                                <span class="drag-handle">⠿</span>
                                <div class="item-content">
                                    <strong>Entertainment Selection</strong>
                                    <p>Live acoustic band or professional DJ.</p>
                                </div>
                                <div class="item-actions">
                                    <span class="edit-icon">✏️</span>
                                    <span class="delete-icon">🗑️</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Premium Tier -->
                    <div class="package-card tier-premium">
                         <div class="package-header">
                            <span class="tier-icon">📙</span>
                            <div class="tier-info">
                                <h3>Premium Tier</h3>
                                <p>Full luxury curation</p>
                            </div>
                            <button type="button" class="add-section-btn">+ Add Section</button>
                        </div>
                        <div class="package-body">
                            <div class="form-group">
                                <label>PACKAGE DESCRIPTION</label>
                                <input type="text" placeholder="Enter overview of premium package...">
                            </div>
                            <div class="form-group">
                                <label>PRICE RANGE</label>
                                <input type="text" placeholder="e.g. Rs. 100,000+">
                            </div>
                            <div class="item-row">
                                <span class="drag-handle">⠿</span>
                                <div class="item-content">
                                    <strong>Full Management</strong>
                                    <p>End-to-end event concierge and coordination.</p>
                                </div>
                                <div class="item-actions">
                                    <span class="edit-icon">✏️</span>
                                    <span class="delete-icon">🗑️</span>
                                </div>
                            </div>
                            <div class="item-row">
                                <span class="drag-handle">⠿</span>
                                <div class="item-content">
                                    <strong>Premium Catering</strong>
                                    <p>Custom multi-cuisine buffet and signature cocktails.</p>
                                </div>
                                <div class="item-actions">
                                    <span class="edit-icon">✏️</span>
                                    <span class="delete-icon">🗑️</span>
                                </div>
                            </div>
                        </div>
                    </div>

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
    </main>

</body>
</html>
