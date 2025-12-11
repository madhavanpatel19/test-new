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

include 'admin/header.php';
?>

<h1>Pickleball Users</h1>

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

include 'admin/footer.php';
?>
