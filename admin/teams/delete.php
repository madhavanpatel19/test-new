<?php
require __DIR__ . '/../firebase_init.php';

$tournamentId = $_GET['tournament_id'] ?? null;
$teamId = $_GET['team_id'] ?? null;

if (!$tournamentId || !$teamId) {
    die("Tournament ID or Team ID not provided.");
}

try {
    $database->getReference('teams/' . $tournamentId . '/' . $teamId)->remove();
    header("Location: index.php");
    exit();
} catch (Exception $e) {
    die("Error deleting team: " . $e->getMessage());
}
