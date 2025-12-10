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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        .container { max-width: 500px; margin: auto; padding: 20px; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="date"] { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .message { margin-top: 20px; padding: 10px; background-color: #e7f3fe; border-left: 6px solid #2196F3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New User</h1>
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        <form action="create.php" method="POST">
            <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" required>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city">
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob">
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <input type="text" id="gender" name="gender">
            </div>
            <div class="form-group">
                <label for="playingHand">Playing Hand</label>
                <input type="text" id="playingHand" name="playingHand">
            </div>
            <div class="form-group">
                <label for="photoUrl">Photo URL</label>
                <input type="text" id="photoUrl" name="photoUrl">
            </div>
            <button type="submit" class="btn">Add User</button>
        </form>
        <br>
        <a href="index.php">Back to Users List</a>
    </div>
</body>
</html>
