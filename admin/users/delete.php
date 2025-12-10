<?php
require __DIR__ . '/../firebase_init.php';

$userId = $_GET['id'] ?? null;

if (!$userId) {
    die("User ID not provided.");
}

try {
    $database->getReference('users/' . $userId)->remove();
    header("Location: index.php");
    exit();
} catch (Exception $e) {
    die("Error deleting user: " . $e->getMessage());
}
