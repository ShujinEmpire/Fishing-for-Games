<?php
// ============================================================
//  Fishing for Games — Create Game
//  create_game.php
// ============================================================

require_once("auth.php");
require_once("config.php");
session_start();
$pdo = get_pdo();

$user_name = $is_logged_in ? htmlspecialchars($_SESSION['username'] ?? 'User') : null;
$user_email = $is_logged_in ? htmlspecialchars($_SESSION['email']    ?? '') : null;
$user_initials = $is_logged_in ? strtoupper(substr($user_name, 0, 2)) : null;

// ── variables for game ──────────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $gname = trim($_POST['gname'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $platforms = trim($_POST['platform'] ?? '');
    $publisher = trim($_POST['publisher'] ?? '');
    $developer = trim($_POST['developer'] ?? '');
    $release_date = trim($_POST['release_date'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $cover_Image = trim($_POST['cover_url'] ?? '');

    try{
        $stmt = $pdo->prepare('SELECT DSName FROM DeveloperStudio WHERE DSName = ?');
        $stmt->execute([$developer]);
        $developer_row = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare('SELECT PName FROM Publisher WHERE PName = ?');
        $stmt->execute([$publisher]);
        $publisher_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($developer_row && !$publisher_row) {
            $DSName = $developer_row['DSName'];
            $stmt = $pdo->prepare('CALL MakeGame(?,?,?,?,?,?,?,?)');
            $stmt->execute([$gname, $genre, $platforms, $publisher, $DSName, $release_date, $description, $cover_Image]);
            echo("<p style='color: green;'><strong>Success:</strong> Game created successfully.</p>");
        }elseif ($publisher_row && !$developer_row) {
            $PName = $publisher_row['PName'];
            $stmt = $pdo->prepare('CALL MakeGame(?,?,?,?,?,?,?,?)');
            $stmt->execute([$gname, $genre, $platforms, $PName, $developer, $release_date, $description, $cover_Image]);
            echo("<p style='color: green;'><strong>Success:</strong> Game created successfully.</p>");
        }else if ($developer_row && $publisher_row) {
            $DSName = $developer_row['DSName'];
            $PName = $publisher_row['PName'];
            $stmt = $pdo->prepare('CALL MakeGame(?,?,?,?,?,?,?,?)');
            $stmt->execute([$gname, $genre, $platforms, $PName, $DSName, $release_date, $description, $cover_Image]);
            echo("<p style='color: green;'><strong>Success:</strong> Game created successfully.</p>");
        }else{
        
        $stmt = $pdo->prepare('
        INSERT INTO Publisher(PName) Values(?)
        ');
        $stmt->execute([$publisher]);

         $stmt = $pdo->prepare('
        INSERT INTO DeveloperStudio(DSName) Values(?)
        ');
        $stmt->execute([$developer]);
        
        $stmt = $pdo->prepare('
        CALL MakeGame(?,?,?,?,?,?,?,?)
        ');
        $stmt->execute([$gname, $genre, $platforms, $publisher, $developer, $release_date, $description, $cover_Image]);
        echo("<p style='color: green;'><strong>Success:</strong> Game created successfully.</p>");
        }
    }
    catch (PDOException $e) {
        echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Game — Fishing for Games</title>
  <link href="dashboard.css" rel="stylesheet">
  <link href="create_game_style.css" rel="stylesheet">
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
    <a href="create_game.php" class="nav-link active">Create Game</a>

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
      <?php endif; ?>
    </div>

  </nav>

  <!-- ── Main Content ── -->
  <main id="main">

    <a href="dashboard.php" class="back-link">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 12H5"/>
        <path d="M12 19l-7-7 7-7"/>
      </svg>
      Back to The Dock
    </a>

    <section class="create-hero">
      <h1 class="create-heading">Create a New Catch</h1>
      <p class="create-subtitle">
        Add a new game to the dock. Once created, players can view the game page and cast reviews that feed into the average score.
      </p>
    </section>

    <div class="create-layout">

      <!-- Create Form -->
      <section class="form-card">
        <div class="form-section-title">Game Details</div>

        <form action="create_game.php" method="POST">

          <div class="form-grid">

            <div class="form-group full">
              <label class="form-label" for="gname">Game Name</label>
              <input 
                class="form-input"
                type="text"
                id="gname"
                name="gname"
                placeholder="Example: Elden Ring"
                value="<?= htmlspecialchars($gname) ?>"
                required
              >
            </div>

            <div class="form-group">
              <label class="form-label" for="platform">Platform</label>
              <input
                class="form-input"
                type="text"
                id="platform"
                name="platform"
                placeholder="Example: PS5"
                value="<?= htmlspecialchars($platform) ?>"
                required
              >
            </div>

            <div class="form-group">
              <label class="form-label" for="genre">Genre</label>
              <input
                class="form-input"
                type="text"
                id="genre"
                name="genre"
                placeholder="Example: Action RPG"
                value="<?= htmlspecialchars($genre) ?>"
              >
            </div>

            <div class="form-group">
              <label class="form-label" for="developer">Developer</label>
              <input
                class="form-input"
                type="text"
                id="developer"
                name="developer"
                placeholder="Example: FromSoftware"
                value="<?= htmlspecialchars($developer) ?>"
              >
            </div>

            <div class="form-group">
              <label class="form-label" for="publisher">Publisher</label>
              <input
                class="form-input"
                type="text"
                id="publisher"
                name="publisher"
                placeholder="Example: Bandai Namco"
                value="<?= htmlspecialchars($publisher) ?>"
              >
            </div>

            <div class="form-group">
              <label class="form-label" for="release_date">Release Date</label>
              <input
                class="form-input"
                type="date"
                id="release_date"
                name="release_date"
                value="<?= htmlspecialchars($release_date) ?>"
              >
            </div>

            <div class="form-group">
              <label class="form-label" for="cover_url">Cover URL</label>
              <input
                class="form-input"
                type="url"
                id="cover_url"
                name="cover_url"
                placeholder="https://example.com/cover.jpg"
                value="<?= htmlspecialchars($cover_url) ?>"
              >
            </div>

            <div class="form-group full">
              <label class="form-label" for="description">Description</label>
              <textarea
                class="form-textarea"
                id="description"
                name="description"
                placeholder="Write a short description for this game..."
              ><?= htmlspecialchars($description) ?></textarea>
            </div>

          </div>

          <div class="form-actions">
            <span class="form-note">
              This will insert a new record into the Game table.
            </span>

            <div class="button-row">
              <a href="dashboard.php" class="btn-secondary">Cancel</a>
              <button type="submit" class="btn-cast">Create Game</button>
            </div>
          </div>

        </form>
      </section>

    </div>

  </main>

  <script>
    const coverInput = document.getElementById('cover_url');
    const coverImg = document.getElementById('coverPreviewImg');
    const coverPlaceholder = document.getElementById('coverPlaceholder');

    function updateCoverPreview() {
      const url = coverInput.value.trim();

      if (!url) {
        coverImg.style.display = 'none';
        coverImg.src = '';
        coverPlaceholder.style.display = 'block';
        return;
      }

      coverImg.src = url;
      coverImg.style.display = 'block';
      coverPlaceholder.style.display = 'none';
    }

    coverInput.addEventListener('input', updateCoverPreview);

    coverImg.addEventListener('error', function() {
      coverImg.style.display = 'none';
      coverPlaceholder.style.display = 'block';
      coverPlaceholder.innerHTML = 'Invalid Cover<br>URL';
    });

    updateCoverPreview();
  </script>
  
</body>
</html>