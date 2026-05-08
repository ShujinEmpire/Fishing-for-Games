<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("../auth.php");
require_once("../config.php");
session_start();

require_login("../login.php");

$pdo = get_pdo();

if (!isset($_FILES["profile_image"]) || $_FILES["profile_image"]["error"] !== UPLOAD_ERR_OK) {
    die("No image uploaded.");
}

$uploadDir = __DIR__ . "/uploads/";

if (!is_dir($uploadDir)) {
   mkdir($uploadDir, 0755, true);
}

$extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
$fileName = "user_" . $_SESSION["user_id"] . "_" . time() . "." . $extension;
$targetPath = $uploadDir . $fileName;

if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetPath)) {
    die("Could not save image.");
}

$imagePath = "profile/uploads/" . $fileName;

$stmt = $pdo->prepare("
    UPDATE User
    SET Profile_Image = ?
    WHERE UID = ?
");

$stmt->execute([
    $imagePath,
    $_SESSION["user_id"]
]);

header("Location: profile.php");
exit();