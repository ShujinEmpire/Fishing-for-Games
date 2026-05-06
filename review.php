<?php
// ============================================================
//  Fishing for Games — Review Page
//  review.php
// ============================================================

require_once("auth.php");
require_once("config.php");
session_start();

// ── Auth & User ──────────────────────────────────────────────
$is_logged_in  = isset($_SESSION['user_id']);
$user_name     = $is_logged_in ? htmlspecialchars($_SESSION['username'] ?? 'User') : null;
$user_email    = $is_logged_in ? htmlspecialchars($_SESSION['email']    ?? '')     : null;
$user_initials = $is_logged_in ? strtoupper(substr($user_name, 0, 2))            : null;

// ── Flash messages ───────────────────────────────────────────
$flash_success = get_flash('flash_success');
$flash_error   = get_flash('flash_error');

// ── Selected rating (for pre-filling stars) ──────────────────
$selectedRating = $selectedRating ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review — Fishing for Games</title>
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

    <!-- ── Star Rating ── -->
    <div class="review-form-card">
      <div class="review-form-title">Rate This Catch</div>

      <div class="star-rating">
        <input type="radio" id="star5" name="rating" value="5" <?= ($selectedRating == 5) ? 'checked' : '' ?>>
        <label for="star5">&#9733;</label>
        <input type="radio" id="star4" name="rating" value="4" <?= ($selectedRating == 4) ? 'checked' : '' ?>>
        <label for="star4">&#9733;</label>
        <input type="radio" id="star3" name="rating" value="3" <?= ($selectedRating == 3) ? 'checked' : '' ?>>
        <label for="star3">&#9733;</label>
        <input type="radio" id="star2" name="rating" value="2" <?= ($selectedRating == 2) ? 'checked' : '' ?>>
        <label for="star2">&#9733;</label>
        <input type="radio" id="star1" name="rating" value="1" <?= ($selectedRating == 1) ? 'checked' : '' ?>>
        <label for="star1">&#9733;</label>
      </div>
      <div class="star-hint">Click to rate</div>
    </div>

    <!-- ── Comment Form ── -->
    <?php if ($is_logged_in): ?>
      <div class="review-form-card">
        <div class="review-form-title">Write a Comment</div>
        <div style="display:flex;gap:.8rem;align-items:flex-start;">
          <div class="avatar" style="width:36px;height:36px;font-size:.7rem;flex-shrink:0;"><?= $user_initials ?></div>
          <div style="flex:1;">
            <textarea class="review-textarea" rows="3" placeholder="Share your thoughts…"></textarea>
            <div class="review-form-actions">
              <span class="note"></span>
              <button class="btn-cast">Post Comment</button>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="login-prompt">
        <a href="login.php">Sign in</a> to leave a comment.
      </div>
    <?php endif; ?>

    <!-- ── Comments List ── -->
    <div class="section-title">Comments</div>

    <!-- Comment 1 with reply -->
    <div class="review-card">
      <div class="review-top">
        <div class="review-author">
          <div class="avatar">JD</div>
          <div>
            <div class="review-author-name">John Doe</div>
            <div class="review-date">2 hours ago</div>
          </div>
        </div>
      </div>
      <div class="review-body">
        This is an amazing post! Thanks for sharing your insights with us. I've
        learned a lot from this.
      </div>
      <div class="comment-actions">
        <a href="#">Like</a>
        <a href="#">Reply</a>
        <a href="#">Share</a>
      </div>

      <!-- Reply -->
      <div class="reply-section">
        <div class="review-card" style="margin-top:.75rem;">
          <div class="review-top">
            <div class="review-author">
              <div class="avatar">JS</div>
              <div>
                <div class="review-author-name">Jane Smith</div>
                <div class="review-date">1 hour ago</div>
              </div>
            </div>
          </div>
          <div class="review-body">
            Totally agree with you! The points mentioned are very insightful.
          </div>
          <div class="comment-actions">
            <a href="#">Like</a>
            <a href="#">Reply</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Comment 2 -->
    <div class="review-card">
      <div class="review-top">
        <div class="review-author">
          <div class="avatar">MJ</div>
          <div>
            <div class="review-author-name">Mike Johnson</div>
            <div class="review-date">3 hours ago</div>
          </div>
        </div>
      </div>
      <div class="review-body">
        Great discussion everyone! I'd like to add that this topic has many
        interesting aspects we could explore further.
      </div>
      <div class="comment-actions">
        <a href="#">Like</a>
        <a href="#">Reply</a>
        <a href="#">Share</a>
      </div>
    </div>

  </main>

  <script>
    document.querySelectorAll('.star-rating label').forEach(function(star) {
      star.addEventListener('click', function() {
        this.style.transform = 'scale(1.3)';
        setTimeout(function() { star.style.transform = 'scale(1)'; }, 200);
      });
    });
  </script>

</body>
</html>
