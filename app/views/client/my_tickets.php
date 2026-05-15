<?php
$title = 'My Tickets - e-Plan';
$activePage = 'tickets';
$extra_head = '
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/my-tickets.css?v=' . time() . '">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/feedback-popup.css?v=' . time() . '">
    <style>
        .pagination-container { display: flex; justify-content: center; align-items: center; gap: 20px; margin-top: 40px; margin-bottom: 20px; }
        .page-btn { padding: 10px 18px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; cursor: pointer; transition: all 0.2s; font-weight: 600; font-size: 14px; color: #64748b; display: flex; align-items: center; gap: 8px; }
        .page-btn:hover:not(:disabled) { background: #f8fafc; color: #1e293b; border-color: #cbd5e1; transform: translateY(-1px); }
        .page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .page-info { font-size: 14px; color: #64748b; font-weight: 500; }
        
        /* empty-state + btn-browse-more: see my-tickets.css */
    </style>
';
include 'partials/header.php';
?>

<div class="dashboard-container">
    <div class="page-header-row clearfix" style="margin-bottom: 30px;">
        <div class="headings">
            <h1 class="page-header-title">MY TICKETS</h1>
            <p class="page-header-desc">Access your concert entry passes, track payment statuses, and print tickets for seamless venue entry.</p>
        </div>
    </div>

    <div class="ticket-list" id="ticketList">
        <div class="empty-state">
            <i class="fa-solid fa-spinner fa-spin" style="font-size: 24px; color: #246A55;"></i>
            <p style="margin-top: 15px;">Loading your tickets...</p>
        </div>
    </div>

    <div id="pagination" class="pagination-container" style="display: none;"></div>
</div>

<script>
    let allTickets = [];
    let currentPage = 1;
    const itemsPerPage = 6;

    document.addEventListener('DOMContentLoaded', () => {
        fetchTickets();
    });

    function fetchTickets() {
        if (!window.emsApi) return;
        
        window.emsApi.apiFetch('/api/v1/bookings')
            .then(res => {
                if (res.success && res.data && res.data.items) {
                    // Filter for concerts only
                    allTickets = res.data.items.filter(b => (b.event_category || '').trim().toLowerCase() === 'concert');
                    renderTickets();
                } else {
                    showEmptyState();
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                showEmptyState('Error loading tickets. Please refresh.');
            });
    }

    function renderTickets() {
        const container = document.getElementById('ticketList');
        if (!allTickets.length) {
            showEmptyState();
            return;
        }

        const totalPages = Math.ceil(allTickets.length / itemsPerPage);
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageItems = allTickets.slice(start, end);

        let html = '';
        pageItems.forEach(t => {
            const eSnap = safeParse(t.event_snapshot);
            const title = eSnap?.title || t.event_title;
            const rawImg = eSnap?.image_path || t.event_image || '';
            const imgUrl = getValidImageUrl(rawImg);
            const status = (t.status || '').toLowerCase();
            const payStatus = (t.payment_status || 'unpaid').toLowerCase();
            const ticketId = 'EPLN-' + String(t.id).padStart(5, '0');

            html += `
                <div class="ticket-card">
                    <div class="ticket-banner">
                        <img src="${escapeHtml(imgUrl)}" alt="Event" class="ticket-img">
                        <div class="ticket-overlay"></div>
                        <span class="ticket-id">#${ticketId}</span>
                    </div>

                    <div class="ticket-info">
                        <div class="ticket-title-row">
                            <h3 class="ticket-title">${escapeHtml(title)}</h3>
                            <span class="ticket-status status-${status}">${status}</span>
                        </div>

                        <div class="ticket-meta-grid">
                            <div class="meta-item">
                                <span class="meta-label">Date & Time</span>
                                <span class="meta-value"><i class="fa-regular fa-calendar"></i> ${formatDate(t.event_date)}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Quantity</span>
                                <span class="meta-value"><i class="fa-solid fa-user-group"></i> ${t.guest_count} Person(s)</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Tier</span>
                                <span class="meta-value"><i class="fa-solid fa-tag"></i> ${capitalize(t.package_tier)}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Venue</span>
                                <span class="meta-value" style="font-size: 12px;"><i class="fa-solid fa-location-dot"></i> ${escapeHtml(t.venue_name || 'Venue TBD')}</span>
                            </div>
                        </div>

                        <div class="ticket-pricing">
                            <div class="price-box">
                                <span class="price-label">TOTAL AMOUNT</span>
                                <span class="price-value">Rs. ${parseFloat(t.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
                            </div>
                            <span class="payment-badge pay-${payStatus}">
                                ${payStatus.toUpperCase().replace('_', ' ')}
                            </span>
                        </div>

                        <div class="ticket-actions">
                            ${payStatus === 'unpaid' ? `
                                <a href="/EventManagementSystem/public/client/payment/checkout?booking_id=${t.id}" class="btn-primary-ticket">
                                    Complete Payment
                                </a>
                            ` : (status === 'confirmed' || status === 'completed' ? `
                                <a href="/EventManagementSystem/public/client/ticket?id=${t.id}" class="btn-print-ticket" target="_blank">
                                    <i class="fa-solid fa-print"></i> Print QR Ticket
                                </a>
                            ` : '')}

                            <a href="/EventManagementSystem/public/client/bookings/view?id=${t.id}" class="btn-secondary-ticket-full">
                                View Ticket Details
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        const pag = document.getElementById('pagination');
        if (totalPages <= 1) {
            pag.style.display = 'none';
            return;
        }

        pag.style.display = 'flex';
        pag.innerHTML = `
            <button class="page-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                <i class="fa-solid fa-arrow-left"></i> Previous
            </button>
            <span class="page-info">Page ${currentPage} of ${totalPages}</span>
            <button class="page-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                Next <i class="fa-solid fa-arrow-right"></i>
            </button>
        `;
    }

    function changePage(page) {
        currentPage = page;
        renderTickets();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function showEmptyState(msg) {
        const container = document.getElementById('ticketList');
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa-solid fa-ticket" style="font-size: 32px; color: #94a3b8;"></i>
                </div>
                <h3 style="font-size: 20px; color: #1e293b; margin-bottom: 10px;">No Tickets Found</h3>
                <p style="color: #64748b; margin-bottom: 25px;">${msg || "You haven't reserved any concert tickets yet. Explore upcoming concerts to get started."}</p>
                <a href="/EventManagementSystem/public/client/events" class="btn-browse-more">Browse Concerts <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        `;
        document.getElementById('pagination').style.display = 'none';
    }

    // Helper functions
    function safeParse(json) {
        if (!json) return null;
        if (typeof json === 'object') return json;
        try { return JSON.parse(json); } catch (e) { return null; }
    }

    function getValidImageUrl(imagePath) {
        if (!imagePath) return '/EventManagementSystem/public/assets/images/placeholder.jpg';
        if (imagePath.startsWith('[')) {
            const paths = safeParse(imagePath);
            if (paths && paths.length > 0) imagePath = paths[0];
        }
        return (imagePath.startsWith('/')) ? imagePath : '/EventManagementSystem/public/assets/images/events/' + imagePath;
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function escapeHtml(unsafe) {
        return (unsafe || "").toString()
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }
</script>

<?php include 'partials/feedback_popup.php'; ?>
<?php include 'partials/footer.php'; ?>