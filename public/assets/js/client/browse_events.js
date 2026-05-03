/**
 * Client Browse Events API Integration
 * Handles live search, category filtering, and pagination without page reloads.
 */

document.addEventListener('DOMContentLoaded', () => {
    initBrowseEvents();
});

function initBrowseEvents() {
    const searchInput = document.querySelector('.search-input');
    const searchForm = document.querySelector('.search-bar');
    const categoryBtns = document.querySelectorAll('.category-filters .filter-btn');
    const eventGrid = document.querySelector('.event-grid');
    const paginationContainer = document.querySelector('.pagination');

    if (!eventGrid) return;

    let currentCategory = new URLSearchParams(window.location.search).get('category') || 'All';
    let currentSearch = new URLSearchParams(window.location.search).get('search') || '';
    let currentPage = parseInt(new URLSearchParams(window.location.search).get('page')) || 1;

    // Handle Category Clicks
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const url = new URL(btn.href);
            currentCategory = url.searchParams.get('category') || 'All';
            currentPage = 1; // Reset to page 1 on category change
            
            updateCategoryUI(currentCategory);
            fetchEvents();
        });
    });

    // Handle Live Search (Debounced)
    let searchTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = searchInput.value;
            currentPage = 1;
            fetchEvents();
        }, 400);
    });

    // Prevent form submission reload
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        currentSearch = searchInput.value;
        currentPage = 1;
        fetchEvents();
    });

    // Handle Popstate (Browser Back/Forward)
    window.addEventListener('popstate', () => {
        const params = new URLSearchParams(window.location.search);
        currentCategory = params.get('category') || 'All';
        currentSearch = params.get('search') || '';
        currentPage = parseInt(params.get('page')) || 1;
        
        searchInput.value = currentSearch;
        updateCategoryUI(currentCategory);
        fetchEvents(false); // Don't push state again
    });

    async function fetchEvents(pushState = true) {
        if (!window.emsApi) return;

        // Show loading state
        eventGrid.style.opacity = '0.5';
        eventGrid.style.pointerEvents = 'none';

        try {
            const params = new URLSearchParams({
                category: currentCategory,
                search: currentSearch,
                page: currentPage,
                limit: 6
            });

            const res = await window.emsApi.apiFetch(`/api/v1/events?${params.toString()}`);
            
            if (res.success) {
                renderEvents(res.data.items);
                renderPagination(res.data.total, res.data.limit, res.data.page);
                updateCountUI(res.data.items.length, res.data.total);
                
                if (pushState) {
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('category', currentCategory);
                    newUrl.searchParams.set('search', currentSearch);
                    newUrl.searchParams.set('page', currentPage);
                    window.history.pushState({}, '', newUrl);
                }
            }
        } catch (err) {
            console.error('Search failed:', err);
        } finally {
            eventGrid.style.opacity = '1';
            eventGrid.style.pointerEvents = 'auto';
        }
    }

    function renderEvents(events) {
        if (!events || events.length === 0) {
            eventGrid.innerHTML = `
                <div class="no-events" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <i class="fa-solid fa-magnifying-glass" style="font-size: 48px; color: #cbd5e1; margin-bottom: 20px; display: block;"></i>
                    <h3 style="color: #1e293b; margin-bottom: 10px;">No events found matching your criteria.</h3>
                    <p style="color: #64748b;">Try selecting a different category or adjusting your search terms.</p>
                </div>
            `;
            return;
        }

        eventGrid.innerHTML = events.map(event => {
            const image = event.image_path ? (event.image_path[0] === '/' ? event.image_path : '/EventManagementSystem/public/assets/images/events/' + event.image_path) : '/EventManagementSystem/public/assets/images/placeholder.jpg';
            const category = event.category || 'Event';
            
            // Calculate starting price
            let startingPrice = 10000;
            try {
                const packages = JSON.parse(event.packages || '{}');
                const prices = Object.values(packages).map(p => p.price).filter(p => !isNaN(p));
                if (prices.length > 0) startingPrice = Math.min(...prices);
            } catch (e) {}

            return `
                <div class="event-card">
                    <div class="event-image-container">
                        <img src="${image}" alt="${event.title}" class="event-image">
                        <span class="event-category-tag">${category}</span>
                    </div>
                    <div class="event-content">
                        <h3 class="event-title">${event.title}</h3>
                        <p class="event-description">${event.description}</p>
                        <div class="event-location">
                            <i class="fa-solid fa-location-dot"></i>
                            ${event.venue_location || 'Location TBD'}
                        </div>
                        <div class="event-price">Packages from Rs. ${startingPrice.toLocaleString()}</div>
                        <a href="/EventManagementSystem/public/client/events/view?id=${event.id}" class="btn-view-packages">
                            ${category.toLowerCase() === 'concert' ? 'Get Ticket &rarr;' : 'View Packages &rarr;'}
                        </a>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderPagination(total, limit, page) {
        if (!paginationContainer) return;
        
        const totalPages = Math.ceil(total / limit);
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let html = '';
        
        // Previous
        if (page > 1) {
            html += `<a href="#" class="page-item page-link-text" data-page="${page - 1}">Previous</a>`;
        }

        // Pages
        for (let i = 1; i <= totalPages; i++) {
            html += `<a href="#" class="page-item ${i === page ? 'active' : ''}" data-page="${i}">${i}</a>`;
        }

        // Next
        if (page < totalPages) {
            html += `<a href="#" class="page-item page-link-text" data-page="${page + 1}">Next</a>`;
        }

        paginationContainer.innerHTML = html;

        // Add Listeners
        paginationContainer.querySelectorAll('.page-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                currentPage = parseInt(item.dataset.page);
                fetchEvents();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }

    function updateCategoryUI(category) {
        categoryBtns.forEach(btn => {
            const url = new URL(btn.href);
            const btnCat = url.searchParams.get('category') || 'All';
            if (btnCat === category) btn.classList.add('active');
            else btn.classList.remove('active');
        });
    }

    function updateCountUI(showing, total) {
        const countDiv = document.getElementById('event-search-count');
        if (countDiv) {
            countDiv.textContent = `Showing ${showing} of ${total} event campaigns`;
        }
    }
}
