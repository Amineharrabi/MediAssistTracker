<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Constants
define('ROOT_PATH', dirname(__DIR__));
define('TEMPLATE_PATH', ROOT_PATH . '/templates');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// Time zone
date_default_timezone_set('UTC');

// App settings
define('APP_NAME', 'MediAssist');
define('APP_URL', (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);
define('SECRET_KEY', getenv('SESSION_SECRET') ?: 'mediassist_dev_key');

// Load database connection
$db = require_once ROOT_PATH . '/config/database.php';

// Utility functions
require_once INCLUDES_PATH . '/functions.php';

// Initialize flash messages
if (!isset($_SESSION['flash_messages'])) {
    $_SESSION['flash_messages'] = [];
}

// Get current user theme preference (default: light)
$theme = isset($_SESSION['user_theme']) ? $_SESSION['user_theme'] : 'light';

// Current year for footer
$current_year = date('Y');
