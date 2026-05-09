<?php
require_once("../auth.php");
require_once("../config.php");

require_login("../login.php");
session_start();

$pdo = get_pdo();

$flash_success = get_flash('flash_success');
$flash_error = get_flash('flash_error');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = strtolower(trim($_POST['Email']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('flash_error', 'Invalid email format.');
        header("Location: profile.php");
        exit();
    }

    $check = $pdo->prepare("
        SELECT UID FROM User WHERE Email = ? AND UID != ?
    ");
    $check->execute([$email, $_SESSION['user_id']]);

    if ($check->fetch()) {
        set_flash('flash_error', 'Email already in use.');
        header("Location: profile.php");
        exit();
    }

    $stmt = $pdo->prepare("
        UPDATE User
        SET FName = ?, LName = ?, Email = ?
        WHERE UID = ?
    ");

    $stmt->execute([
        $_POST['FName'],
        $_POST['LName'],
        $email,
        $_SESSION['user_id']
    ]);

    $_SESSION['username'] = $_POST['FName'] . " " . $_POST['LName'];

    $_SESSION['email'] = $email;
    
    set_flash('flash_success', 'Profile updated successfully.');
    header("Location: ../dashboard.php");
    exit();
}