<?php
// ============================================================
//  Fishing for Games — Submit Review
//  submit_review.php
// ============================================================

require_once("auth.php");
require_once("config.php");

// Must be logged in
require_login();

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit();
}

$pdo = get_pdo();

// ── Gather & validate input ──────────────────────────────────
$game_id = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;
$rating  = isset($_POST['rating'])  ? (int)$_POST['rating']  : 0;
$comment = trim($_POST['comment'] ?? '');
$user_id = $_SESSION['user_id'];

// Validate game_id
if ($game_id <= 0) {
    set_flash('flash_error', 'Invalid game.');
    header('Location: dashboard.php');
    exit();
}

// Validate rating (1–5 stars)
if ($rating < 1 || $rating > 5) {
    set_flash('flash_error', 'Please select a star rating between 1 and 5.');
    header('Location: game.php?id=' . $game_id);
    exit();
}

// Comment is optional but if provided, cap length
if (strlen($comment) > 5000) {
    $comment = substr($comment, 0, 5000);
}

// ── Check the game exists ────────────────────────────────────
try {
    $stmt = $pdo->prepare("SELECT GID FROM Game WHERE GID = ?");
    $stmt->execute([$game_id]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$game) {
        set_flash('flash_error', 'Game not found.');
        header('Location: dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    set_flash('flash_error', 'Something went wrong. Please try again.');
    header('Location: game.php?id=' . $game_id);
    exit();
}

// ── Optional: prevent duplicate reviews ──────────────────────
try {
    $stmt = $pdo->prepare("SELECT RID FROM Review WHERE UID = ? AND GID = ?");
    $stmt->execute([$user_id, $game_id]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($review) {
        // User already reviewed — update instead of insert
        $stmt = $pdo->prepare("
            UPDATE Review
            SET Rating = ?, RText = ?, Created_at = NOW()
            WHERE UID = ? AND GID = ? AND RID = ?
        ");
        $stmt->execute([$rating, $comment, $user_id, $game_id, $review['RID']]);

        set_flash('flash_success', 'Your review has been updated!');
        header('Location: game.php?id=' . $game_id);
        exit();
    }
} catch (PDOException $e) {
    // Table might not have ReviewID or unique constraint — continue to insert
}

// ── Insert the review ────────────────────────────────────────
try {
    $stmt = $pdo->prepare("
        CALL MakeReview(?,?,?,?)
    ");
    $stmt->execute([$user_id, $game_id, $comment, $rating]);
    set_flash('flash_success', 'Review posted — nice catch!');
} catch (PDOException $e) {
    set_flash('flash_error', 'Could not save your review: ' . $e->getMessage());
}

header('Location: game.php?id=' . $game_id);
exit();
