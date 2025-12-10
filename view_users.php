<?php

require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

// Path to your service account key file
$serviceAccountPath = __DIR__ . '/pickleball8dots-35ddd-firebase-adminsdk-fbsvc-cac56b31dc.json';

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Pickleball 8 Dots Users</h1>

    <?php
    try {
        // Reference to the 'users' node
        $reference = $database->getReference('users');
        $snapshot = $reference->getSnapshot();
        $users = $snapshot->getValue();

        if (empty($users)) {
            echo "<p>No users found in the database.</p>";
        } else {
            echo "<table>";
            echo "<thead><tr>";
            echo "<th>Mobile Number</th>";
            echo "<th>Name</th>";
            echo "<th>City</th>";
            echo "<th>DOB</th>";
            echo "<th>Gender</th>";
            echo "<th>Playing Hand</th>";
            echo "<th>Photo URL</th>";
            echo "<th>Updated At</th>";
            echo "</tr></thead>";
            echo "<tbody>";

            foreach ($users as $mobileNumber => $userData) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($mobileNumber) . "</td>";
                echo "<td>" . htmlspecialchars($userData['name'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($userData['city'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($userData['dob'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($userData['gender'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($userData['playingHand'] ?? 'N/A') . "</td>";
                echo "<td>";
                if (!empty($userData['photoUrl'])) {
                    echo "<a href=\"" . htmlspecialchars($userData['photoUrl']) . "\" target=\"_blank\">View Photo</a>";
                } else {
                    echo "N/A";
                }
                echo "</td>";
                echo "<td>" . htmlspecialchars($userData['updatedAt'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "<p class=\"error\">Error fetching users: " . $e->getMessage() . "</p>";
    }
    ?>
</body>
</html>