<?php
// Session is started in index.php
define('ROOT_PATH', dirname(__DIR__));
define('TEMPLATE_PATH', ROOT_PATH . '/templates');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('ASSETS_PATH', ROOT_PATH . '/assets');

date_default_timezone_set('UTC');

define('APP_NAME', 'MediAssist');
define('APP_URL', (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);
define('SECRET_KEY', getenv('SESSION_SECRET') ?: 'mediassist_dev_key');

$db = require_once ROOT_PATH . '/config/database.php';

require_once INCLUDES_PATH . '/functions.php';

if (!isset($_SESSION['flash_messages'])) {
    $_SESSION['flash_messages'] = [];
}

$theme = isset($_SESSION['user_theme']) ? $_SESSION['user_theme'] : 'light';

$current_year = date('Y');
