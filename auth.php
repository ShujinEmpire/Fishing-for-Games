<<<<<<< HEAD
=======
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function is_admin(): bool
{
    return is_logged_in() && isset($_SESSION['Type']) && $_SESSION['Type'] === 1;
}

function require_login(string $redirect = 'login.php'): void
{
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please log in to access that page.';
        header('Location: ' . $redirect);
        exit();
    }
}

function redirect_if_logged_in(string $target = 'dashboard.php'): void
{
    if (is_logged_in()) {
        header('Location: ' . $target);
        exit();
    }
}

function set_flash(string $key, string $message): void
{
    $_SESSION[$key] = $message;
}

function get_flash(string $key): ?string
{
    if (!isset($_SESSION[$key])) {
        return null;
    }

    $value = $_SESSION[$key];
    unset($_SESSION[$key]);

    return $value;
}
>>>>>>> origin/Alexis

