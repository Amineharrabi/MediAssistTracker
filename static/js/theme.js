/**
 * MediAssist - Theme Management Module
 * Handles theme switching functionality
 */

// Initialize when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeTheme();
});

/**
 * Initializes the theme system
 */
function initializeTheme() {
    // Set up theme toggle
    setupThemeToggle();
    
    // Apply the current theme from user preference or local storage
    applyCurrentTheme();
    
    // Listen for system preference changes
    listenForSystemPreferenceChanges();
}

/**
 * Sets up the theme toggle switch
 */
function setupThemeToggle() {
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
        
        // If user is logged in, save preference to server
        if (document.body.classList.contains('user-logged-in')) {
            saveThemePreference(newTheme);
        } else {
            // Otherwise just store in local storage
            localStorage.setItem('theme-preference', newTheme);
            applyTheme(newTheme);
        }
    });
}

/**
 * Updates the theme toggle label
 * @param {string} theme - The current theme ('light' or 'dark')
 */
function updateThemeLabel(theme) {
    const themeIcon = document.getElementById('theme-icon');
    const themeLabel = document.getElementById('theme-label');
    
    if (themeIcon) {
        themeIcon.className = theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
    }
    
    if (themeLabel) {
        themeLabel.textContent = theme === 'dark' ? 'Dark Mode' : 'Light Mode';
    }
}

/**
 * Applies the current theme from user preference or local storage
 */
function applyCurrentTheme() {
    // Check if theme is set in the body class (server-side)
    if (document.body.classList.contains('light-theme') || document.body.classList.contains('dark-theme')) {
        return; // Theme is already set
    }
    
    // Check local storage
    const storedTheme = localStorage.getItem('theme-preference');
    if (storedTheme) {
        applyTheme(storedTheme);
        return;
    }
    
    // Check system preference
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        applyTheme('dark');
    } else {
        applyTheme('light');
    }
}

/**
 * Applies the specified theme
 * @param {string} theme - The theme to apply ('light' or 'dark')
 */
function applyTheme(theme) {
    if (theme === 'dark') {
        document.body.classList.remove('light-theme');
        document.body.classList.add('dark-theme');
    } else {
        document.body.classList.remove('dark-theme');
        document.body.classList.add('light-theme');
    }
    
    // Update meta theme-color for mobile browsers
    const metaThemeColor = document.querySelector('meta[name="theme-color"]');
    if (metaThemeColor) {
        metaThemeColor.setAttribute('content', 
            theme === 'dark' ? '#1A202C' : '#F8FAFC'
        );
    }
    
    // Update theme toggle if it exists
    const themeSwitch = document.getElementById('theme-switch');
    if (themeSwitch) {
        themeSwitch.checked = theme === 'dark';
        updateThemeLabel(theme);
    }
}

/**
 * Saves the user's theme preference to the server
 * @param {string} theme - The theme preference ('light' or 'dark')
 */
function saveThemePreference(theme) {
    const form = document.getElementById('theme-form');
    if (!form) return;
    
    const input = document.getElementById('theme-input');
    if (input) {
        input.value = theme;
        form.submit();
    } else {
        // Fallback to just applying the theme without saving
        applyTheme(theme);
    }
}

/**
 * Listens for system color scheme preference changes
 */
function listenForSystemPreferenceChanges() {
    if (!window.matchMedia) return;
    
    const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    
    // Add change listener
    try {
        // Chrome & Firefox
        darkModeMediaQuery.addEventListener('change', (e) => {
            const newTheme = e.matches ? 'dark' : 'light';
            
            // Only apply if user hasn't set a preference
            if (!localStorage.getItem('theme-preference') && 
                !document.body.classList.contains('user-logged-in')) {
                applyTheme(newTheme);
            }
        });
    } catch (error1) {
        try {
            // Safari
            darkModeMediaQuery.addListener((e) => {
                const newTheme = e.matches ? 'dark' : 'light';
                
                // Only apply if user hasn't set a preference
                if (!localStorage.getItem('theme-preference') && 
                    !document.body.classList.contains('user-logged-in')) {
                    applyTheme(newTheme);
                }
            });
        } catch (error2) {
            console.error('Could not add listener for dark mode changes:', error2);
        }
    }
}
