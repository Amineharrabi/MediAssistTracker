

document.addEventListener('DOMContentLoaded', function() {
    initNotifications();
    
    setInterval(checkForNotifications, 60000); 
});

function initNotifications() {
    loadUnreadNotifications();
    
    setupNotificationListeners();
    
    requestBrowserNotificationPermission();
}


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


function setupNotificationListeners() {
    const markAllReadBtn = document.getElementById('mark-all-read');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            markAllNotificationsAsRead();
        });
    }
    
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
                    <button class="btn btn-sm btn-link text-primary mark-read"  data-id="${notification.id}">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}


function markNotificationAsRead(id) {
    fetch(`/api.php?endpoint=mark-notification&id=${id}`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const notification = document.querySelector(`.notification[data-id="${id}"]`);
            if (notification) {
                notification.remove();
            }
            
            loadUnreadNotifications();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}


function markAllNotificationsAsRead() {
    fetch('/api.php?endpoint=mark-all-notifications', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            loadUnreadNotifications();
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}


function checkForNotifications() {
    loadUnreadNotifications();
}


function requestBrowserNotificationPermission() {
    if ('Notification' in window) {
        if (Notification.permission !== 'denied') {
            Notification.requestPermission();
        }
    }
}


function checkAndShowBrowserNotifications(notifications) {
    if (!notifications || notifications.length === 0) return;
    
    if ('Notification' in window && Notification.permission === 'granted') {
        const latestNotification = notifications[0];
        
        const notificationTime = new Date(latestNotification.scheduled_time);
        const now = new Date();
        const timeDiff = (now - notificationTime) / 1000 / 60; // difference in minutes
        
        if (timeDiff <= 1) {
            const notification = new Notification('MediAssist Reminder', {
                body: latestNotification.message,
                icon: '/assets/img/icon.png'
            });
            
            setTimeout(() => {
                notification.close();
            }, 5000);
            
            notification.onclick = function() {
                window.focus();
                markNotificationAsRead(latestNotification.id);
            };
        }
    }
}


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