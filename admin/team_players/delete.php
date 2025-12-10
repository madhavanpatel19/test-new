<?php
require __DIR__ . '/../firebase_init.php';

$tournamentId = $_GET['tournament_id'] ?? null;
$teamId = $_GET['team_id'] ?? null;
$playerMobile = $_GET['player_mobile'] ?? null;

if (!$tournamentId || !$teamId || !$playerMobile) {
    die("Tournament ID, Team ID, or Player Mobile not provided.");
}

try {
    $database->getReference('team_players/' . $tournamentId . '/' . $teamId . '/' . $playerMobile)->remove();
    header("Location: index.php");
    exit();
} catch (Exception $e) {
    die("Error removing player from team: " . $e->getMessage());
}
