<?php
require_once("auth.php");
require_once("config.php");
require_login();
session_start();
$pdo = get_pdo();

$review_id = isset($_GET['rid']) ? (int)$_GET['rid'] : 0;
$game_id = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;
$user_id = $_SESSION['user_id'];
$is_admin = is_admin();

try {
    if($is_admin){
        $stmt = $pdo->prepare("
            CALL AdminDeleteReview(?,?)
        ");
        $stmt->execute([$review_id, $game_id]);
    }else {
        $stmt = $pdo->prepare("
            CALL DeleteReview(?,?,?)
        ");
        $stmt->execute([$user_id, $review_id, $game_id]);
    }

    set_flash('flash_success', 'Review deleted successfully.');
} catch (PDOException $e) {
    set_flash('flash_error', 'Could not delete your review: ' . $e->getMessage());
}

header('Location: game.php?id=' . $game_id);
exit();

?>