/**
 * MediAssist - Notifications Module
 * Handles notification management and display
 */

// Initialize when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Setup notification system
    initNotifications();
    
    // Periodically check for new notifications
    setInterval(checkForNotifications, 60000); // Check every minute
});

/**
 * Initializes the notification system
 */
function initNotifications() {
    // Check if the user is logged in
    if (!document.body.classList.contains('user-logged-in')) return;
    
    // Load initial notifications
    loadUnreadNotifications();
    
    // Setup event delegation for notification actions
    setupNotificationListeners();
    
    // Enable browser notifications if supported
    requestBrowserNotificationPermission();
}

/**
 * Loads unread notifications from the server
 */
function loadUnreadNotifications() {
    fetch('/api/notifications/unread')
        .then(response => response.json())
        .then(notifications => {
            updateNotificationBadge(notifications.length);
            updateNotificationDropdown(notifications);
            
            // Check if we need to show browser notifications
            checkAndShowBrowserNotifications(notifications);
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });
}

/**
 * Sets up event listeners for notification actions
 */
function setupNotificationListeners() {
    // Mark all as read button
    const markAllReadBtn = document.getElementById('mark-all-read');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', markAllNotificationsAsRead);
    }
    
    // Event delegation for mark as read buttons
    const notificationList = document.getElementById('notification-dropdown');
    if (notificationList) {
        notificationList.addEventListener('click', function(e) {
            if (e.target.classList.contains('mark-read') || 
                e.target.parentElement.classList.contains('mark-read')) {
                
                const button = e.target.classList.contains('mark-read') ? 
                               e.target : e.target.parentElement;
                               
                const notificationId = button.dataset.id;
                if (notificationId) {
                    e.preventDefault();
                    e.stopPropagation();
                    markNotificationAsRead(notificationId);
                }
            }
        });
    }
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
    const container = document.getElementById('notification-list');
    if (!container) return;
    
    const emptyState = document.getElementById('no-notifications');
    
    // Clear existing notifications
    container.innerHTML = '';
    
    if (notifications.length === 0) {
        if (emptyState) emptyState.style.display = 'block';
        return;
    }
    
    if (emptyState) emptyState.style.display = 'none';
    
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
                <button class="btn btn-sm btn-link text-muted mark-read" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;" data-id="${notification.id}" title="Mark as read">
                    <i class="fas fa-check"></i>
                </button>
            </div>
        `;
        
        container.appendChild(notifItem);
    });
}

/**
 * Marks a notification as read
 * @param {string} id - The notification ID
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
        // Remove notification from list or update UI
        const notifItem = document.querySelector(`.notification-item[data-id="${id}"]`);
        if (notifItem) {
            notifItem.classList.remove('unread');
            notifItem.querySelector('.mark-read').style.display = 'none';
        }
        
        // Reload notifications
        loadUnreadNotifications();
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
        // Reload notifications
        loadUnreadNotifications();
        
        // Close dropdown
        const dropdownToggle = document.getElementById('notification-dropdown-toggle');
        if (dropdownToggle) {
            const dropdown = bootstrap.Dropdown.getInstance(dropdownToggle);
            if (dropdown) {
                dropdown.hide();
            }
        }
        
        // Show success message
        showAlert('All notifications marked as read.', 'success');
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

/**
 * Periodically checks for new notifications
 */
function checkForNotifications() {
    // Only check if user is logged in
    if (!document.body.classList.contains('user-logged-in')) return;
    
    // Get current unread count
    const badge = document.getElementById('notification-count');
    const currentCount = badge && badge.style.display !== 'none' ? 
                        parseInt(badge.textContent) || 0 : 0;
    
    fetch('/api/notifications/unread')
        .then(response => response.json())
        .then(notifications => {
            // Only update if count has changed
            if (notifications.length !== currentCount) {
                updateNotificationBadge(notifications.length);
                updateNotificationDropdown(notifications);
                
                // If new notifications were added, show browser notification
                if (notifications.length > currentCount) {
                    checkAndShowBrowserNotifications(
                        notifications.slice(0, notifications.length - currentCount)
                    );
                }
            }
        })
        .catch(error => {
            console.error('Error checking for notifications:', error);
        });
}

/**
 * Requests permission for browser notifications
 */
function requestBrowserNotificationPermission() {
    if (!('Notification' in window)) {
        console.log('This browser does not support desktop notification');
        return;
    }
    
    if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
        Notification.requestPermission();
    }
}

/**
 * Checks if there are new notifications and shows browser notifications
 * @param {Array} notifications - Array of notification objects
 */
function checkAndShowBrowserNotifications(notifications) {
    if (!('Notification' in window) || Notification.permission !== 'granted') {
        return;
    }
    
    // Only show notifications for recent items (last 5 minutes)
    const fiveMinutesAgo = new Date();
    fiveMinutesAgo.setMinutes(fiveMinutesAgo.getMinutes() - 5);
    
    const recentNotifications = notifications.filter(notification => {
        const scheduledTime = new Date(notification.scheduled_time);
        return scheduledTime > fiveMinutesAgo;
    });
    
    recentNotifications.forEach(notification => {
        const notificationOptions = {
            body: notification.message,
            icon: '/static/images/logo.svg',
            badge: '/static/images/badge.svg'
        };
        
        const browserNotification = new Notification('MediAssist Reminder', notificationOptions);
        
        browserNotification.onclick = function() {
            window.focus();
            markNotificationAsRead(notification.id);
            this.close();
        };
        
        // Close notification after 10 seconds
        setTimeout(() => {
            browserNotification.close();
        }, 10000);
    });
}
