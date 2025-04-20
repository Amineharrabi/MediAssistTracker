// Notifications handling
document.addEventListener('DOMContentLoaded', function () {
    // Function to create and show a notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show`;
        notification.setAttribute('role', 'alert');

        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Add to the notifications container or create one if it doesn't exist
        let container = document.querySelector('.notifications-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'notifications-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(container);
        }

        container.appendChild(notification);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(notification);
            bsAlert.close();
        }, 5000);
    }

    // Make the function available globally
    window.showNotification = showNotification;
}); 