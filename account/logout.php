<?php
/**
 * Fabloom – Logout
 * Destroys session and redirects to homepage with flash message.
 */
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

// Capture user name before destroying session
$name = '';
if (is_logged_in()) {
    $u = current_user();
    $name = $u['name'] ?? '';
}

// Clear all session data
$_SESSION = [];

// Destroy the session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

// Restart session so flash works after redirect
session_start();

$msg = $name
    ? 'You have been logged out. See you again soon, ' . $name . '!'
    : 'You have been logged out successfully.';

flash('success', $msg);

header('Location: ' . SITE_URL . '/');
exit;
