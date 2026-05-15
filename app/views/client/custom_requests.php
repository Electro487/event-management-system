<?php
$title = "My Custom Requests - e.PLAN";
$activePage = "requests";
$extra_head = '
<style>
/* Mimic the booking layout styles */
.bookings-grid { display: grid; gap: 24px; }
.booking-card { display: flex; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid var(--border-color); }
.booking-image { width: 280px; position: relative; }
.booking-image img { width: 100%; height: 100%; object-fit: cover; }
.status-badge { position: absolute; top: 16px; right: 16px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; color: white; }
.status-pending { background: #f59e0b; }
.status-negotiating { background: #3b82f6; }
.status-approved { background: #10b981; }
.status-rejected { background: #ef4444; }
.status-booked { background: #8b5cf6; }
.booking-details { padding: 24px; flex: 1; display: flex; flex-direction: column; }
.booking-title { font-size: 20px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; }
.booking-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; flex: 1; }
.booking-meta span { display: flex; align-items: center; gap: 8px; color: var(--text-gray); font-size: 14px; }
.card-actions { margin-top: 24px; display: flex; gap: 12px; justify-content: flex-end; }
.btn-secondary { background: #f8fafc; color: var(--text-dark); border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 20px; text-decoration: none; font-weight: 600; transition: all 0.2s; }
.empty-state { text-align: center; padding: 60px 20px; }
.empty-state i { font-size: 48px; color: var(--primary-color); margin-bottom: 16px; opacity: 0.5; }

/* Pagination */
.pagination { display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 40px; padding-bottom: 40px; }
.page-btn { padding: 10px 16px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; cursor: pointer; transition: all 0.2s; font-weight: 600; font-size: 14px; color: #64748b; }
.page-btn:hover { background: #f8fafc; color: #1e293b; border-color: #cbd5e1; }
.page-btn.active { background: #246A55; color: white; border-color: #246A55; }
.page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.page-info { font-size: 14px; color: #64748b; margin: 0 12px; }

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .booking-card { flex-direction: column; }
    .booking-image { width: 100%; height: 200px; }
    .booking-meta { grid-template-columns: 1fr; gap: 10px; }
    .card-actions { flex-direction: column; width: 100%; gap: 10px; }
    .card-actions .btn-secondary { width: 100%; text-align: center; display: block; box-sizing: border-box; }
    .page-header-title { font-size: 24px; text-align: center; }
    .page-header-desc { font-size: 13px; text-align: center; }
}

@media (max-width: 480px) {
    .dashboard-container { padding: 15px; }
    .booking-details { padding: 20px; }
    .booking-title { font-size: 18px; }
}
</style>
';
include 'partials/header.php';
?>


    <div class="dashboard-container">
        <!-- Header Row -->
        <div class="page-header-row clearfix" style="margin-bottom: 30px;">
            <div class="headings">
                <h1 class="page-header-title">MY CUSTOM REQUESTS</h1>
                <p class="page-header-desc">Track and manage your custom event package negotiations. Communicate with organizers to tailor your dream event.</p>
            </div>
        </div>

        <?php if (empty($requests)): ?>
            <div class="empty-state">
                <div style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fa-solid fa-message" style="font-size: 32px; color: #94a3b8;"></i>
                </div>
                <h3 style="font-size: 20px; color: #1e293b; margin-bottom: 10px;">No Active Requests</h3>
                <p style="color: #64748b; margin-bottom: 25px;">You haven't initiated any custom event package negotiations yet. Browse events and select "Customize" to get started.</p>
                <a href="/EventManagementSystem/public/client/events" class="btn-browse-more" style="float:none;">Browse Events</a>
            </div>
        <?php else: ?>
            <div class="bookings-grid" id="requests-list">
                <!-- JS will render requests here -->
            </div>
            
            <!-- Pagination Controls -->
            <div id="pagination" class="pagination"></div>
        <?php endif; ?>
    </div>

    <script>
        const allRequests = <?php echo json_encode($requests); ?>;
        let currentPage = 1;
        const itemsPerPage = 5;

        function renderRequests() {
            const listContainer = document.getElementById('requests-list');
            if (!listContainer) return;

            const startIndex = (currentPage - 1) * itemsPerPage;
            const paginatedRequests = allRequests.slice(startIndex, startIndex + itemsPerPage);

            if (paginatedRequests.length === 0) {
                listContainer.innerHTML = '<div class="empty-state"><h3>No requests found.</h3></div>';
                return;
            }

            listContainer.innerHTML = paginatedRequests.map(r => {
                const statusClass = "status-" + r.status.toLowerCase();
                const eventImg = r.event_image ? 
                    (r.event_image[0] === '/' ? r.event_image : '/EventManagementSystem/public/assets/images/events/' + r.event_image) : 
                    '/EventManagementSystem/public/assets/images/placeholder.jpg';
                
                const isConcert = (r.event_category || '').toLowerCase() === 'concert';
                const tierLabel = r.base_package_tier.charAt(0).toUpperCase() + r.base_package_tier.slice(1);
                const guestLabel = isConcert ? ' Tickets' : ' Guests';
                const guestDisplay = r.guest_count ? r.guest_count + guestLabel : 'Quantity TBD';
                const dateDisplay = r.event_date ? 'Requested for ' + formatDate(r.event_date) : 'Date to be confirmed';
                const priceDisplay = new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2 }).format(r.proposed_price);

                return `
                    <div class="booking-card">
                        <div class="booking-image">
                            <img src="${escapeHtml(eventImg)}" alt="Event">
                            <span class="status-badge ${statusClass}">
                                ${r.status.toUpperCase()}
                            </span>
                        </div>
                        <div class="booking-details">
                            <div class="booking-title">${escapeHtml(r.event_title || 'Custom Event Request')}</div>
                            <div class="booking-meta">
                                <span><i class="fa-solid fa-tag"></i> Based on ${tierLabel} Package</span>
                                <span><i class="fa-solid ${isConcert ? 'fa-ticket' : 'fa-user-group'}"></i> ${guestDisplay}</span>
                                <span><i class="fa-regular fa-calendar-check"></i> ${dateDisplay}</span>
                                <span><i class="fa-solid fa-money-bill-wave"></i> Proposed Price: Rs. ${priceDisplay}</span>
                            </div>
                            <div class="card-actions">
                                <a href="/EventManagementSystem/public/client/requests/view?id=${r.id}" class="btn-secondary">
                                    <i class="fa-solid fa-comments"></i> View Thread
                                </a>
                                ${r.status === 'approved' ? `
                                    <a href="/EventManagementSystem/public/client/book?id=${r.group_event_id}&package=${r.base_package_tier}&request_id=${r.id}" class="btn-secondary" style="background: #246A55; color: white; border: none;">
                                        Book Now
                                    </a>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            renderPagination(allRequests.length);
        }

        function renderPagination(totalItems) {
            const container = document.getElementById('pagination');
            if (!container) return;

            const totalPages = Math.ceil(totalItems / itemsPerPage);
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = `
                <button class="page-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <span class="page-info">Page ${currentPage} of ${totalPages}</span>
                <button class="page-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            `;
            container.innerHTML = html;
        }

        function changePage(page) {
            currentPage = page;
            renderRequests();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        function escapeHtml(unsafe) {
            return String(unsafe)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        document.addEventListener('DOMContentLoaded', renderRequests);
    </script>
    </div>

<?php include 'partials/footer.php'; ?>
