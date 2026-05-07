<?php
require_once("../auth.php");
require_once("../config.php");

require_login("../login.php");

$pdo = get_pdo();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = strtolower(trim($_POST['Email']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    $check = $pdo->prepare("
        SELECT UID FROM User WHERE Email = ? AND UID != ?
    ");
    $check->execute([$email, $_SESSION['user_id']]);

    if ($check->fetch()) {
        die("Email already in use.");
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

    $_SESSION['email'] = $email;

    header("Location: ../dashboard.php");
    exit();
}