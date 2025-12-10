<?php
require __DIR__ . '/../firebase_init.php';

// Fetch users
$users = [];
$error = null;
try {
    $reference = $database->getReference('users');
    $snapshot = $reference->getSnapshot();
    $users = $snapshot->getValue();
} catch (Exception $e) {
    $error = "Error fetching users: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .error { color: red; font-weight: bold; }
        .action-links a { margin-right: 10px; text-decoration: none; }
        .add-link { display: inline-block; margin-bottom: 20px; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Manage Users</h1>
    <a href="create.php" class="add-link">Add New User</a>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif (empty($users)): ?>
        <p>No users found in the database.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Mobile Number</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>DOB</th>
                    <th>Gender</th>
                    <th>Playing Hand</th>
                    <th>Photo URL</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $mobileNumber => $userData): ?>
                    <tr>
                        <td><?= htmlspecialchars($mobileNumber) ?></td>
                        <td><?= htmlspecialchars($userData['name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($userData['city'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($userData['dob'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($userData['gender'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($userData['playingHand'] ?? 'N/A') ?></td>
                        <td>
                            <?php if (!empty($userData['photoUrl'])): ?>
                                <a href="<?= htmlspecialchars($userData['photoUrl']) ?>" target="_blank">View Photo</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($userData['updatedAt'] ?? 'N/A') ?></td>
                        <td class="action-links">
                            <a href="edit.php?id=<?= htmlspecialchars($mobileNumber) ?>">Edit</a>
                            <a href="delete.php?id=<?= htmlspecialchars($mobileNumber) ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
