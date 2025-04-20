document.addEventListener('DOMContentLoaded', function () {
    // Get all flash messages
    const flashMessages = document.querySelectorAll('.flash-message');

    flashMessages.forEach(function (message) {
        // Add fade out animation after 5 seconds
        setTimeout(function () {
            message.style.animation = 'fadeOut 0.5s ease-out forwards';
        }, 5000);

        // Remove the element after animation completes
        message.addEventListener('animationend', function (e) {
            if (e.animationName === 'fadeOut') {
                message.remove();
            }
        });
    });
});

// Add fadeOut animation to the stylesheet
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }
`;
document.head.appendChild(style); 