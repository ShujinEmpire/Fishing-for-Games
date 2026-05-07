<?php
// ============================================================
//  Fishing for Games — Game View
//  game.php
// ============================================================

/* 
TODO: add delete to admin actions, and add check in game_delete.php to prevent 
non-admins from deleting. aswell as for reviews.
*/

require_once("auth.php");
require_once("config.php");
session_start();
$pdo = get_pdo();

// ── Auth & User ──────────────────────────────────────────────
$is_logged_in  = is_logged_in();
$is_admin      = is_admin();
$user_name     = $is_logged_in ? htmlspecialchars($_SESSION['username'] ?? 'User') : null;
$user_email    = $is_logged_in ? htmlspecialchars($_SESSION['email']    ?? '')     : null;
$user_initials = $is_logged_in ? strtoupper(substr($user_name, 0, 2))            : null;
$user_id       = $_SESSION['user_id'] ?? null;

// ── Get Game ID ──────────────────────────────────────────────
$game_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ── Flash messages ───────────────────────────────────────────
$flash_success = get_flash('flash_success');
$flash_error   = get_flash('flash_error');

// ── Fetch game from DB ───────────────────────────────────────
$game = null;
$reviews = [];

try {
    $stmt = $pdo->prepare("SELECT * FROM Game WHERE GID = ?");
    $stmt->execute([$game_id]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table/column may not exist yet
}

// ── Fetch reviews for this game ──────────────────────────────
if ($game) {
    try {
        $stmt = $pdo->prepare("
            SELECT r.*, u.FName, u.LName
            FROM Review r
            JOIN User u ON r.UID = u.UID
            WHERE r.GID = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$game_id]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Reviews table may not exist yet
        set_flash('flash_error', 'Could not fetch reviews: ' . $e->getMessage());
    }
}


// ── Check if current user already reviewed ───────────────────
$user_review = null;
if ($is_logged_in && $game) {
    foreach ($reviews as $r) {
        if (($r['UID'] ?? null) == $user_id) {
            $user_review = $r;
            break;
        }
    }
}

// ── Helpers ──────────────────────────────────────────────────
function lure_class_game(float $score): string {
    if ($score >= 4.3 && $score <= 5.0) return 'lure-great';
    if ($score >= 3.5 && $score < 4.3) return 'lure-good';
    if ($score >= 2.5 && $score < 3.5) return 'lure-mid';
    return 'lure-bad';
}

function lure_label(float $score): string {
    if ($score >= 4.3 && $score <= 5.0) return 'Legendary Catch';
    if ($score >= 3.5 && $score < 4.3) return 'Great Catch';
    if ($score >= 2.5 && $score < 3.5) return 'Decent Bite';
    return 'Throw It Back';
}

// Compute average score from reviews (1–5 scale)
$avg_score = 0;
$review_count = count($reviews);
if ($review_count > 0) {
    $total = 0;
    foreach ($reviews as $r) {
        $total += (float)($r['Rating'] ?? $r['Rating'] ?? 0);
    }
    $avg_score = $total / $review_count;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $game ? htmlspecialchars($game['GName'] ?? $game['GName'] ?? 'Game') : 'Game Not Found' ?> — Fishing for Games</title>
  <link href="dashboard.css" rel="stylesheet">
  <link href="game-review.css" rel="stylesheet">
</head>
<body>

  <!-- ── Sidebar ── -->
  <nav id="sidebar">

    <div class="logo">
      Fishing for Games
      <small>Admin Dashboard</small>
    </div>

    <div class="nav-section">Navigate</div>
    <a href="dashboard.php" class="nav-link">The Dock</a>

    <!-- Sidebar footer -->
    <div class="sidebar-footer">
      <?php if (is_logged_in()): ?>
        <div class="admin-user">
          <div class="avatar"><?= $user_initials ?></div>
          <div>
            <div class="admin-name"><?= $user_name ?></div>
            <div class="admin-email"><?= $user_email ?></div>
          </div>
          <div class="teal-dot" style="margin-left:auto;"></div>
        </div>
        <div style="margin-top:.6rem;">
          <a href="logout.php" class="btn-signin" style="display:block;text-align:center;color:var(--muted);text-decoration:none;font-size:.8rem;padding:.45rem;border:1px solid var(--border);">Sign Out</a>
        </div>
      <?php else: ?>
        <a href="login.php" class="btn-signin">Sign In</a>
        <div class="not-logged-in">Not logged in</div>
      <?php endif; ?>
    </div>

  </nav>

  <!-- ── Main Content ── -->
  <main id="main">

    <!-- Back link -->
    <a href="dashboard.php" class="back-link">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
      Back to The Dock
    </a>

    <!-- Flash messages -->
    <?php if ($flash_success): ?>
      <div class="flash flash-success"><?= htmlspecialchars($flash_success) ?></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
      <div class="flash flash-error"><?= htmlspecialchars($flash_error) ?></div>
    <?php endif; ?>

    <?php if ($game): ?>

      <?php
        $title       = htmlspecialchars($game['GName']       ?? $game['GName']       ?? 'Untitled');
        $platform    = htmlspecialchars($game['Platform']    ?? $game['platform']    ?? '');
        $genre       = htmlspecialchars($game['Genre']       ?? $game['genre']       ?? '');
        $developer   = htmlspecialchars($game['DSName']   ?? $game['DSName']   ?? '');
        $publisher   = htmlspecialchars($game['PName']   ?? $game['PName']   ?? '');
        $release     = $game['GReleaseDate'] ?? $game['GReleaseDate'] ?? null;
        $description = htmlspecialchars($game['Description'] ?? $game['Description'] ?? '');
        $cover_url   = $game['Cover_Image']    ?? $game['Cover_Image']    ?? null;
      ?>

      <!-- ── Game Hero Card ── -->
      <div class="game-hero">
        <div class="game-cover">
          <?php if ($cover_url): ?>
            <img src="<?= htmlspecialchars($cover_url) ?>" alt="<?= $title ?> cover">
          <?php else: ?>
            <div class="game-cover-placeholder">No Cover<br>Available</div>
          <?php endif; ?>
        </div>

        <div class="game-info">
          <h1 class="game-title-heading"><?= $title ?></h1>

          <div class="game-meta">
            <?php if ($platform): ?>
              <span class="meta-tag"><?= $platform ?></span>
            <?php endif; ?>
            <?php if ($genre): ?>
              <span class="meta-tag genre"><?= $genre ?></span>
            <?php endif; ?>
            <?php if ($release): ?>
              <span class="meta-tag genre"><?= date('M j, Y', strtotime($release)) ?></span>
            <?php endif; ?>
          </div>

          <?php if ($description): ?>
            <p class="game-description"><?= nl2br($description) ?></p>
          <?php endif; ?>

          <!-- Average lure score -->
          <?php if ($review_count > 0): ?>
            <div class="score-block">
              <div class="big-score <?= lure_class_game($avg_score) ?>"><?= number_format($avg_score, 1) ?></div>
              <div class="score-details">
                <div class="score-label <?= lure_class_game($avg_score) ?>"><?= lure_label($avg_score) ?></div>
                Based on <?= $review_count ?> review<?= $review_count !== 1 ? 's' : '' ?>
              </div>
              <?php if ($is_admin): ?>
                <div class="admin-actions">
                <a href="game_delete.php?id=<?= $game_id ?>" class="btn-delete" 
                 onclick="return confirm('Are you sure you want to delete this game?');">Delete</a>
                </div>
                <?php endif; ?>
            </div>
          <?php else: ?>
            <div class="score-block">
              <div class="score-details" style="color:var(--muted);">No reviews yet — be the first angler to score this catch.</div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ── Detail Cards ── -->
      <div class="detail-grid">
        <?php if ($developer): ?>
          <div class="detail-card">
            <div class="detail-label">Developer</div>
            <div class="detail-value"><?= $developer ?></div>
          </div>
        <?php endif; ?>
        <?php if ($publisher): ?>
          <div class="detail-card">
            <div class="detail-label">Publisher</div>
            <div class="detail-value"><?= $publisher ?></div>
          </div>
        <?php endif; ?>
        <?php if ($platform): ?>
          <div class="detail-card">
            <div class="detail-label">Platform</div>
            <div class="detail-value"><?= $platform ?></div>
          </div>
        <?php endif; ?>
        <?php if ($release): ?>
          <div class="detail-card">
            <div class="detail-label">Release Date</div>
            <div class="detail-value"><?= date('M j, Y', strtotime($release)) ?></div>
          </div>
        <?php endif; ?>
      </div>

      <!-- ── Write a Review (inline form) ── -->
      <?php if ($is_logged_in): ?>
        <div class="review-form-card" id="review-form">
          <div class="review-form-title">
            <?= $user_review ? 'Update Your Review' : 'Cast Your Review' ?>
          </div>
          <form action="submit_review.php" method="POST">
            <input type="hidden" name="game_id" value="<?= $game_id ?>">

            <!-- Star Rating -->
            <div class="star-rating">
              <?php for ($i = 5; $i >= 1; $i--): ?>
                <?php
                  $existing_score = $user_review ? (int)($user_review['Score'] ?? $user_review['score'] ?? 0) : 0;
                  $checked = ($existing_score === $i) ? 'checked' : '';
                ?>
                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" <?= $checked ?>>
                <label for="star<?= $i ?>">&#9733;</label>
              <?php endfor; ?>
            </div>
            <div class="star-hint">Click to rate (1–5 stars)</div>

            <!-- Comment -->
            <textarea
              class="review-textarea"
              name="comment"
              rows="3"
              placeholder="Share your thoughts on this catch…"
            ><?= $user_review ? htmlspecialchars($user_review['RText'] ?? $user_review['RText'] ?? '') : '' ?></textarea>

            <div class="review-form-actions">
              <span class="note">
                <?php if ($user_review): ?>
                  You've already reviewed this game — submitting will update your review.
                <?php else: ?>
                  Comment is optional.
                <?php endif; ?>
              </span>
              <button type="submit" class="btn-cast">
                <?= $user_review ? 'Update Review' : 'Post Review' ?>
              </button>
            </div>
          </form>
        </div>
      <?php else: ?>
        <div class="login-prompt">
          <a href="login.php">Sign in</a> to cast your review on this game.
        </div>
      <?php endif; ?>

      <!-- ── Reviews Section ── -->
      <div class="reviews-header">
        <div class="section-title" style="margin-bottom:0;">
          Angler Reviews
          <?php if ($review_count > 0): ?>
            <span style="font-weight:normal;font-size:.75rem;color:var(--muted);margin-left:.4rem;">(<?= $review_count ?>)</span>
          <?php endif; ?>
        </div>
            <!-- delete review if user -->
      </div>

      <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $review): ?>
          <?php
            $r_name     = htmlspecialchars(($review['FName'] ?? '') . ' ' . ($review['LName'] ?? ''));
            $r_initials = strtoupper(substr($review['FName'] ?? '', 0, 1) . substr($review['LName'] ?? '', 0, 1));
            $r_score    = (int)($review['Rating'] ?? $review['Rating'] ?? 0);
            $r_body     = htmlspecialchars($review['RText'] ?? $review['RText'] ?? $review['RText'] ?? $review['RText'] ?? '');
            $r_date     = $review['Created_at'] ?? $review['Created_at'] ?? null;
            $is_own     = $is_logged_in && ($review['UID'] ?? null) == $user_id;
          ?>
          <div class="review-card <?= $is_own ? 'own-review' : '' ?>">
            <div class="review-top">
              <div class="review-author">
                <div class="avatar"><?= $r_initials ?></div>
                <div>
                  <div class="review-author-name">
                    <?= $r_name ?>
                    <?php if ($is_own): ?>
                      <span class="you-badge">You</span>
                    <?php endif; ?>
                  </div>
                  <?php if ($r_date): ?>
                    <div class="review-date"><?= date('M j, Y', strtotime($r_date)) ?></div>
                  <?php endif; ?>
                </div>
              </div>
              <div>
                <span class="review-stars">
                  <?php for ($s = 1; $s <= 5; $s++): ?>
                    <span class="<?= $s <= $r_score ? '' : 'empty' ?>">&#9733;</span>
                  <?php endfor; ?>
                </span>
              </div>
            </div>
            <?php if ($r_body): ?>
              <div class="review-body"><?= nl2br($r_body) ?></div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-reviews">
          No reviews yet — the waters are quiet.
        </div>
      <?php endif; ?>

    <?php else: ?>

      <!-- ── Game Not Found ── -->
      <div class="not-found">
        <h2>Game Not Found</h2>
        <p>This catch doesn't exist in our waters. Head back to the dock and try again.</p>
        <br>
        <a href="dashboard.php" class="btn-cast">Back to The Dock</a>
      </div>

    <?php endif; ?>

  </main>

  <script>
    // Small star animation on click
    document.querySelectorAll('.star-rating label').forEach(function(star) {
      star.addEventListener('click', function() {
        this.style.transform = 'scale(1.3)';
        setTimeout(function() { star.style.transform = 'scale(1)'; }, 200);
      });
    });
  </script>

</body>
</html>
