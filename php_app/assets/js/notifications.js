/**
 * MediAssist - Notifications JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize notifications
    initNotifications();
    
    // Set up notification polling
    setInterval(checkForNotifications, 60000); // Check every minute
});

/**
 * Initializes the notification system
 */
function initNotifications() {
    // Load unread notifications
    loadUnreadNotifications();
    
    // Set up event listeners
    setupNotificationListeners();
    
    // Request browser notification permission
    requestBrowserNotificationPermission();
}

/**
 * Loads unread notifications from the server
 */
function loadUnreadNotifications() {
    fetch('/api.php?endpoint=unread-notifications')
        .then(response => response.json())
        .then(notifications => {
            updateNotificationBadge(notifications.length);
            updateNotificationDropdown(notifications);
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
    // Mark all notifications as read
    const markAllReadBtn = document.getElementById('mark-all-read');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            markAllNotificationsAsRead();
        });
    }
    
    // Set up delegation for mark as read buttons
    const notificationsContainer = document.getElementById('notifications-container');
    if (notificationsContainer) {
        notificationsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('mark-read') || e.target.closest('.mark-read')) {
                e.preventDefault();
                
                const button = e.target.classList.contains('mark-read') ? 
                               e.target : e.target.closest('.mark-read');
                               
                const notificationId = button.getAttribute('data-id');
                markNotificationAsRead(notificationId);
            }
        });
    }
}

/**
 * Updates the notification count badge
 * @param {number} count - The number of unread notifications
 */
function updateNotificationBadge(count) {
    const badge = document.getElementById('notification-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }
}

/**
 * Updates the notification dropdown with new notifications
 * @param {Array} notifications - Array of notification objects
 */
function updateNotificationDropdown(notifications) {
    const container = document.getElementById('notifications-container');
    if (!container) return;
    
    if (!notifications || notifications.length === 0) {
        container.innerHTML = '<div class="dropdown-item text-center">No unread notifications</div>';
        return;
    }
    
    let html = '';
    
    notifications.forEach(notification => {
        // Format the notification time
        const notificationTime = new Date(notification.scheduled_time);
        const timeAgo = getTimeAgo(notificationTime);
        
        html += `
            <div class="dropdown-item notification" data-id="${notification.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1">${escapeHtml(notification.message)}</p>
                        <small class="text-muted">${timeAgo}</small>
                    </div>
                    <button class="btn btn-sm btn-link text-primary mark-read" data-id="${notification.id}">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

/**
 * Marks a notification as read
 * @param {string} id - The notification ID
 */
function markNotificationAsRead(id) {
    fetch(`/api.php?endpoint=mark-notification&id=${id}`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Remove the notification from the dropdown
            const notification = document.querySelector(`.notification[data-id="${id}"]`);
            if (notification) {
                notification.remove();
            }
            
            // Reload unread notifications to update badge
            loadUnreadNotifications();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

/**
 * Marks all notifications as read
 */
function markAllNotificationsAsRead() {
    fetch('/api.php?endpoint=mark-all-notifications', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Reload unread notifications
            loadUnreadNotifications();
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

/**
 * Checks for new notifications and updates the UI
 */
function checkForNotifications() {
    loadUnreadNotifications();
}

/**
 * Requests permission for browser notifications
 */
function requestBrowserNotificationPermission() {
    if ('Notification' in window) {
        if (Notification.permission !== 'denied') {
            Notification.requestPermission();
        }
    }
}

/**
 * Checks if there are new notifications and shows browser notifications
 * @param {Array} notifications - Array of notification objects
 */
function checkAndShowBrowserNotifications(notifications) {
    if (!notifications || notifications.length === 0) return;
    
    // Only show browser notifications if permission is granted
    if ('Notification' in window && Notification.permission === 'granted') {
        // Get the most recent notification
        const latestNotification = notifications[0];
        
        // Check if this notification is recent (within the last minute)
        const notificationTime = new Date(latestNotification.scheduled_time);
        const now = new Date();
        const timeDiff = (now - notificationTime) / 1000 / 60; // difference in minutes
        
        if (timeDiff <= 1) {
            // Show browser notification
            const notification = new Notification('MediAssist Reminder', {
                body: latestNotification.message,
                icon: '/assets/img/icon.png'
            });
            
            // Close the notification after 5 seconds
            setTimeout(() => {
                notification.close();
            }, 5000);
            
            // Handle notification click
            notification.onclick = function() {
                window.focus();
                markNotificationAsRead(latestNotification.id);
            };
        }
    }
}

/**
 * Gets a human-readable time difference string
 * @param {Date} date - The date to compare with now
 * @returns {string} Human-readable time difference
 */
function getTimeAgo(date) {
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // difference in seconds
    
    if (diff < 60) {
        return 'Just now';
    } else if (diff < 3600) {
        const minutes = Math.floor(diff / 60);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diff < 86400) {
        const hours = Math.floor(diff / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else if (diff < 2592000) {
        const days = Math.floor(diff / 86400);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    } else {
        return date.toLocaleDateString();
    }
}