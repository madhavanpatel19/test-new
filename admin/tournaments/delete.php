<?php
require __DIR__ . '/../firebase_init.php';

$tournamentId = $_GET['id'] ?? null;

if (!$tournamentId) {
    die("Tournament ID not provided.");
}

try {
    $database->getReference('tournaments/' . $tournamentId)->remove();
    header("Location: index.php");
    exit();
} catch (Exception $e) {
    die("Error deleting tournament: " . $e->getMessage());
}
