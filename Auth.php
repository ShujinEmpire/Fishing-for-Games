<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool
{
    return isset($_SESSION['UID']); //A check to make enure if the user is logged in on certain parts of the website 
                                    // Only logged in users can make a review. Also redirect them if they are not logged in.
}

function require_login(string $redirect = 'login.php'): void // This is the main check to redirect users to login if they want to leave reviews or comment
{
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please log in to access that page.';
        header('Location: ' . $redirect);
        exit();
    }
}

function redirect_if_logged_in(string $target = 'dashboard.php'): void // A check to make sure loggin in users dont see what are meant for guests, aswell as not let them go to the login menu 
                                                                        // if they are already logged in
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
