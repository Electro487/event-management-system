/**
 * Admin User Management API Integration
 */

let allUsers = [];
let filteredUsers = [];
let currentRoleFilter = 'all';
let currentPage = 1;
const itemsPerPage = 10;

document.addEventListener('DOMContentLoaded', () => {
    // Check for search param in URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    
    const searchInput = document.getElementById('userSearchInput');
    const globalSearch = document.getElementById('globalSearchInput');
    
    if (searchParam) {
        if (searchInput) searchInput.value = searchParam;
        if (globalSearch) globalSearch.value = searchParam;
    }

    loadUsers(searchParam || '');

    // Local Search Input
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            if (globalSearch) globalSearch.value = e.target.value;
            applyFilters(e.target.value);
        });
    }

    // Global Search Input
    if (globalSearch) {
        globalSearch.addEventListener('input', (e) => {
            if (searchInput) searchInput.value = e.target.value;
            applyFilters(e.target.value);
        });
    }

    // Role Filter Dropdown
    const roleFilter = document.getElementById('roleFilter');
    if (window.DropdownManager) {
        window.DropdownManager.onSelect('roleFilter', (val) => {
            currentRoleFilter = val || 'all';
            currentPage = 1;
            applyFilters(searchInput?.value || globalSearch?.value || '');
        });
    }

    if (roleFilter) {
        roleFilter.addEventListener('change', (e) => {
            if (e.detail && e.detail.value) {
                currentRoleFilter = e.detail.value;
                currentPage = 1;
                applyFilters(searchInput?.value || globalSearch?.value || '');
            }
        });
    }
});

async function loadUsers(initialSearch = '') {
    if (!window.emsApi) return;

    try {
        const res = await window.emsApi.apiFetch('/api/v1/admin/users');
        if (res.success) {
            allUsers = res.data.users || [];
            updateStats(res.data.stats);
            currentPage = 1;
            applyFilters(initialSearch);
        }
    } catch (err) {
        console.error('Failed to load users:', err);
    }
}

function updateStats(stats) {
    if (!stats) return;
    document.getElementById('stat-total-users').innerText = (stats.total || 0).toLocaleString();
    document.getElementById('stat-clients').innerText = (stats.clients || 0).toLocaleString();
    document.getElementById('stat-organizers').innerText = (stats.organizers || 0).toLocaleString();
    document.getElementById('stat-blocked').innerText = (stats.blocked || 0).toLocaleString();
}

function applyFilters(searchQ = '') {
    searchQ = searchQ.toLowerCase();
    
    filteredUsers = allUsers.filter(u => {
        const name = (u.fullname || '').toLowerCase();
        const email = (u.email || '').toLowerCase();
        const role = (u.role || '').toLowerCase();

        const matchesSearch = name.includes(searchQ) || email.includes(searchQ);
        const matchesRole = currentRoleFilter === 'all' || role === currentRoleFilter;

        return matchesSearch && matchesRole;
    });

    currentPage = 1;
    renderUserTable();
    renderPagination();
}

function renderUserTable() {
    const tbody = document.getElementById('userTableBody');
    if (!filteredUsers.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:40px;">No users found matching your criteria.</td></tr>';
        return;
    }

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageUsers = filteredUsers.slice(start, end);

    tbody.innerHTML = pageUsers.map(u => {
        const initials = (u.fullname || 'U').split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        const avatarHtml = u.profile_picture 
            ? `<img src="${u.profile_picture}" alt="User" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">`
            : `<div style="width: 32px; height: 32px; background: #f0f7f3; color: #246A55; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);">${initials}</div>`;

        const joinedDate = new Date(u.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        const isBlocked = parseInt(u.is_blocked) === 1;

        return `
            <tr class="user-row">
                <td>
                    <div class="client-info" style="display: flex; align-items: center;">
                        ${avatarHtml}
                        <span style="margin-left: 12px; font-weight: 500;">${u.fullname}</span>
                    </div>
                </td>
                <td style="color: #64748b; font-size: 13.5px;">${u.email}</td>
                <td>
                    <span class="role-badge role-${u.role}">
                        ${u.role.charAt(0).toUpperCase() + u.role.slice(1)}
                    </span>
                </td>
                <td style="font-size: 13px; color: #64748b;">${joinedDate}</td>
                <td>
                    <span class="status-badge ${isBlocked ? 'status-blocked' : 'status-active'}">
                        ${isBlocked ? 'Blocked' : 'Active'}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <div class="custom-premium-dropdown small" style="width: 120px;" id="role-dd-${u.id}">
                            <div class="dropdown-trigger" style="height: 32px; padding: 0 10px;">
                                <span class="selected-val">${u.role.charAt(0).toUpperCase() + u.role.slice(1)}</span>
                                <i class="fa-solid fa-angle-down"></i>
                            </div>
                            <div class="dropdown-menu">
                                <div class="dropdown-item ${u.role === 'client' ? 'active' : ''}" data-value="client" onclick="updateUserRole(${u.id}, 'client')">Client</div>
                                <div class="dropdown-item ${u.role === 'organizer' ? 'active' : ''}" data-value="organizer" onclick="updateUserRole(${u.id}, 'organizer')">Organizer</div>
                                <div class="dropdown-item ${u.role === 'admin' ? 'active' : ''}" data-value="admin" onclick="updateUserRole(${u.id}, 'admin')">Admin</div>
                            </div>
                        </div>
                        
                        ${isBlocked 
                            ? `<button type="button" class="action-btn-outline btn-unblock" onclick="toggleUserBlock(${u.id}, 0)">Unblock</button>`
                            : `<button type="button" class="action-btn-outline btn-block" onclick="showBlockModal(${u.id}, '${u.fullname.replace(/'/g, "\\'")}')">Block</button>`
                        }
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    // Re-initialize dropdowns for the newly injected rows
    if (window.DropdownManager) {
        window.DropdownManager.init();
    }
}

function renderPagination() {
    const container = document.getElementById('pagination-container');
    const totalPages = Math.ceil(filteredUsers.length / itemsPerPage);
    
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = `
        <div class="pagination">
            <button class="pg-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                <i class="fa-solid fa-angle-left"></i>
            </button>
    `;

    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html += `<button class="pg-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += `<span class="pg-dots">...</span>`;
        }
    }

    html += `
            <button class="pg-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                <i class="fa-solid fa-angle-right"></i>
            </button>
        </div>
    `;

    container.innerHTML = html;
}

window.changePage = (page) => {
    currentPage = page;
    renderUserTable();
    renderPagination();
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

async function updateUserRole(userId, newRole) {
    if (!confirm(`Are you sure you want to change this user's role to ${newRole}?`)) return;

    try {
        const res = await window.emsApi.apiFetch('/api/v1/admin/users/update-role', {
            method: 'POST',
            body: { user_id: userId, role: newRole }
        });

        if (res.success) {
            loadUsers(); // Refresh
        } else {
            alert(res.message || 'Failed to update role.');
        }
    } catch (err) {
        console.error('Error updating role:', err);
    }
}

async function toggleUserBlock(userId, status) {
    try {
        const res = await window.emsApi.apiFetch('/api/v1/admin/users/toggle-block', {
            method: 'POST',
            body: { user_id: userId, status: status }
        });

        if (res.success) {
            closeBlockModal();
            loadUsers(); // Refresh
        } else {
            alert(res.message || 'Failed to update user status.');
        }
    } catch (err) {
        console.error('Error toggling block:', err);
    }
}

function showBlockModal(userId, name) {
    const modal = document.getElementById('blockModal');
    const modalText = document.getElementById('blockModalText');
    const confirmBtn = document.querySelector('#blockForm button');
    
    document.getElementById('modalUserId').value = userId;
    modalText.innerText = `${name} will no longer be able to log in or access their curated event dashboard. This action can be reversed by an administrator.`;
    
    // Update confirm button to call our JS function
    confirmBtn.type = 'button';
    confirmBtn.onclick = () => toggleUserBlock(userId, 1);
    
    modal.style.display = 'flex';
}

function closeBlockModal() {
    document.getElementById('blockModal').style.display = 'none';
}
