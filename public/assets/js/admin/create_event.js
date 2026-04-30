/**
 * Admin Create/Edit Event JS
 */

document.addEventListener('DOMContentLoaded', () => {
    initImageUpload();
    initPackageManagement();
    initFormSubmission();
    
    if (window.IS_EDIT && window.EVENT_ID) {
        populateEditData();
    }
});

async function populateEditData() {
    try {
        const res = await window.emsApi.apiFetch(`/api/v1/events/${window.EVENT_ID}`);
        if (!res.success || !res.data.event) {
            alert('Failed to load event data.');
            return;
        }

        const e = res.data.event;

        // Basic Info
        document.getElementById('event_title_input').value = e.title || '';
        document.getElementById('event_description_input').value = e.description || '';
        document.getElementById('event_venue_name_input').value = e.venue_name || '';
        document.getElementById('event_venue_location_input').value = e.venue_location || '';
        
        // Category Dropdown
        const catInput = document.getElementById('event_category_input');
        const catValSpan = document.getElementById('cat-selected-val');
        if (catInput && e.category) {
            catInput.value = e.category;
            if (catValSpan) catValSpan.textContent = e.category;
            
            // update active class in dropdown
            document.querySelectorAll('#categoryDropdown .dropdown-item').forEach(item => {
                if (item.dataset.value === e.category) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        }

        // Image
        if (e.image_path) {
            const preview = document.getElementById('image-preview');
            const wrap = document.getElementById('upload-preview-wrap');
            const removeBtn = document.getElementById('remove-image');
            
            preview.src = e.image_path;
            preview.style.display = 'block';
            wrap.style.display = 'none';
            removeBtn.style.display = 'inline-block';
        }

        // Packages
        if (e.packages) {
            let pkgs = {};
            try { pkgs = typeof e.packages === 'string' ? JSON.parse(e.packages) : e.packages; } catch(err){}
            
            Object.keys(pkgs).forEach(tier => {
                const pkg = pkgs[tier];
                
                const descInput = document.getElementById(`pkg_desc_${tier}`);
                if (descInput) descInput.value = pkg.description || '';
                
                const priceInput = document.getElementById(`pkg_price_${tier}`);
                if (priceInput) priceInput.value = pkg.price || pkg.price_range || '';
                
                // Items
                const itemsList = document.querySelector(`.items-list[data-tier="${tier}"]`);
                if (itemsList && pkg.items && Array.isArray(pkg.items)) {
                    itemsList.innerHTML = ''; // clear default
                    pkg.items.forEach((item, idx) => {
                        const row = document.createElement('div');
                        row.className = 'item-row';
                        row.innerHTML = `
                            <span class="drag-handle">⠿</span>
                            <div class="item-content">
                                <strong>${escapeHtml(item.title)}</strong>
                                <p>${escapeHtml(item.description)}</p>
                                <input type="hidden" name="packages[${tier}][items][${idx}][title]" value="${escapeHtml(item.title)}">
                                <input type="hidden" name="packages[${tier}][items][${idx}][description]" value="${escapeHtml(item.description)}">
                            </div>
                            <div class="item-actions">
                                <button type="button" class="icon-action-btn edit-item-btn"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button type="button" class="icon-action-btn delete-item-btn"><i class="fa-solid fa-trash-can"></i></button>
                            </div>
                        `;
                        itemsList.appendChild(row);
                        bindRowEventsGlobal(row, tier);
                    });
                }
            });
        }
        
    } catch (err) {
        console.error("Error populating data", err);
    }
}

function initImageUpload() {
    const uploadTrigger = document.getElementById('upload-trigger');
    const fileInput = document.getElementById('file-input');
    const imagePreview = document.getElementById('image-preview');
    const removeImageBtn = document.getElementById('remove-image');
    const uploadPreviewWrap = document.getElementById('upload-preview-wrap');
    const dropArea = document.getElementById('drop-area');

    if (!uploadTrigger) return;

    uploadTrigger.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                uploadPreviewWrap.style.display = 'none';
                removeImageBtn.style.display = 'inline-block';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    removeImageBtn.addEventListener('click', () => {
        fileInput.value = '';
        imagePreview.style.display = 'none';
        removeImageBtn.style.display = 'none';
        uploadPreviewWrap.style.display = 'flex';
    });

    dropArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropArea.classList.add('drag-over');
    });
    dropArea.addEventListener('dragleave', () => dropArea.classList.remove('drag-over'));
    dropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        dropArea.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });
}

function initPackageManagement() {
    let currentTierForAdd = null;
    let currentEditRow = null;

    // Add Section
    document.querySelectorAll('.add-section-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentTierForAdd = this.dataset.tier;
            document.getElementById('newSectionTitle').value = '';
            document.getElementById('newSectionDesc').value = '';
            document.getElementById('addSectionModal').style.display = 'flex';
            document.getElementById('newSectionTitle').focus();
        });
    });

    document.getElementById('closeModal')?.addEventListener('click', () => {
        document.getElementById('addSectionModal').style.display = 'none';
    });

    document.getElementById('confirmAddSection')?.addEventListener('click', () => {
        const title = document.getElementById('newSectionTitle').value.trim();
        const desc = document.getElementById('newSectionDesc').value.trim();
        if (!title) { alert('Title required'); return; }

        const itemsList = document.querySelector(`.items-list[data-tier="${currentTierForAdd}"]`);
        const newRow = buildItemRow(currentTierForAdd, title, desc || 'Description here...');
        itemsList.appendChild(newRow);
        window.bindRowEventsGlobal(newRow, currentTierForAdd);
        document.getElementById('addSectionModal').style.display = 'none';
    });

    // Edit Modal
    document.getElementById('closeEditModal')?.addEventListener('click', () => {
        document.getElementById('editSectionModal').style.display = 'none';
    });

    document.getElementById('confirmEditSection')?.addEventListener('click', () => {
        const title = document.getElementById('editSectionTitle').value.trim();
        const desc = document.getElementById('editSectionDesc').value.trim();
        if (!title) { alert('Title required'); return; }
        if (window.currentEditRow) {
            window.currentEditRow.querySelector('.item-content strong').textContent = title;
            window.currentEditRow.querySelector('.item-content p').textContent = desc;
            window.currentEditRow.querySelector('input[name*="[title]"]').value = title;
            window.currentEditRow.querySelector('input[name*="[description]"]').value = desc;
        }
        document.getElementById('editSectionModal').style.display = 'none';
    });

    // Bind existing
    document.querySelectorAll('.items-list').forEach(list => {
        const tier = list.dataset.tier;
        list.querySelectorAll('.item-row').forEach((row, idx) => {
            if (!row.querySelector('input[type="hidden"]')) {
                const t = row.querySelector('strong').textContent;
                const d = row.querySelector('p').textContent;
                row.querySelector('.item-content').insertAdjacentHTML('beforeend', `
                    <input type="hidden" name="packages[${tier}][items][${idx}][title]" value="${escapeHtml(t)}">
                    <input type="hidden" name="packages[${tier}][items][${idx}][description]" value="${escapeHtml(d)}">
                `);
            }
            window.bindRowEventsGlobal(row, tier);
        });
    });
}

window.currentEditRow = null;

function buildItemRow(tier, title, desc) {
    const row = document.createElement('div');
    row.className = 'item-row';
    const index = document.querySelectorAll(`.items-list[data-tier="${tier}"] .item-row`).length;
    row.innerHTML = `
        <span class="drag-handle">⠿</span>
        <div class="item-content">
            <strong>${escapeHtml(title)}</strong>
            <p>${escapeHtml(desc)}</p>
            <input type="hidden" name="packages[${tier}][items][${index}][title]" value="${escapeHtml(title)}">
            <input type="hidden" name="packages[${tier}][items][${index}][description]" value="${escapeHtml(desc)}">
        </div>
        <div class="item-actions">
            <button type="button" class="icon-action-btn edit-item-btn"><i class="fa-solid fa-pen-to-square"></i></button>
            <button type="button" class="icon-action-btn delete-item-btn"><i class="fa-solid fa-trash-can"></i></button>
        </div>
    `;
    return row;
}

window.bindRowEventsGlobal = function(row, tier) {
    row.querySelector('.edit-item-btn').addEventListener('click', () => {
        window.currentEditRow = row;
        document.getElementById('editSectionTitle').value = row.querySelector('strong').textContent;
        document.getElementById('editSectionDesc').value = row.querySelector('p').textContent;
        document.getElementById('editSectionModal').style.display = 'flex';
    });

    row.querySelector('.delete-item-btn').addEventListener('click', () => {
        if (confirm('Remove this section?')) {
            row.remove();
            renumberItems(tier);
        }
    });
};

function renumberItems(tier) {
    document.querySelectorAll(`.items-list[data-tier="${tier}"] .item-row`).forEach((row, index) => {
        row.querySelector('input[name*="[title]"]').name = `packages[${tier}][items][${index}][title]`;
        row.querySelector('input[name*="[description]"]').name = `packages[${tier}][items][${index}][description]`;
    });
}

function initFormSubmission() {
    const form = document.getElementById('createEventForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        
        // Add status
        const submitButton = e.submitter;
        const status = submitButton ? (submitButton.value || 'active') : 'active';
        formData.set('status', status);

        // Validation
        const basic = parseInt(formData.get('packages[basic][price]'));
        const standard = parseInt(formData.get('packages[standard][price]'));
        const premium = parseInt(formData.get('packages[premium][price]'));

        if (basic >= standard || standard >= premium) {
            alert('Price order must be: Basic < Standard < Premium.');
            return;
        }

        try {
            const isEdit = window.IS_EDIT;
            const eventId = window.EVENT_ID;
            const url = isEdit ? `/api/v1/events/${eventId}` : '/api/v1/events';
            const method = isEdit ? 'POST' : 'POST'; // We use POST with _method or just POST for multipart
            
            if (isEdit) formData.append('_method', 'PUT');

            const res = await window.emsApi.apiFetch(url, {
                method: 'POST', // Always POST for FormData/Uploads
                body: formData
            });

            if (res.success) {
                window.location.href = '/EventManagementSystem/public/admin/events';
            } else {
                alert(res.message || 'Failed to save event.');
            }
        } catch (err) {
            console.error('Submission error:', err);
            alert('An error occurred during submission.');
        }
    });

    document.getElementById('save-draft-btn')?.addEventListener('click', () => {
        const btn = document.querySelector('.btn-publish');
        const draftInput = document.createElement('input');
        draftInput.type = 'hidden';
        draftInput.name = 'status';
        draftInput.value = 'draft';
        form.appendChild(draftInput);
        form.requestSubmit();
    });
}

function escapeHtml(text) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(text));
    return d.innerHTML;
}
