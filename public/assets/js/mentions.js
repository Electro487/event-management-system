const roles = ['admin', 'client', 'organizer'];

let mentionDropdown = null;
let activeTextarea = null;
let mentionStartIndex = -1;

function initMentions() {
    if (mentionDropdown) return;
    
    mentionDropdown = document.createElement('div');
    mentionDropdown.className = 'mention-dropdown';
    document.body.appendChild(mentionDropdown);

    document.addEventListener('input', function(e) {
        if (e.target && e.target.tagName.toLowerCase() === 'textarea') {
            handleMentionInput(e.target);
        }
    });

    // Handle keyboard navigation inside textarea for dropdown
    document.addEventListener('keydown', function(e) {
        if (mentionDropdown && mentionDropdown.style.display === 'block' && activeTextarea === e.target) {
            const items = mentionDropdown.querySelectorAll('.mention-item');
            let selectedIdx = Array.from(items).findIndex(item => item.classList.contains('selected'));
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (selectedIdx < items.length - 1) {
                    if (selectedIdx >= 0) items[selectedIdx].classList.remove('selected');
                    items[selectedIdx + 1].classList.add('selected');
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (selectedIdx > 0) {
                    items[selectedIdx].classList.remove('selected');
                    items[selectedIdx - 1].classList.add('selected');
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (selectedIdx >= 0) {
                    const role = items[selectedIdx].innerText.trim().toLowerCase();
                    insertMention(role);
                }
            } else if (e.key === 'Escape') {
                hideMentionDropdown();
            }
        }
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (mentionDropdown && mentionDropdown.style.display === 'block') {
            if (!mentionDropdown.contains(e.target) && e.target !== activeTextarea) {
                hideMentionDropdown();
            }
        }
    });
}

function handleMentionInput(textarea) {
    const val = textarea.value;
    const cursorPos = textarea.selectionStart;
    
    // Find if we are currently typing a mention
    const textBeforeCursor = val.substring(0, cursorPos);
    const lastAtPos = textBeforeCursor.lastIndexOf('@');
    
    if (lastAtPos !== -1) {
        // check if @ is preceded by space or start of line
        if (lastAtPos === 0 || /\s/.test(textBeforeCursor[lastAtPos - 1])) {
            const query = textBeforeCursor.substring(lastAtPos + 1);
            if (!/\s/.test(query)) {
                // Show dropdown
                showMentionDropdown(textarea, query, lastAtPos);
                return;
            }
        }
    }
    hideMentionDropdown();
}

function showMentionDropdown(textarea, query, atPos) {
    activeTextarea = textarea;
    mentionStartIndex = atPos;
    
    const filteredRoles = roles.filter(r => r.toLowerCase().includes(query.toLowerCase()));
    
    if (filteredRoles.length === 0) {
        hideMentionDropdown();
        return;
    }
    
    mentionDropdown.innerHTML = '';
    filteredRoles.forEach((role, idx) => {
        const item = document.createElement('div');
        item.className = 'mention-item' + (idx === 0 ? ' selected' : '');
        item.innerHTML = `<i class="fa-solid fa-user-circle" style="color: #cbd5e1;"></i> <span style="text-transform: capitalize;">${role}</span>`;
        item.onclick = (e) => {
            e.preventDefault();
            insertMention(role);
        };
        item.onmouseenter = () => {
            mentionDropdown.querySelectorAll('.mention-item').forEach(el => el.classList.remove('selected'));
            item.classList.add('selected');
        };
        mentionDropdown.appendChild(item);
    });
    
    const rect = textarea.getBoundingClientRect();
    mentionDropdown.style.display = 'block';
    
    // Try to position near bottom of textarea
    mentionDropdown.style.top = (window.scrollY + rect.bottom + 5) + 'px';
    mentionDropdown.style.left = (window.scrollX + rect.left) + 'px';
}

function hideMentionDropdown() {
    if (mentionDropdown) {
        mentionDropdown.style.display = 'none';
    }
    activeTextarea = null;
    mentionStartIndex = -1;
}

function insertMention(role) {
    if (!activeTextarea) return;
    const val = activeTextarea.value;
    const cursorPos = activeTextarea.selectionStart;
    
    const textBefore = val.substring(0, mentionStartIndex);
    const textAfter = val.substring(cursorPos);
    
    const newText = textBefore + '@' + role + ' ' + textAfter;
    activeTextarea.value = newText;
    
    const newCursorPos = mentionStartIndex + role.length + 2; // +2 for @ and space
    activeTextarea.setSelectionRange(newCursorPos, newCursorPos);
    activeTextarea.focus();
    
    hideMentionDropdown();
}

document.addEventListener('DOMContentLoaded', initMentions);
