<?php
require __DIR__ . '/../firebase_init.php';

$matchId = $_GET['id'] ?? null;

if (!$matchId) {
    die("Match ID not provided.");
}

try {
    $database->getReference('pickle_matches/' . $matchId)->remove();
    header("Location: index.php");
    exit();
} catch (Exception $e) {
    die("Error deleting match: " . $e->getMessage());
}
