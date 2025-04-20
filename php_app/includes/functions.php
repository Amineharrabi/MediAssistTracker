<?php
function redirect($url)
{
    header("Location: $url");
    exit;
}


function add_flash_message($message, $type = 'info')
{
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }

    $_SESSION['flash_messages'][] = [
        'message' => $message,
        'type' => $type,
        'timestamp' => time()
    ];
}


function get_flash_messages()
{
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}


function has_flash_messages()
{
    return !empty($_SESSION['flash_messages']);
}


function sanitize($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}


function hash_password($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}


function verify_password($password, $hash)
{
    return password_verify($password, $hash);
}


function is_logged_in()
{
    return isset($_SESSION['user_id']);
}


function require_login()
{
    if (!is_logged_in()) {
        add_flash_message('Please log in to access this page.', 'info');
        redirect('/login.php');
    }
}


function get_user_id()
{
    return $_SESSION['user_id'] ?? null;
}


function get_current_date($format = 'Y-m-d')
{
    return date($format);
}


function format_date($date)
{
    $timestamp = strtotime((string)$date);
    return $timestamp !== false ? date('F j, Y', $timestamp) : '';
}

function format_time($time)
{
    $timestamp = strtotime($time);
    return date('g:i A', $timestamp);
}


function json_decode_safe($json)
{
    if (empty($json)) {
        return [];
    }

    $result = json_decode($json, true);
    return $result !== null ? $result : [];
}


function render($template, $variables = [])
{
    global $theme, $current_year;

    extract($variables);

    // Include the template
    include TEMPLATE_PATH . "/$template";
}
