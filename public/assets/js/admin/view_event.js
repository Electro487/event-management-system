/**
 * Admin View Event API Integration
 */

let currentEvent = null;
let allPackages = {};

document.addEventListener('DOMContentLoaded', () => {
    loadEventDetail();
});

async function loadEventDetail() {
    const eventId = window.EVENT_ID;
    if (!eventId || !window.emsApi) return;

    try {
        const res = await window.emsApi.apiFetch(`/api/v1/events/${eventId}`);
        if (res.success) {
            currentEvent = res.data.event;
            populateUI(currentEvent);
        }
    } catch (err) {
        console.error('Failed to load event detail:', err);
    }
}

function populateUI(event) {
    // Hero
    document.getElementById('hero-img').src = event.image_path || '/EventManagementSystem/public/assets/images/placeholder.jpg';
    document.getElementById('event-category-label').innerText = event.category || 'Event';
    document.getElementById('event-title-header').innerText = event.title;
    document.getElementById('event-tagline').innerText = "Curating timeless moments for your once-in-a-lifetime celebration with architectural precision.";

    // About
    const descBody = document.getElementById('event-description-body');
    if (event.description) {
        descBody.innerText = event.description;
    } else {
        descBody.innerText = "Your event is a tapestry of moments that define your journey together. At e-Plan, we specialize in transforming your vision into an architectural masterpiece of floral arrangements, curated catering, and seamless logistical execution.";
    }

    // Details
    document.getElementById('event-venue-name').innerText = event.venue_name || event.venue_location || 'Location TBD';
    document.getElementById('event-venue-location').innerText = event.venue_location || '';
    
    // Schedule handling
    const scheduleCol = document.getElementById('scheduleColumn');
    const scheduleVal = document.getElementById('eventSchedule');
    if (event.event_date && scheduleCol && scheduleVal) {
        const dt = new Date(event.event_date);
        const formatted = dt.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) + ' @ ' + dt.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        scheduleVal.textContent = formatted;
        scheduleCol.style.display = 'block';
    }

    const statusLabel = document.getElementById('event-status-label');
    statusLabel.innerText = event.status === 'active' ? 'Booking Open' : (event.status.charAt(0).toUpperCase() + event.status.slice(1));
    statusLabel.className = `status-${event.status === 'active' ? 'open' : 'closed'}`;

    // Packages
    try {
        allPackages = JSON.parse(event.packages || '{}');
    } catch (e) {
        console.error('Error parsing packages:', e);
        allPackages = {};
    }

    renderPackages();
    renderIncludedItems('all');
}

function renderPackages() {
    const container = document.getElementById('packages-list-container');
    const tiers = ['basic', 'standard', 'premium'];
    
    const availableTiers = tiers.filter(t => allPackages[t]);

    if (!availableTiers.length) {
        container.innerHTML = '<p class="no-data">No package information configured.</p>';
        return;
    }

    container.innerHTML = availableTiers.map(tier => {
        const pkg = allPackages[tier];
        const cssClass = tier === 'standard' ? 'standard' : (tier === 'premium' ? 'premium' : '');
        const price = pkg.price || pkg.price_range || '0';
        const priceDisplay = isNaN(price) ? price : `Rs. ${parseFloat(price).toLocaleString()}`;

        return `
            <div class="package-tier ${cssClass}" onclick="selectPackageTier('${tier}', this)">
                ${tier === 'standard' ? '<div class="most-popular-badge">Most Popular</div>' : ''}
                <div class="package-header">
                    <div class="tier-name">${tier.charAt(0).toUpperCase() + tier.slice(1)}</div>
                    <div class="tier-price">${priceDisplay}</div>
                </div>
                <div class="tier-desc">
                    ${pkg.description || 'Complete set of services curated for this tier.'}
                </div>
            </div>
        `;
    }).join('');
}

window.selectPackageTier = function(tier, element) {
    document.querySelectorAll('.package-tier').forEach(el => el.classList.remove('active-tier'));
    if (element) element.classList.add('active-tier');
    renderIncludedItems(tier);
};

function renderIncludedItems(tier) {
    const grid = document.getElementById('includedGrid');
    const subtitle = document.getElementById('whats-included-subtitle');
    let items = [];

    if (tier === 'all') {
        subtitle.innerText = "All Packages Scope";
        // Aggregate all unique items
        const seen = new Set();
        Object.values(allPackages).forEach(pkg => {
            (pkg.items || []).forEach(item => {
                if (!seen.has(item.title)) {
                    items.push(item);
                    seen.add(item.title);
                }
            });
        });
    } else if (allPackages[tier]) {
        subtitle.innerText = `${tier.charAt(0).toUpperCase() + tier.slice(1)} Package Scope`;
        items = allPackages[tier].items || [];
    }

    if (!items.length) {
        // Fallback
        items = [
            { title: 'Bespoke Floral Decoration' },
            { title: 'Premium Heritage Venue Setup' },
            { title: 'Gourmet Multi-cuisine Catering' }
        ];
    }

    grid.innerHTML = items.map(item => `
        <div class="included-item">
            <i class="fa-solid fa-circle-check"></i>
            <div style="display: flex; flex-direction: column;">
                <span>${escapeHtml(item.title)}</span>
                ${item.description ? `<span style="font-size: 11px; color: var(--text-gray); font-weight: normal; margin-top: 2px;">${escapeHtml(item.description)}</span>` : ''}
            </div>
        </div>
    `).join('');
}

function escapeHtml(unsafe) {
    return (unsafe || "").toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
