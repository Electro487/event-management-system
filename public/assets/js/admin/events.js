/**
 * Admin Events Management JS
 */

let allEvents = [];
let currentStatus = 'all';
let currentCategory = 'all';
let currentPage = 1;
const ITEMS_PER_PAGE = 6;

document.addEventListener('DOMContentLoaded', () => {
    // Check for search param in URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    const searchInput = document.getElementById('globalSearchInput');
    
    if (searchInput && searchParam) {
        searchInput.value = searchParam;
    }

    loadEvents();
    initFilters();
});

async function loadEvents() {
    if (!window.emsApi) return;

    try {
        const res = await window.emsApi.apiFetch('/api/v1/events?limit=1000');
        if (res.success) {
            allEvents = res.data.items || [];
            applyFilters();
        }
    } catch (err) {
        console.error('Failed to load system events:', err);
    }
}

function initFilters() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentStatus = this.dataset.filterStatus;
            currentPage = 1;
            applyFilters();
        });
    });

    if (window.DropdownManager) {
        window.DropdownManager.onSelect('categoryFilter', (val) => {
            currentCategory = val || 'all';
            currentPage = 1;
            applyFilters();
        });
    }

    // Fallback: Direct listener if DropdownManager is slow or fails
    const catFilter = document.getElementById('categoryFilter');
    if (catFilter) {
        catFilter.addEventListener('change', (e) => {
            if (e.detail && e.detail.value) {
                currentCategory = e.detail.value;
                currentPage = 1;
                applyFilters();
            }
        });
    }

    // Header search
    const searchInput = document.getElementById('globalSearchInput');
    const filterSearch = document.getElementById('filterSearchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            currentPage = 1;
            applyFilters();
        });
    }
    if (filterSearch) {
        filterSearch.addEventListener('input', () => {
            currentPage = 1;
            applyFilters();
        });
    }
}

function applyFilters() {
    let searchQ = '';
    const globalSearch = document.getElementById('globalSearchInput');
    const filterSearch = document.getElementById('filterSearchInput');
    
    if (filterSearch && filterSearch.value) searchQ = filterSearch.value.toLowerCase().trim();
    else if (globalSearch && globalSearch.value) searchQ = globalSearch.value.toLowerCase().trim();
    
    const filtered = allEvents.filter(e => {
        const title = (e.title || '').toLowerCase().trim();
        const org = (e.organizer_name || '').toLowerCase().trim();
        const status = (e.status || '').toLowerCase().trim();
        const cat = (e.category || '').toLowerCase().trim();
        const venue = (e.venue_location || '').toLowerCase().trim();

        const matchSearch = !searchQ || 
                            title.includes(searchQ) || 
                            org.includes(searchQ) || 
                            cat.includes(searchQ) || 
                            venue.includes(searchQ);
        
        const matchStatus = currentStatus === 'all' || status === currentStatus.toLowerCase();
        
        // More robust category matching
        const targetCat = currentCategory.toLowerCase().trim();
        const matchCategory = currentCategory === 'all' || cat === targetCat || cat.includes(targetCat);

        return matchSearch && matchStatus && matchCategory;
    });

    renderGrid(filtered);
}

function renderGrid(events) {
    const grid = document.getElementById('eventsGrid');
    const countLabel = document.getElementById('eventsCountLabel');

    if (countLabel) countLabel.innerText = `Showing ${events.length} system campaigns`;

    if (!events.length) {
        grid.innerHTML = `
            <div class="no-events-message" style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #999;">
                <p>No system events found matching your criteria.</p>
            </div>
        `;
        return;
    }

    // Pagination Logic
    const totalPages = Math.ceil(events.length / ITEMS_PER_PAGE);
    if (currentPage > totalPages) currentPage = totalPages;
    const startIdx = (currentPage - 1) * ITEMS_PER_PAGE;
    const paginatedEvents = events.slice(startIdx, startIdx + ITEMS_PER_PAGE);

    let html = paginatedEvents.map(e => {
        const img = e.image_path || '/EventManagementSystem/public/assets/images/placeholder.jpg';
        const status = (e.status || 'draft').toLowerCase();
        const desc = (e.description || '').substring(0, 80) + '...';

        return `
            <div class="event-card">
                <div class="event-image">
                    <img src="${img}" alt="${e.title || 'Event'}" onerror="this.src='/EventManagementSystem/public/assets/images/placeholder.jpg'">
                    <span class="status-badge ${status}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>
                    <span class="category-tag">${e.category || 'Event'}</span>
                </div>
                <div class="event-details">
                    <h3 class="event-title">${e.title || 'Untitled Event'}</h3>
                    <span class="organizer-tag"><i class="fas fa-user-tie"></i> ${e.organizer_name || 'System'}</span>
                    <p class="event-desc" style="margin-top: 10px;">${desc}</p>
                    
                    <div class="event-actions" style="margin-top: 15px;">
                        <a href="/EventManagementSystem/public/admin/events/edit?id=${e.id}" class="btn-action edit">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </a>

                        <button type="button" class="btn-action delete" onclick="deleteEvent(${e.id})">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    if (totalPages > 1) {
        let pageBtns = '';
        for (let i = 1; i <= totalPages; i++) {
            if (totalPages > 5) {
                if (i > 1 && i < totalPages && Math.abs(i - currentPage) > 1) {
                    if (i === 2 && currentPage > 3) pageBtns += `<span class="dots">...</span>`;
                    if (i === totalPages - 1 && currentPage < totalPages - 2) pageBtns += `<span class="dots">...</span>`;
                    continue;
                }
            }
            pageBtns += `<button class="p-btn num ${currentPage === i ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
        }

        html += `
            <div class="pagination-row" style="grid-column: 1 / -1; margin-top: 20px;">
                <div class="showing-text">Showing ${paginatedEvents.length} of ${events.length} event campaigns</div>
                <div class="pagination-controls" id="paginationControls">
                    <button class="p-btn prev ${currentPage === 1 ? 'disabled' : ''}" onclick="changePage(${currentPage - 1})"><i class="fa-solid fa-angle-left"></i></button>
                    ${pageBtns}
                    <button class="p-btn next ${currentPage === totalPages ? 'disabled' : ''}" onclick="changePage(${currentPage + 1})"><i class="fa-solid fa-angle-right"></i></button>
                </div>
            </div>
        `;
    }

    grid.innerHTML = html;
}

window.changePage = function(page) {
    currentPage = page;
    applyFilters();
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

async function deleteEvent(id) {
    if (!confirm('ADMIN: Are you sure you want to delete this event? This will also affect existing bookings.')) return;

    try {
        const res = await window.emsApi.apiFetch(`/api/v1/events/${id}`, { method: 'DELETE' });
        if (res.success) {
            loadEvents(); // Refresh
        } else {
            alert(res.message || 'Failed to delete event.');
        }
    } catch (err) {
        console.error('Error deleting event:', err);
    }
}
