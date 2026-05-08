<?php

require_once ('auth.php');
require_once ('config.php');
require_login();
is_admin();

$pdo = get_pdo();


$game_id = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;

if ($game_id <= 0) {
    set_flash('flash_error', 'Invalid game ID.');
    header('Location: dashboard.php');
    exit();
}

try{
    $stmt = $pdo->prepare("CALL DeleteGame(?)");
    $stmt->execute([$game_id]);
    set_flash('flash_success', 'Game deleted successfully.');
} catch (PDOException $e) {
    set_flash('flash_error', 'Could not delete the game: ' . $e->getMessage());
}

header('Location: dashboard.php');
exit();

?>