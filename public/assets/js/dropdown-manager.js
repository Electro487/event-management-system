/**
 * Dropdown Manager
 * Handles global custom premium dropdown components
 */
class DropdownManager {
    static init() {
        document.addEventListener('click', (e) => {
            const allDropdowns = document.querySelectorAll('.custom-premium-dropdown');
            
            // 1. Check if clicking a dropdown trigger
            const trigger = e.target.closest('.dropdown-trigger');
            if (trigger) {
                const parent = trigger.closest('.custom-premium-dropdown');
                if (parent) {
                    const isOpen = parent.classList.contains('open');
                    
                    // Close all dropdowns
                    allDropdowns.forEach(d => {
                        d.classList.remove('open');
                        const row = d.closest('tr');
                        if (row) row.classList.remove('dropdown-open-row');
                    });

                    // If the one we clicked wasn't open, open it
                    if (!isOpen) {
                        parent.classList.add('open');
                        const activeRow = parent.closest('tr');
                        if (activeRow) activeRow.classList.add('dropdown-open-row');
                    }
                }
                return;
            }

            // 2. Check if clicking a dropdown item
            const item = e.target.closest('.dropdown-item');
            if (item) {
                const parent = item.closest('.custom-premium-dropdown');
                if (parent) {
                    const selectedValDisplay = parent.querySelector('.selected-val');
                    const menuItems = parent.querySelectorAll('.dropdown-item');
                    
                    menuItems.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                    
                    if (selectedValDisplay) {
                        selectedValDisplay.textContent = item.textContent;
                    }
                    
                    parent.classList.remove('open');

                    const value = item.dataset.value;
                    parent.dispatchEvent(new CustomEvent('change', { 
                        detail: { value: value, text: item.textContent } 
                    }));

                    const hiddenInput = parent.querySelector('input[type="hidden"]');
                    if (hiddenInput) {
                        hiddenInput.value = value;
                        const form = parent.closest('form');
                        if (form && parent.dataset.autoSubmit === "true") {
                            form.submit();
                        }
                    }
                }
                return;
            }

            // 3. Clicked completely outside any dropdown trigger or item
            allDropdowns.forEach(d => {
                d.classList.remove('open');
                const row = d.closest('tr');
                if (row) row.classList.remove('dropdown-open-row');
            });
        });
    }

    /**
     * Helper to sync a custom dropdown with an existing logic
     * @param {string} id - The ID of the custom dropdown
     * @param {function} callback - Function to run on change
     */
    static onSelect(id, callback) {
        const dropdown = document.getElementById(id);
        if (dropdown) {
            dropdown.addEventListener('change', (e) => {
                callback(e.detail.value, e.detail.text);
            });
        }
    }
}

// Global initialization
document.addEventListener('DOMContentLoaded', () => {
    DropdownManager.init();
});
