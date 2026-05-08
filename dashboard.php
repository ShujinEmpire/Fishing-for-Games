<?php
// ============================================================
//  Fishing for Games — Dashboard
//  dashboard.php
// ============================================================

/*
TODO: fix the date at the JS on the bottom from appearing a day off due to timezone issues. 
It should show the correct release date regardless of the user's timezone.
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


$flash_success = get_flash('flash_success');
$flash_error   = get_flash('flash_error');
 
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
    <?php if($is_admin): ?>
        <small>Admin Dashboard</small>
    <?php endif; ?>
    </div>

    <div class="nav-section">Navigate</div>
    <a href="dashboard.php" class="nav-link active">The Dock</a>
    <a href="profile/profile.php" class="nav-link">Profile</a>
    <?php if($is_admin): ?>
        <div class="nav-section">Admin</div>
        <a href="create_game.php" class="nav-link">Create Game</a>
    <?php endif; ?> 

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
          <input id="game-search" type="text" placeholder="Search the waters…" autocomplete="off">
        </div>
      </div>
    </div>

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

      <!-- Total Catches -->
      <div class="stat-card">
        <div class="stat-label">Total Catches</div>
        <div id="stat-catches" class="stat-value"><?= stat_value($total_catches) ?></div>
        <div id="stat-catches-note" class="stat-note">
          <?= $total_catches !== null ? '' : 'No data yet' ?>
        </div>
      </div>

      <!-- Games in Pond -->
      <div class="stat-card">
        <div class="stat-label">Games in Pond</div>
        <div id="stat-games" class="stat-value"><?= stat_value($games_in_pond) ?></div>
        <div id="stat-games-note" class="stat-note">
          <?= $games_in_pond !== null ? '' : 'No data yet' ?>
        </div>
      </div>

    </div>

    <!-- ── Recent Catches Table ── -->
    <div class="section-title">Games <span id="result-count"></span></div>
    <div class="catch-table">
      <table>
        <thead>
          <tr>
            <th>Game</th>
            <th>Platforms</th>
            <th>Genre</th>
            <th>Avg Rating</th>
            <th>Reviews</th>
            <th>Released</th>
          </tr>
        </thead>
        <tbody id="game-results">
          <?php if (!empty($recent_catches)): ?>
            <?php foreach ($recent_catches as $catch): ?>
              <tr>
                <td>
                  <span class="game-title"><?= htmlspecialchars($catch['GName']) ?></span>
                </td>
                <td>
                  <span class="platform-tag"><?= htmlspecialchars($catch['Platforms']) ?></span>
                </td>
                <td>
                  <span class="avatar"><?= strtoupper(substr($catch['Cover_Image'], 0, 2)) ?></span>
                  <?= htmlspecialchars($catch['Cover_Image']) ?>
                </td>
                <td>
                  <span class="lure <?= lure_class((float)$catch['Rating']) ?>">
                    <?= number_format((float)$catch['Rating'], 1) ?>
                  </span>
                </td>
                <td class="date-col">
                  <?= date('M j', strtotime($catch['GReleaseDate'])) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6">
                <div class="empty-state">No catches yet — the waters are quiet.</div>
              </td>
            </tr>
          <?php endif; ?>
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

        if (!tbody) {
          console.error('Missing <tbody id="game-results"> in dashboard.php');
          return;
        }

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
        if (countEl) countEl.textContent = '(' + games.length + ')';

        // No results
        if (games.length === 0) {
          tbody.innerHTML = '<tr><td colspan="6"><div class="empty-state">No catches found — try a different search.</div></td></tr>';
          return;
        }

        // Build table rows
        let html = '';
        games.forEach(function(g) {
          const score = parseFloat(g.Rating) || 0;
          let scoreClass = 'lure-bad';
          if (score >= 4.3) scoreClass = 'lure-great';
          else if (score >= 3.5) scoreClass = 'lure-good';
          else if (score >= 2.5) scoreClass = 'lure-mid';

          const reviews = parseInt(g.ReviewCount) || 0;
          const released = new Date(g.GReleaseDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
          //fix the date appearing a day off due to timezone issues by using the 
          // original date string and removing the time component

          const gameId = g.GID || 0;

          html += '<tr onclick="window.location=\'game.php?id=' + gameId + '\'" style="cursor:pointer;">';
          html += '  <td><span class="game-title">' + escapeHtml(g.GName || '') + '</span></td>';
          html += '  <td><span class="platform-tag">' + escapeHtml(g.Platforms || '') + '</span></td>';
          html += '  <td style="color:var(--muted);font-size:.82rem;">' + escapeHtml(g.Genre || '') + '</td>';
          html += '  <td><span class="lure ' + scoreClass + '">' + score.toFixed(1) + '</span></td>';
          html += '  <td style="color:var(--muted);font-size:.82rem;">' + reviews + '</td>';
          html += '  <td class="date-col">' + released + '</td>';
          html += '</tr>';
        });

        tbody.innerHTML = html;
      })
      .catch(function(err) {
        const tbody = document.getElementById("game-results");
        if (tbody) {
          tbody.innerHTML = '<tr><td colspan="6"><div class="empty-state" style="color:#f87171;">Fetch error: ' + escapeHtml(String(err)) + '</div></td></tr>';
        }
        console.error(err);
      });
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
      var div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

    // Connect the search input to the async search and load all games on page load
    document.addEventListener("DOMContentLoaded", function() {
      const searchInput = document.getElementById("game-search");

      if (searchInput) {
        searchInput.addEventListener("input", function() {
          searchGames(this.value);
        });
      }

      fetchGames("");
    });
  </script>

</body>
</html>
