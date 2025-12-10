<?php
require __DIR__ . '/../firebase_init.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentId = $_POST['tournamentId'];
    $tournamentData = [
        'name' => $_POST['name'],
        'city' => $_POST['city'],
        'court' => $_POST['court'],
        'startDate' => $_POST['startDate'],
        'endDate' => $_POST['endDate'],
        'organizerName' => $_POST['organizerName'],
        'organizerNumber' => $_POST['organizerNumber'],
        'createdUser' => $_POST['createdUser'],
        'createdAt' => (new DateTime())->format('Y-m-d H:i:s'),
        'categories' => array_map('trim', explode(',', $_POST['categories'])),
        'formats' => array_map('trim', explode(',', $_POST['formats'])),
        'bannerLocalPath' => '',
        'logoLocalPath' => ''
    ];

    try {
        $database->getReference('tournaments/' . $tournamentId)->set($tournamentData);
        $message = "Tournament created successfully!";
    } catch (Exception $e) {
        $message = "Error creating tournament: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tournament</title>
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
        <h1>Add New Tournament</h1>
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        <form action="create.php" method="POST">
            <div class="form-group">
                <label for="tournamentId">Tournament ID</label>
                <input type="text" id="tournamentId" name="tournamentId" required>
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
                <label for="court">Court</label>
                <input type="text" id="court" name="court">
            </div>
            <div class="form-group">
                <label for="startDate">Start Date</label>
                <input type="date" id="startDate" name="startDate">
            </div>
            <div class="form-group">
                <label for="endDate">End Date</label>
                <input type="date" id="endDate" name="endDate">
            </div>
            <div class="form-group">
                <label for="organizerName">Organizer Name</label>
                <input type="text" id="organizerName" name="organizerName">
            </div>
            <div class="form-group">
                <label for="organizerNumber">Organizer Number</label>
                <input type="text" id="organizerNumber" name="organizerNumber">
            </div>
            <div class="form-group">
                <label for="createdUser">Created User (Mobile)</label>
                <input type="text" id="createdUser" name="createdUser">
            </div>
            <div class="form-group">
                <label for="categories">Categories (comma-separated)</label>
                <input type="text" id="categories" name="categories">
            </div>
            <div class="form-group">
                <label for="formats">Formats (comma-separated)</label>
                <input type="text" id="formats" name="formats">
            </div>
            <button type="submit" class="btn">Add Tournament</button>
        </form>
        <br>
        <a href="index.php">Back to Tournaments List</a>
    </div>
</body>
</html>
