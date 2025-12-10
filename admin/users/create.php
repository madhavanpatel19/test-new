<?php
require __DIR__ . '/../firebase_init.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobileNumber = $_POST['mobile'];
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
        $database->getReference('users/' . $mobileNumber)->set($userData);
        $message = "User created successfully!";
    } catch (Exception $e) {
        $message = "Error creating user: " . $e->getMessage();
    }
}
include __DIR__ . '/../header.php';
?>

<h1>Add New User</h1>
<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>
<form action="create.php" method="POST">
    <div>
        <label for="mobile">Mobile Number</label>
        <input type="text" id="mobile" name="mobile" required>
    </div>
    <div>
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div>
        <label for="city">City</label>
        <input type="text" id="city" name="city">
    </div>
    <div>
        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob">
    </div>
    <div>
        <label for="gender">Gender</label>
        <input type="text" id="gender" name="gender">
    </div>
    <div>
        <label for="playingHand">Playing Hand</label>
        <input type="text" id="playingHand" name="playingHand">
    </div>
    <div>
        <label for="photoUrl">Photo URL</label>
        <input type="text" id="photoUrl" name="photoUrl">
    </div>
    <button type="submit">Add User</button>
</form>
<br>
<a href="index.php">Back to Users List</a>

<?php include __DIR__ . '/../footer.php'; ?>
