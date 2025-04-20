/**
 * MediAssist - Main JavaScript File
 * Handles shared functionality across the application
 */

// Initialize the application when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Bootstrap popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Set up theme switcher
    setupThemeSwitcher();

    // Setup notification checker
    checkNotifications();
    
    // Setup periodic notification check (every minute)
    setInterval(checkNotifications, 60000);
});

/**
 * Sets up the theme switcher functionality
 */
function setupThemeSwitcher() {
    const themeSwitch = document.getElementById('theme-switch');
    if (!themeSwitch) return;

    // Set initial state based on current theme
    const currentTheme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
    themeSwitch.checked = currentTheme === 'dark';

    // Update the theme label
    updateThemeLabel(currentTheme);

    // Handle change event
    themeSwitch.addEventListener('change', function() {
        const newTheme = this.checked ? 'dark' : 'light';
        
        // Submit the form to update user preference
        const form = document.getElementById('theme-form');
        const themeInput = document.getElementById('theme-input');
        
        if (form && themeInput) {
            themeInput.value = newTheme;
            form.submit();
        } else {
            // If form doesn't exist (e.g., user not logged in), just toggle the class
            document.body.classList.toggle('dark-theme', newTheme === 'dark');
            document.body.classList.toggle('light-theme', newTheme === 'light');
            updateThemeLabel(newTheme);
        }
    });
}

/**
 * Updates the theme switch label text
 * @param {string} theme - The current theme ('light' or 'dark')
 */
function updateThemeLabel(theme) {
    const themeLabel = document.getElementById('theme-label');
    if (themeLabel) {
        themeLabel.textContent = theme === 'dark' ? 'Dark Mode' : 'Light Mode';
    }
}

/**
 * Formats a date for display
 * @param {string} dateStr - Date string in YYYY-MM-DD format
 * @returns {string} Formatted date string
 */
function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { 
        weekday: 'short', 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

/**
 * Formats a time for display
 * @param {string} timeStr - Time string in HH:MM format
 * @returns {string} Formatted time string
 */
function formatTime(timeStr) {
    // Handle time strings that might not have seconds
    if (timeStr.split(':').length === 2) {
        timeStr += ':00';
    }
    
    const date = new Date(`2000-01-01T${timeStr}`);
    return date.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
    });
}

/**
 * Shows an alert to the user
 * @param {string} message - The message to display
 * @param {string} type - The type of alert (success, danger, warning, info)
 * @param {string} containerId - The ID of the container element to display the alert in
 */
function showAlert(message, type = 'info', containerId = 'alert-container') {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const alertEl = document.createElement('div');
    alertEl.className = `alert alert-${type} alert-dismissible fade show`;
    alertEl.role = 'alert';
    alertEl.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    container.innerHTML = '';
    container.appendChild(alertEl);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = bootstrap.Alert.getOrCreateInstance(alertEl);
        alert.close();
    }, 5000);
}

/**
 * Checks for new notifications and updates the UI
 */
function checkNotifications() {
    // Only proceed if user is logged in
    if (!document.body.classList.contains('user-logged-in')) return;
    
    fetch('/api/notifications/unread')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.length);
            updateNotificationDropdown(data);
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
        });
}

/**
 * Updates the notification count badge
 * @param {number} count - The number of unread notifications
 */
function updateNotificationBadge(count) {
    const badge = document.getElementById('notification-count');
    if (!badge) return;
    
    if (count > 0) {
        badge.textContent = count > 9 ? '9+' : count;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }
}

/**
 * Updates the notification dropdown with new notifications
 * @param {Array} notifications - Array of notification objects
 */
function updateNotificationDropdown(notifications) {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    // Clear existing notifications
    container.innerHTML = '';
    
    if (notifications.length === 0) {
        container.innerHTML = '<div class="text-center p-3">No new notifications</div>';
        return;
    }
    
    // Add mark all as read button
    const markAllBtn = document.createElement('button');
    markAllBtn.className = 'btn btn-sm btn-outline-primary w-100 mb-2';
    markAllBtn.textContent = 'Mark all as read';
    markAllBtn.addEventListener('click', markAllNotificationsAsRead);
    container.appendChild(markAllBtn);
    
    // Add notifications
    notifications.forEach(notification => {
        const notifItem = document.createElement('div');
        notifItem.className = 'notification-item unread';
        notifItem.dataset.id = notification.id;
        
        const scheduledTime = new Date(notification.scheduled_time);
        const timeFormatted = scheduledTime.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
        
        notifItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong>${notification.message}</strong>
                    <div class="text-muted small">${timeFormatted}</div>
                </div>
                <button class="btn btn-sm btn-link text-muted mark-read" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;" data-id="${notification.id}">
                    <i class="fas fa-check"></i>
                </button>
            </div>
        `;
        
        container.appendChild(notifItem);
        
        // Add event listener to mark as read button
        const markReadBtn = notifItem.querySelector('.mark-read');
        markReadBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            markNotificationAsRead(notification.id);
        });
    });
}

/**
 * Marks a single notification as read
 * @param {number} id - The notification ID
 */
function markNotificationAsRead(id) {
    fetch(`/api/notifications/${id}/mark_read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update UI
        const notifItem = document.querySelector(`.notification-item[data-id="${id}"]`);
        if (notifItem) {
            notifItem.classList.remove('unread');
            notifItem.querySelector('.mark-read').style.display = 'none';
        }
        
        // Refresh notifications
        checkNotifications();
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

/**
 * Marks all notifications as read
 */
function markAllNotificationsAsRead() {
    fetch('/api/notifications/mark_all_read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Refresh notifications
        checkNotifications();
        
        // Close the dropdown
        const dropdown = document.getElementById('notification-dropdown');
        if (dropdown) {
            const bsDropdown = bootstrap.Dropdown.getInstance(dropdown);
            if (bsDropdown) {
                bsDropdown.hide();
            }
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

/**
 * Validates a form input
 * @param {HTMLInputElement} input - The input element to validate
 * @returns {boolean} Whether the input is valid
 */
function validateInput(input) {
    input.classList.remove('is-invalid');
    
    // Check required fields
    if (input.required && !input.value.trim()) {
        input.classList.add('is-invalid');
        const feedbackEl = input.nextElementSibling;
        if (feedbackEl && feedbackEl.classList.contains('invalid-feedback')) {
            feedbackEl.textContent = 'This field is required';
        }
        return false;
    }
    
    // Validate email if it's an email field
    if (input.type === 'email' && input.value.trim()) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(input.value.trim())) {
            input.classList.add('is-invalid');
            const feedbackEl = input.nextElementSibling;
            if (feedbackEl && feedbackEl.classList.contains('invalid-feedback')) {
                feedbackEl.textContent = 'Please enter a valid email address';
            }
            return false;
        }
    }
    
    // Validate phone if it's a phone field
    if (input.type === 'tel' && input.value.trim()) {
        const phoneRegex = /^\+?[0-9\s\-\(\)]{7,20}$/;
        if (!phoneRegex.test(input.value.trim())) {
            input.classList.add('is-invalid');
            const feedbackEl = input.nextElementSibling;
            if (feedbackEl && feedbackEl.classList.contains('invalid-feedback')) {
                feedbackEl.textContent = 'Please enter a valid phone number';
            }
            return false;
        }
    }
    
    return true;
}
