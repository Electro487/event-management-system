/**
 * Dropdown Manager
 * Handles global custom premium dropdown components
 */
class DropdownManager {
    static init() {
        document.addEventListener('click', (e) => {
            const allDropdowns = document.querySelectorAll('.custom-premium-dropdown');
            
            // Close other dropdowns when clicking outside or on a new one
            allDropdowns.forEach(dropdown => {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('open');
                }
            });

            // Toggle logic for dropdown triggers
            const trigger = e.target.closest('.dropdown-trigger');
            if (trigger) {
                const parent = trigger.closest('.custom-premium-dropdown');
                if (parent) {
                    parent.classList.toggle('open');
                }
            }

            // Selection logic for dropdown items
            const item = e.target.closest('.dropdown-item');
            if (item) {
                const parent = item.closest('.custom-premium-dropdown');
                const selectedValDisplay = parent.querySelector('.selected-val');
                const menuItems = parent.querySelectorAll('.dropdown-item');
                
                // Update active state
                menuItems.forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                
                // Update display text
                if (selectedValDisplay) {
                    selectedValDisplay.textContent = item.textContent;
                }
                
                // Close menu
                parent.classList.remove('open');

                // Dispatch custom event for external listeners
                const value = item.dataset.value;
                parent.dispatchEvent(new CustomEvent('change', { 
                    detail: { value: value, text: item.textContent } 
                }));

                // Handle legacy form submission if needed (for role updates)
                const hiddenInput = parent.querySelector('input[type="hidden"]');
                if (hiddenInput) {
                    hiddenInput.value = value;
                    const form = parent.closest('form');
                    if (form && parent.dataset.autoSubmit === "true") {
                        form.submit();
                    }
                }
            }
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
