<?php
require __DIR__ . '/../firebase_init.php';

$tournamentId = $_GET['tournament_id'] ?? null;
$groupId = $_GET['group_id'] ?? null;

if (!$tournamentId || !$groupId) {
    die("Tournament ID or Group ID not provided.");
}

try {
    $database->getReference('groups/' . $tournamentId . '/' . $groupId)->remove();
    header("Location: index.php");
    exit();
} catch (Exception $e) {
    die("Error deleting group: " . $e->getMessage());
}
