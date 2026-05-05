<?php
// ============================================================
//  Fishing for Games — Dashboard
//  dashboard.php
// ============================================================

require_once("auth.php");
require_once("config.php");
session_start();
$pdo = get_pdo();

// ── Auth & User ──────────────────────────────────────────────
$is_logged_in  = isset($_SESSION['user_id']);
$is_admin      = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$user_name     = $is_logged_in ? htmlspecialchars($_SESSION['username'] ?? 'User') : null;
$user_email    = $is_logged_in ? htmlspecialchars($_SESSION['email']    ?? '')     : null;
$user_initials = $is_logged_in ? strtoupper(substr($user_name, 0, 2))            : null;

// ── Placeholder values (remove once DB is connected) ─────────
$total_catches  = null;
$games_in_pond  = null;
$on_the_hook    = null;
$recent_catches = [];
 
// ── Helper: lure score CSS class ─────────────────────────────
function lure_class(float $score): string {
    if ($score >= 8.5) return 'lure-great';
    if ($score >= 7.0) return 'lure-good';
    if ($score >= 5.0) return 'lure-mid';
    return 'lure-bad';
}

// ── Helper: format stat value ────────────────────────────────
function stat_value($val): string {
    return $val !== null ? number_format((int)$val) : '—';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fishing for Games — Dashboard</title>
  <link href="dashboard.css" rel="stylesheet">
</head>
<body>

  <!-- ── Sidebar ── -->
  <nav id="sidebar">

    <div class="logo">
      Fishing for Games
      <small>Admin Dashboard</small>
    </div>

    <div class="nav-section">Navigate</div>
    <a href="dashboard.php" class="nav-link active">The Dock</a>

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
        <?php if (is_logged_in()): ?>
          <small>Welcome back, <?= $user_name ?></small>
        <?php endif; ?>
        The Dock
      </div>
      <div style="display:flex;align-items:center;gap:.8rem;">
        <div class="search-wrap">
          <input type="text" placeholder="Search the waters…">
        </div>
        <?php if (is_logged_in()): ?>
          <a href="review_new.php" class="btn-cast">+ Cast a Review</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- ── Stat Cards ── -->
    <div class="stats">

      <!-- Total Catches -->
      <div class="stat-card">
        <div class="stat-label">Total Catches</div>
        <div class="stat-value"><?= stat_value($total_catches) ?></div>
        <div class="stat-note">
          <?= $total_catches !== null ? '' : 'No data yet' ?>
        </div>
      </div>

      <!-- Games in Pond -->
      <div class="stat-card">
        <div class="stat-label">Games in Pond</div>
        <div class="stat-value"><?= stat_value($games_in_pond) ?></div>
        <div class="stat-note">
          <?= $games_in_pond !== null ? '' : 'No data yet' ?>
        </div>
      </div>

      <!-- On the Hook (admin only) -->
      <?php if ($is_admin): ?>
        <div class="stat-card admin-card">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.3rem;">
            <div class="stat-label" style="margin:0;">On the Hook</div>
            <span class="admin-badge">Admin</span>
          </div>
          <div class="stat-value"><?= stat_value($on_the_hook) ?></div>
          <div class="stat-note">
            <?= $on_the_hook !== null ? '' : 'No data yet' ?>
          </div>
        </div>
      <?php endif; ?>

    </div>

    <!-- ── Recent Catches Table ── -->
    <div class="section-title">Recent Catches</div>
    <div class="catch-table">
      <table>
        <thead>
          <tr>
            <th>Game</th>
            <th>Platform</th>
            <th>Angler</th>
            <th>Lure Score</th>
            <th>Reeled In</th>
            <?php if ($is_admin): ?><th></th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($recent_catches)): ?>
            <?php foreach ($recent_catches as $catch): ?>
              <tr>
                <td>
                  <span class="game-title"><?= htmlspecialchars($catch['game']) ?></span>
                </td>
                <td>
                  <span class="platform-tag"><?= htmlspecialchars($catch['platform']) ?></span>
                </td>
                <td>
                  <span class="avatar"><?= strtoupper(substr($catch['angler'], 0, 2)) ?></span>
                  <?= htmlspecialchars($catch['angler']) ?>
                </td>
                <td>
                  <span class="lure <?= lure_class((float)$catch['score']) ?>">
                    <?= number_format((float)$catch['score'], 1) ?>
                  </span>
                </td>
                <td class="date-col">
                  <?= date('M j', strtotime($catch['created_at'])) ?>
                </td>
                <?php if ($is_admin): ?>
                  <td>
                    <a href="review_edit.php?id=<?= (int)$catch['id'] ?>" class="edit-link">edit</a>
                  </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="<?= $is_admin ? 6 : 5 ?>">
                <div class="empty-state">No catches yet — the waters are quiet.</div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>

</body>
</html>
