<?php
require __DIR__ . '/../firebase_init.php';

$message = '';
$userId = $_GET['id'] ?? null;
$user = null;

if (!$userId) {
    die("User ID not provided.");
}

// Fetch user data
try {
    $user = $database->getReference('users/' . $userId)->getSnapshot()->getValue();
} catch (Exception $e) {
    die("Error fetching user: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userData = [
        'name' => $_POST['name'],
        'city' => $_POST['city'],
        'dob' => $_POST['dob'],
        'gender' => $_POST['gender'],
        'playingHand' => $_POST['playingHand'],
        'photoUrl' => $_POST['photoUrl'],
        'updatedAt' => (new DateTime())->format('Y-m-d H:i:s')
    ];

    try {
        $database->getReference('users/' . $userId)->update($userData);
        $message = "User updated successfully!";
        // Refresh user data
        $user = $database->getReference('users/' . $userId)->getSnapshot()->getValue();
    } catch (Exception $e) {
        $message = "Error updating user: " . $e->getMessage();
    }
}

include __DIR__ . '/../header.php';
?>

<h1>Edit User</h1>
<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>
<?php if ($user): ?>
<form action="edit.php?id=<?= htmlspecialchars($userId) ?>" method="POST">
    <div>
        <label for="mobile">Mobile Number</label>
        <input type="text" id="mobile" name="mobile" value="<?= htmlspecialchars($userId) ?>" disabled>
    </div>
    <div>
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
    </div>
    <div>
        <label for="city">City</label>
        <input type="text" id="city" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>">
    </div>
    <div>
        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($user['dob'] ?? '') ?>">
    </div>
    <div>
        <label for="gender">Gender</label>
        <input type="text" id="gender" name="gender" value="<?= htmlspecialchars($user['gender'] ?? '') ?>">
    </div>
    <div>
        <label for="playingHand">Playing Hand</label>
        <input type="text" id="playingHand" name="playingHand" value="<?= htmlspecialchars($user['playingHand'] ?? '') ?>">
    </div>
    <div>
        <label for="photoUrl">Photo URL</label>
        <input type="text" id="photoUrl" name="photoUrl" value="<?= htmlspecialchars($user['photoUrl'] ?? '') ?>">
    </div>
    <button type="submit">Update User</button>
</form>
<?php else: ?>
<p>User not found.</p>
<?php endif; ?>
<br>
<a href="index.php">Back to Users List</a>

<?php include __DIR__ . '/../footer.php'; ?>
