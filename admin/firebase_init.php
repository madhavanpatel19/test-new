<?php

require __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

// Path to your service account key file
$serviceAccountPath = __DIR__ . '/../pickleball8dots-35ddd-firebase-adminsdk-fbsvc-cac56b31dc.json';

// Check if the service account file exists
if (!file_exists($serviceAccountPath)) {
    die("Error: Firebase service account key file not found at " . $serviceAccountPath);
}

// Initialize Firebase
try {
    $factory = (new Factory)
        ->withServiceAccount($serviceAccountPath)
        ->withDatabaseUri('https://pickleball8dots-35ddd-default-rtdb.firebaseio.com/'); // Replace with your actual Firebase project URL

    $database = $factory->createDatabase();
} catch (Exception $e) {
    die("Error initializing Firebase: " . $e->getMessage());
}

?>