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

include __DIR__ . '/../header.php';
?>

<h1>Manage Users</h1>
<a href="create.php" class="button">Add New User</a>

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
                    <td>
                        <a href="edit.php?id=<?= htmlspecialchars($mobileNumber) ?>">Edit</a>
                        <a href="delete.php?id=<?= htmlspecialchars($mobileNumber) ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../footer.php'; ?>
