<?php

$user = "asanchez"; 
$pass = "qwerty070606@#"; 
$db   = "asanchez";

// Establish a PDO connection
function get_pdo() {
    global $user, $pass, $db;
    $host = "localhost";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo "<p><strong>Connection failed:</strong> " . $e->getMessage() . "</p>";
        die();
    }
}
