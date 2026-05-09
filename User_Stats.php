<?php
// ============================================================
//  Fishing for Games — User Stats
//  User_Stats.php
// ============================================================

require_once("auth.php");
require_once("config.php");

session_start();
$pdo = get_pdo();

// ── Auth & User ──────────────────────────────────────────────
$is_logged_in  = is_logged_in();
$is_admin      = is_admin();
$user_name     = $is_logged_in ? htmlspecialchars($_SESSION['username'] ?? 'User') : null;
$user_email    = $is_logged_in ? htmlspecialchars($_SESSION['email'] ?? '') : null;
$user_initials = $is_logged_in ? strtoupper(substr($user_name, 0, 2)) : null;

$flash_success = get_flash('flash_success');
$flash_error   = get_flash('flash_error');

// ── Grab user review counts from the view ─────────────────────
$user_stats = [];

try {
    $stmt = $pdo->query("
        SELECT * FROM UserReviewCount
    ");

    $user_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $flash_error = "Could not load user stats: " . $e->getMessage();
}

$total_users = count($user_stats);

$total_reviews = 0;
foreach ($user_stats as $user) {
    $total_reviews += (int)$user['TotalReviews'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fishing for Games — User Stats</title>
  <link href="dashboard.css" rel="stylesheet">
</head>
<body>

  <!-- ── Sidebar ── -->
  <nav id="sidebar">

    <div class="logo">
      Fishing for Games
      <?php if ($is_admin): ?>
        <small>Admin Dashboard</small>
      <?php endif; ?>
    </div>

    <div class="nav-section">Navigate</div>
    <a href="dashboard.php" class="nav-link">The Dock</a>
    <a href="profile/profile.php" class="nav-link">Profile</a>

    <?php if ($is_admin): ?>
      <div class="nav-section">Admin</div>
      <a href="create_game.php" class="nav-link">Create Game</a>
      <a href="User_Stats.php" class="nav-link active">User Stats</a>
    <?php endif; ?>

    <!-- Sidebar footer -->
    <div class="sidebar-footer">
      <?php if ($is_logged_in): ?>
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
        <a href="logout.php" class="btn-signin">Sign In</a>
        <div class="not-logged-in">Not logged in</div>
      <?php endif; ?>
    </div>

  </nav>

  <!-- ── Main Content ── -->
  <main id="main">

    <!-- Topbar -->
    <div class="topbar">
      <div class="topbar-title">
        <?php if ($is_logged_in): ?>
          <small>Welcome Admin <?= $user_name ?></small>
        <?php endif; ?>
        User Stats
      </div>

      <div style="display:flex;align-items:center;gap:.8rem;">
        <div class="search-wrap">
          <input id="user-search" type="text" placeholder="Search users..." autocomplete="off">
        </div>
      </div>
    </div>

    <!-- Flash Messages -->
    <div class="topbar-flash">
      <?php if ($flash_success): ?>
        <div class="flash flash-success"><?= htmlspecialchars($flash_success) ?></div>
      <?php endif; ?>

      <?php if ($flash_error): ?>
        <div class="flash flash-error"><?= htmlspecialchars($flash_error) ?></div>
      <?php endif; ?>
    </div>

    <!-- ── Stat Cards ── -->
    <div class="stats">

      <div class="stat-card">
        <div class="stat-label">Total Users</div>
        <div class="stat-value"><?= number_format($total_users) ?></div>
        <div class="stat-note">
          <?= $total_users === 1 ? '1 user found' : $total_users . ' users found' ?>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-label">Total Reviews</div>
        <div class="stat-value"><?= number_format($total_reviews) ?></div>
        <div class="stat-note">
          <?= $total_reviews === 1 ? '1 review total' : $total_reviews . ' reviews total' ?>
        </div>
      </div>

    </div>

    <!-- ── User Stats Table ── -->
    <div class="section-title">
      Users <span id="result-count">(<?= $total_users ?>)</span>
    </div>

    <div class="catch-table">
      <table>
        <thead>
          <tr>
            <th>UID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Total Reviews</th>
          </tr>
        </thead>

        <tbody id="user-results">
          <?php if (!empty($user_stats)): ?>
            <?php foreach ($user_stats as $user): ?>
              <tr>
                <td style="color:var(--muted);font-size:.82rem;">
                  <?= htmlspecialchars($user['UID']) ?>
                </td>

                <td>
                  <span class="game-title">
                    <?= htmlspecialchars($user['FName']) ?>
                  </span>
                </td>

                <td style="color:var(--text);font-size:.85rem;">
                  <?= htmlspecialchars($user['LName']) ?>
                </td>

                <td>
                  <span class="lure <?= (int)$user['TotalReviews'] > 0 ? 'lure-great' : 'lure-bad' ?>">
                    <?= number_format((int)$user['TotalReviews']) ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4">
                <div class="empty-state">No user stats found.</div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>

  <script>
    // ── Client-side search for the user stats table ───────────
    document.addEventListener("DOMContentLoaded", function() {
      const searchInput = document.getElementById("user-search");
      const rows = document.querySelectorAll("#user-results tr");
      const countEl = document.getElementById("result-count");

      if (!searchInput) return;

      searchInput.addEventListener("input", function() {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;

        rows.forEach(function(row) {
          const text = row.textContent.toLowerCase();

          if (text.includes(query)) {
            row.style.display = "";
            visibleCount++;
          } else {
            row.style.display = "none";
          }
        });

        if (countEl) {
          countEl.textContent = "(" + visibleCount + ")";
        }
      });
    });
  </script>

</body>
</html>



