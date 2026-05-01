/**
 * Client Browse Events & My Bookings API Integration
 */

document.addEventListener("DOMContentLoaded", () => {
    initBrowseEvents();
    initMyBookings();
    if (window.emsApi && window.emsApi.hydrateProfile) {
        window.emsApi.hydrateProfile();
    }
});

function initBrowseEvents() {
    const searchInput = document.getElementById("event-search-input");
    const eventGrid = document.getElementById("event-grid");
    const paginationContainer = document.getElementById("event-pagination");

    if (!eventGrid) return;

    let currentCategory = new URLSearchParams(window.location.search).get("category") || "All";
    let currentSearch = new URLSearchParams(window.location.search).get("search") || "";
    let currentPage = parseInt(new URLSearchParams(window.location.search).get("page")) || 1;

    if (searchInput) {
        searchInput.addEventListener("input", () => {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(() => {
                currentSearch = searchInput.value;
                currentPage = 1;
                fetchEvents();
            }, 400);
        });
    }

    window.triggerSearch = () => {
        if (searchInput) currentSearch = searchInput.value;
        currentPage = 1;
        fetchEvents();
    };

    window.filterByCategory = (cat, btn) => {
        currentCategory = cat;
        currentPage = 1;
        document.querySelectorAll(".category-filters .filter-btn").forEach(b => b.classList.remove("active"));
        if (btn) btn.classList.add("active");
        fetchEvents();
    };

    async function fetchEvents(pushState = true) {
        if (!window.emsApi) return;
        eventGrid.innerHTML = `<div style="grid-column: 1 / -1; text-align: center; padding: 40px;"><i class="fa-solid fa-spinner fa-spin" style="font-size: 32px; color: var(--primary-color);"></i><p style="margin-top: 10px; color: var(--text-gray);">Finding best events for you...</p></div>`;
        try {
            const params = new URLSearchParams({ category: currentCategory, search: currentSearch, page: currentPage, limit: 6 });
            const res = await window.emsApi.apiFetch(`/api/v1/events?${params.toString()}`);
            if (res.success) {
                renderEvents(res.data.items);
                renderPagination(res.data.total, res.data.limit, res.data.page);
                updateCountUI(res.data.items.length, res.data.total);
                if (pushState) {
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set("category", currentCategory);
                    newUrl.searchParams.set("search", currentSearch);
                    newUrl.searchParams.set("page", currentPage);
                    window.history.pushState({}, "", newUrl);
                }
            }
        } catch (err) {
            eventGrid.innerHTML = `<div class="error-state">Failed to load events: ${err.message}</div>`;
        }
    }

    function renderEvents(events) {
        if (!events || events.length === 0) {
            eventGrid.innerHTML = `<div class="no-events" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;"><i class="fa-solid fa-magnifying-glass" style="font-size: 48px; color: #cbd5e1; margin-bottom: 20px; display: block;"></i><h3 style="color: #1e293b; margin-bottom: 10px;">No events found matching your criteria.</h3><p style="color: #64748b;">Try selecting a different category or adjusting your search terms.</p></div>`;
            return;
        }
        eventGrid.innerHTML = events.map(event => {
            const image = event.image_path ? (event.image_path[0] === "/" ? event.image_path : "/EventManagementSystem/public/assets/images/events/" + event.image_path) : "/EventManagementSystem/public/assets/images/placeholder.jpg";
            let startingPrice = 10000;
            try {
                const packages = typeof event.packages === "string" ? JSON.parse(event.packages) : event.packages;
                if (packages) {
                    const prices = Object.values(packages).map(p => p.price).filter(p => !isNaN(p));
                    if (prices.length > 0) startingPrice = Math.min(...prices);
                }
            } catch (e) {}
            return `<div class="event-card"><div class="event-image-container"><img src="${image}" alt="${event.title}" class="event-image"><span class="event-category-tag">${event.category || "Event"}</span></div><div class="event-content"><h3 class="event-title">${event.title}</h3><p class="event-description">${event.description}</p><div class="event-location"><i class="fa-solid fa-location-dot"></i>${event.venue_location || "Location TBD"}</div><div class="event-price">Packages from Rs. ${startingPrice.toLocaleString()}</div><a href="/EventManagementSystem/public/client/events/view?id=${event.id}" class="btn-view-packages">View Packages &rarr;</a></div></div>`;
        }).join("");
    }

    function renderPagination(total, limit, page) {
        if (!paginationContainer) return;
        const totalPages = Math.ceil(total / limit);
        if (totalPages <= 1) { paginationContainer.innerHTML = ""; return; }
        let html = "";
        if (page > 1) html += `<button class="page-item page-link-text" onclick="setPage(${page - 1})">Previous</button>`;
        for (let i = 1; i <= totalPages; i++) { html += `<button class="page-item ${i === page ? "active" : ""}" onclick="setPage(${i})">${i}</button>`; }
        if (page < totalPages) html += `<button class="page-item page-link-text" onclick="setPage(${page + 1})">Next</button>`;
        paginationContainer.innerHTML = html;
        window.setPage = (p) => { currentPage = p; fetchEvents(); window.scrollTo({ top: 0, behavior: "smooth" }); };
    }

    function updateCountUI(showing, total) {
        const countDiv = document.getElementById("event-search-count");
        if (countDiv) countDiv.textContent = `Showing ${showing} of ${total} event campaigns`;
    }

    fetchEvents(false);
}

async function initMyBookings() {
    if (!window.emsApi || !document.getElementById("my-bookings-view")) return;
    try {
        const res = await window.emsApi.apiFetch("/api/v1/bookings");
        if (res.success) {
            window.bookingsData = res.data.items || [];
            updateBookingStats(window.bookingsData);
            if (typeof applyPagination === "function") applyPagination();
        }
    } catch (err) {
        console.warn("Failed to fetch bookings:", err);
    }
}

function updateBookingStats(bookings) {
    const total = bookings.length;
    const confirmed = bookings.filter(b => (b.display_status || b.status) === "confirmed").length;
    const pending = bookings.filter(b => (b.display_status || b.status) === "pending").length;
    const completed = bookings.filter(b => (b.display_status || b.status) === "completed").length;
    const cancelled = bookings.filter(b => (b.display_status || b.status) === "cancelled").length;
    const upcoming = bookings.filter(b => ["pending", "confirmed"].includes(b.display_status || b.status)).length;

    const setStat = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = String(val).padStart(2, "0"); };
    setStat("stat-total-bookings", total);
    setStat("stat-confirmed-bookings", confirmed);
    setStat("stat-pending-bookings", pending);
    setStat("stat-completed-bookings", completed);

    const setTabCount = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    setTabCount("tab-count-all", total);
    setTabCount("tab-count-upcoming", upcoming);
    setTabCount("tab-count-completed", completed);
    setTabCount("tab-count-cancelled", cancelled);
}
