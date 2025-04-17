<?php
/**
 * Utility Functions
 */

/**
 * Redirects to a given URL
 *
 * @param string $url The URL to redirect to
 * @return void
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Adds a flash message to the session
 *
 * @param string $message The message to display
 * @param string $type The type of message (success, danger, warning, info)
 * @return void
 */
function add_flash_message($message, $type = 'info') {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    
    $_SESSION['flash_messages'][] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Gets and clears all flash messages
 *
 * @return array Array of flash messages
 */
function get_flash_messages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    $_SESSION['flash_messages'] = [];
    return $messages;
}

/**
 * Sanitizes user input
 *
 * @param string $input The input to sanitize
 * @return string Sanitized input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generates a password hash
 *
 * @param string $password The password to hash
 * @return string Hashed password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verifies a password against a hash
 *
 * @param string $password The password to verify
 * @param string $hash The hash to verify against
 * @return bool True if password matches, false otherwise
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Checks if user is logged in
 *
 * @return bool True if user is logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Requires user to be logged in
 * Redirects to login page if not logged in
 *
 * @return void
 */
function require_login() {
    if (!is_logged_in()) {
        add_flash_message('Please log in to access this page.', 'info');
        redirect('/login.php');
    }
}

/**
 * Gets current user ID
 *
 * @return int|null User ID if logged in, null otherwise
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Gets the current date in the specified format
 *
 * @param string $format The date format
 * @return string Formatted date
 */
function get_current_date($format = 'Y-m-d') {
    return date($format);
}

/**
 * Formats a date for display
 *
 * @param string $date The date to format (YYYY-MM-DD)
 * @return string Formatted date
 */
function format_date($date) {
    $timestamp = strtotime($date);
    return date('F j, Y', $timestamp);
}

/**
 * Formats a time for display
 *
 * @param string $time The time to format (HH:MM)
 * @return string Formatted time
 */
function format_time($time) {
    $timestamp = strtotime($time);
    return date('g:i A', $timestamp);
}

/**
 * Decodes a JSON string to an array
 *
 * @param string $json The JSON string to decode
 * @return array Decoded JSON array
 */
function json_decode_safe($json) {
    if (empty($json)) {
        return [];
    }
    
    $result = json_decode($json, true);
    return $result !== null ? $result : [];
}

/**
 * Renders a template with variables
 *
 * @param string $template Path to template file
 * @param array $variables Variables to pass to the template
 * @return void
 */
function render($template, $variables = []) {
    global $theme, $current_year;
    
    // Extract variables to make them accessible in the template
    extract($variables);
    
    // Include the template
    include TEMPLATE_PATH . "/$template";
}