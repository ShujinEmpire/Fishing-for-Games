<?php
// ============================================================
//  Fishing for Games — Fetch Games (JSON endpoint)
//  fetch_games.php
//  Called via AJAX from dashboard.php live search
// ============================================================

require_once("config.php");

header("Content-Type: application/json");

$search = "%" . ($_POST['search'] ?? '') . "%";
$results = [];

// ── Try DB first ─────────────────────────────────────────────
try {
    $pdo = get_pdo();

    $stmt = $pdo->prepare("
        SELECT g.GameID, g.Title, g.Platform, g.Genre, g.Developer,
               g.Publisher, g.ReleaseDate, g.Description, g.CoverURL,
               COALESCE(AVG(r.Score), 0) AS AvgScore,
               COUNT(r.ReviewID) AS ReviewCount
        FROM Game g
        LEFT JOIN Review r ON g.GameID = r.GameID
        WHERE g.Title LIKE ?
        GROUP BY g.GameID
        ORDER BY g.Title ASC
    ");
    $stmt->execute([$search]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // DB not available — fall through to mock data
}

// ── Mock data fallback ───────────────────────────────────────
// Remove this block once your Game table is populated.
if (empty($results)) {
    $mock = [
        [
            'GameID'      => 1,
            'Title'       => 'Elden Ring',
            'Platform'    => 'PS5',
            'Genre'       => 'Action RPG',
            'Developer'   => 'FromSoftware',
            'Publisher'   => 'Bandai Namco',
            'ReleaseDate' => '2022-02-25',
            'AvgScore'    => 4.3,
            'ReviewCount' => 4,
        ],
        [
            'GameID'      => 2,
            'Title'       => 'The Legend of Zelda: Tears of the Kingdom',
            'Platform'    => 'Switch',
            'Genre'       => 'Adventure',
            'Developer'   => 'Nintendo EPD',
            'Publisher'   => 'Nintendo',
            'ReleaseDate' => '2023-05-12',
            'AvgScore'    => 4.8,
            'ReviewCount' => 6,
        ],
        [
            'GameID'      => 3,
            'Title'       => 'Baldur\'s Gate 3',
            'Platform'    => 'PC',
            'Genre'       => 'RPG',
            'Developer'   => 'Larian Studios',
            'Publisher'   => 'Larian Studios',
            'ReleaseDate' => '2023-08-03',
            'AvgScore'    => 4.7,
            'ReviewCount' => 5,
        ],
        [
            'GameID'      => 4,
            'Title'       => 'Cyberpunk 2077',
            'Platform'    => 'PC',
            'Genre'       => 'Action RPG',
            'Developer'   => 'CD Projekt Red',
            'Publisher'   => 'CD Projekt',
            'ReleaseDate' => '2020-12-10',
            'AvgScore'    => 3.2,
            'ReviewCount' => 3,
        ],
        [
            'GameID'      => 5,
            'Title'       => 'Hollow Knight',
            'Platform'    => 'PC',
            'Genre'       => 'Metroidvania',
            'Developer'   => 'Team Cherry',
            'Publisher'   => 'Team Cherry',
            'ReleaseDate' => '2017-02-24',
            'AvgScore'    => 4.6,
            'ReviewCount' => 7,
        ],
        [
            'GameID'      => 6,
            'Title'       => 'Red Dead Redemption 2',
            'Platform'    => 'PS4',
            'Genre'       => 'Action Adventure',
            'Developer'   => 'Rockstar Games',
            'Publisher'   => 'Rockstar Games',
            'ReleaseDate' => '2018-10-26',
            'AvgScore'    => 4.9,
            'ReviewCount' => 8,
        ],
    ];

    // Filter mock data by search term
    $term = strtolower(trim($_POST['search'] ?? ''));
    if ($term === '') {
        $results = $mock;
    } else {
        $results = array_values(array_filter($mock, function($g) use ($term) {
            return stripos($g['Title'], $term) !== false
                || stripos($g['Genre'], $term) !== false
                || stripos($g['Platform'], $term) !== false
                || stripos($g['Developer'], $term) !== false;
        }));
    }
}

echo json_encode($results);
