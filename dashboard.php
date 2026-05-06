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
 
// ── Helper: lure score CSS class ─────────────────────────────
function lure_class(float $score): string {
    if ($score >= 4.3) return 'lure-great';
    if ($score >= 3.5) return 'lure-good';
    if ($score >= 2.5) return 'lure-mid';
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
        <a href="login.php" class="btn-signin">Sign In</a>
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
          <input
            type="text"
            id="live-search"
            placeholder="Search the waters…"
            autocomplete="off"
            onkeyup="searchGames(this.value)"
          >
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
        <div class="stat-value" id="stat-catches"><?= stat_value($total_catches) ?></div>
        <div class="stat-note" id="stat-catches-note">
          <?= $total_catches !== null ? '' : 'No data yet' ?>
        </div>
      </div>

      <!-- Games in Pond -->
      <div class="stat-card">
        <div class="stat-label">Games in Pond</div>
        <div class="stat-value" id="stat-games"><?= stat_value($games_in_pond) ?></div>
        <div class="stat-note" id="stat-games-note">
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
    <div class="section-title">
      Recent Catches
      <span id="result-count" style="font-weight:normal;font-size:.75rem;color:var(--muted);margin-left:.4rem;"></span>
    </div>
    <div class="catch-table">
      <table>
        <thead>
          <tr>
            <th>Game</th>
            <th>Platform</th>
            <th>Genre</th>
            <th>Lure Score</th>
            <th>Reviews</th>
            <th>Released</th>
          </tr>
        </thead>
        <tbody id="game-results">
          <tr>
            <td colspan="6">
              <div class="empty-state">Loading…</div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </main>

  <script>
    // ── Live Search ──────────────────────────────────────────
    let searchTimeout = null;

    function searchGames(query) {
      // Debounce: wait 200ms after the user stops typing
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
        fetchGames(query);
      }, 200);
    }

    function fetchGames(query) {
      const data = new FormData();
      data.append("search", query);

      fetch("fetch_games.php", {
        method: "POST",
        body: data
      })
      .then(function(response) { return response.json(); })
      .then(function(games) {
        const tbody = document.getElementById("game-results");
        const countEl = document.getElementById("result-count");

        // Handle errors
        if (games.error) {
          tbody.innerHTML = '<tr><td colspan="6"><div class="empty-state" style="color:#f87171;">Error: ' + games.error + '</div></td></tr>';
          return;
        }

        // Update stat cards with live data
        const statGames = document.getElementById("stat-games");
        const statGamesNote = document.getElementById("stat-games-note");
        if (statGames) {
          statGames.textContent = games.length;
          statGamesNote.textContent = games.length === 1 ? '1 game found' : games.length + ' games found';
        }

        // Update total reviews across all results
        let totalReviews = 0;
        games.forEach(function(g) { totalReviews += parseInt(g.ReviewCount) || 0; });
        const statCatches = document.getElementById("stat-catches");
        const statCatchesNote = document.getElementById("stat-catches-note");
        if (statCatches) {
          statCatches.textContent = totalReviews;
          statCatchesNote.textContent = totalReviews === 1 ? '1 review total' : totalReviews + ' reviews total';
        }

        // Update result count
        countEl.textContent = '(' + games.length + ')';

        // No results
        if (games.length === 0) {
          tbody.innerHTML = '<tr><td colspan="6"><div class="empty-state">No catches found — try a different search.</div></td></tr>';
          return;
        }

        // Build table rows
        let html = '';
        games.forEach(function(g) {
          const score = parseFloat(g.AvgScore) || 0;
          let scoreClass = 'lure-bad';
          if (score >= 4.3) scoreClass = 'lure-great';
          else if (score >= 3.5) scoreClass = 'lure-good';
          else if (score >= 2.5) scoreClass = 'lure-mid';

          const reviews = parseInt(g.ReviewCount) || 0;
          const released = g.ReleaseDate ? new Date(g.ReleaseDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
          const gameId = g.GameID || 0;

          html += '<tr onclick="window.location=\'game.php?id=' + gameId + '\'" style="cursor:pointer;">';
          html += '  <td><span class="game-title">' + escapeHtml(g.Title || '') + '</span></td>';
          html += '  <td><span class="platform-tag">' + escapeHtml(g.Platform || '') + '</span></td>';
          html += '  <td style="color:var(--muted);font-size:.82rem;">' + escapeHtml(g.Genre || '') + '</td>';
          html += '  <td><span class="lure ' + scoreClass + '">' + score.toFixed(1) + '</span></td>';
          html += '  <td style="color:var(--muted);font-size:.82rem;">' + reviews + '</td>';
          html += '  <td class="date-col">' + released + '</td>';
          html += '</tr>';
        });

        tbody.innerHTML = html;
      })
      .catch(function(err) {
        document.getElementById("game-results").innerHTML =
          '<tr><td colspan="6"><div class="empty-state" style="color:#f87171;">Fetch error: ' + err + '</div></td></tr>';
        console.error(err);
      });
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
      var div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

    // Load all games on page load
    window.onload = function() {
      fetchGames("");
    };
  </script>

</body>
</html>
