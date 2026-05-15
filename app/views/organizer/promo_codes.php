<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Promo Codes - <?php echo SITE_NAME; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/promo-codes.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
</head>

<body>

    <?php
    $activePage = 'promo_codes';
    include_once __DIR__ . '/partials/sidebar.php';
    ?>

    <main class="main-content">
        <header class="content-header">
            <div class="header-left">
                <h1>Promo Codes</h1>
                <p>Create and manage discount codes for your customers</p>
            </div>

            <div class="header-right">
                <button class="btn-primary" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i> Create New Code
                </button>
                <div class="notifications-wrapper">
                    <div class="notification-bell-btn" id="notification-bell">
                        <i class="fa-regular fa-bell"></i>
                        <span class="unread-badge" id="unread-badge" style="display: none;">0</span>
                    </div>
                    <?php include_once dirname(__DIR__) . '/partials/notifications_dropdown.php'; ?>
                </div>
                <div class="user-profile-info">
                    <?php include_once __DIR__ . '/partials/header_profile.php'; ?>
                </div>
            </div>
        </header>

        <div class="promo-codes-container">
            <div class="promo-codes-table-card">
                <table class="promo-table">
                    <thead>
                        <tr>
                            <th>CODE</th>
                            <th>DISCOUNT</th>
                            <th>EXPIRY</th>
                            <th>USAGE</th>
                            <th>STATUS</th>
                            <th style="text-align:right;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="promoTableBody">
                        <tr>
                            <td colspan="6" class="no-data">Loading promo codes...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Create Promo Modal -->
    <div id="createPromoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create New Promo Code</h2>
                <span class="close-modal" onclick="closeCreateModal()">&times;</span>
            </div>
            <form id="createPromoForm" onsubmit="handleCreatePromo(event)">
                <div class="form-group">
                    <label for="promoCode">Promo Code Name</label>
                    <input type="text" id="promoCode" name="code" placeholder="e.g. SUMMER2026" required style="text-transform: uppercase;">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="discount">Discount Percentage (%)</label>
                        <input type="number" id="discount" name="discount_percentage" min="1" max="100" placeholder="e.g. 15" required>
                    </div>
                    <div class="form-group">
                        <label for="expiry">Expiry Date</label>
                        <input type="date" id="expiry" name="expires_at" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="limit">Usage Limit (Optional)</label>
                    <input type="number" id="limit" name="usage_limit" min="1" placeholder="Leave empty for unlimited">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeCreateModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Create & Notify Users</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', fetchPromoCodes);

        async function fetchPromoCodes() {
            try {
                const res = await window.emsApi.apiFetch('/api/v1/promo-codes');
                const codes = res.data || [];
                renderTable(codes);
            } catch (err) {
                console.error('Failed to fetch promo codes:', err);
                document.getElementById('promoTableBody').innerHTML = '<tr><td colspan="6" class="no-data">Error loading data.</td></tr>';
            }
        }

        function renderTable(codes) {
            const tbody = document.getElementById('promoTableBody');
            if (codes.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="no-data">No promo codes found. Create your first one!</td></tr>';
                return;
            }

            tbody.innerHTML = codes.map(code => {
                const expiryDate = new Date(code.expires_at);
                const isExpired = expiryDate < new Date();
                const statusClass = isExpired ? 'status-expired' : 'status-active';
                const statusText = isExpired ? 'Expired' : 'Active';
                
                return `
                    <tr>
                        <td><span class="promo-badge">${code.code}</span></td>
                        <td><strong>${parseFloat(code.discount_percentage)}%</strong></td>
                        <td>${expiryDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
                        <td>${code.usage_count} ${code.usage_limit ? '/ ' + code.usage_limit : ''}</td>
                        <td><span class="status-pill ${statusClass}">${statusText}</span></td>
                        <td style="text-align:right;">
                            <button class="btn-delete" onclick="handleDeletePromo(${code.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function openCreateModal() {
            document.getElementById('createPromoModal').classList.add('show');
        }

        function closeCreateModal() {
            document.getElementById('createPromoModal').classList.remove('show');
            document.getElementById('createPromoForm').reset();
        }

        async function handleCreatePromo(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            try {
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

                await window.emsApi.apiFetch('/api/v1/promo-codes', {
                    method: 'POST',
                    body: {
                        code: formData.get('code'),
                        discount_percentage: formData.get('discount_percentage'),
                        expires_at: formData.get('expires_at'),
                        usage_limit: formData.get('usage_limit')
                    }
                });

                closeCreateModal();
                fetchPromoCodes();
                alert('Promo code created successfully! All users have been notified.');
            } catch (err) {
                alert('Error: ' + err.message);
            } finally {
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = false;
                btn.innerHTML = 'Create & Notify Users';
            }
        }

        async function handleDeletePromo(id) {
            if (!confirm('Are you sure you want to delete this promo code?')) return;

            try {
                await window.emsApi.apiFetch(`/api/v1/promo-codes/${id}`, {
                    method: 'DELETE'
                });
                fetchPromoCodes();
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }
    </script>
</body>

</html>
