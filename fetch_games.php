<?php
// ============================================================
//  Fishing for Games — Fetch Games (JSON endpoint)
//  fetch_games.php
//  Called via AJAX from dashboard.php live search
// ============================================================

require_once("config.php");

header("Content-Type: application/json");

$term = trim($_POST['search'] ?? '');
$like = "%" . $term . "%";

try {
    $pdo = get_pdo();

    $stmt = $pdo->prepare("
        SELECT
            g.GID,
            g.GName,
            g.Platforms,
            g.Genre,
            g.DSName,
            g.PName,
            g.GReleaseDate,
            g.Description,
            g.Cover_Image,
            g.Rating,
            COUNT(r.RID) AS ReviewCount
        FROM Game g
        LEFT JOIN Review r ON g.GID = r.GID
        WHERE
            :term = ''
            OR g.GName LIKE :like1
        GROUP BY
            g.GID,
            g.GName,
            g.Platforms,
            g.Genre,
            g.DSName,
            g.PName,
            g.GReleaseDate,
            g.Description,
            g.Cover_Image
        ORDER BY g.GName ASC
    ");

    $stmt->execute([
        ':term' => $term,
        ':like1' => $like,
    ]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
    exit;
}
