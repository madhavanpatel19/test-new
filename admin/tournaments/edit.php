<?php
require __DIR__ . '/../firebase_init.php';

$message = '';
$tournamentId = $_GET['id'] ?? null;
$tournament = null;

if (!$tournamentId) {
    die("Tournament ID not provided.");
}

// Fetch tournament data
try {
    $tournament = $database->getReference('tournaments/' . $tournamentId)->getSnapshot()->getValue();
} catch (Exception $e) {
    die("Error fetching tournament: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentData = [
        'name' => $_POST['name'],
        'city' => $_POST['city'],
        'court' => $_POST['court'],
        'startDate' => $_POST['startDate'],
        'endDate' => $_POST['endDate'],
        'organizerName' => $_POST['organizerName'],
        'organizerNumber' => $_POST['organizerNumber'],
        'createdUser' => $_POST['createdUser'],
        'categories' => array_map('trim', explode(',', $_POST['categories'])),
        'formats' => array_map('trim', explode(',', $_POST['formats'])),
    ];

    try {
        $database->getReference('tournaments/' . $tournamentId)->update($tournamentData);
        $message = "Tournament updated successfully!";
        // Refresh tournament data
        $tournament = $database->getReference('tournaments/' . $tournamentId)->getSnapshot()->getValue();
    } catch (Exception $e) {
        $message = "Error updating tournament: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tournament</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        .container { max-width: 500px; margin: auto; padding: 20px; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="date"] { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .message { margin-top: 20px; padding: 10px; background-color: #e7f3fe; border-left: 6px solid #2196F3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Tournament</h1>
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($tournament): ?>
        <form action="edit.php?id=<?= htmlspecialchars($tournamentId) ?>" method="POST">
            <div class="form-group">
                <label for="tournamentId">Tournament ID</label>
                <input type="text" id="tournamentId" name="tournamentId" value="<?= htmlspecialchars($tournamentId) ?>" disabled>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($tournament['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" value="<?= htmlspecialchars($tournament['city'] ?? '') ?>">
            </div>
             <div class="form-group">
                <label for="court">Court</label>
                <input type="text" id="court" name="court" value="<?= htmlspecialchars($tournament['court'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="startDate">Start Date</label>
                <input type="date" id="startDate" name="startDate" value="<?= htmlspecialchars($tournament['startDate'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="endDate">End Date</label>
                <input type="date" id="endDate" name="endDate" value="<?= htmlspecialchars($tournament['endDate'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="organizerName">Organizer Name</label>
                <input type="text" id="organizerName" name="organizerName" value="<?= htmlspecialchars($tournament['organizerName'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="organizerNumber">Organizer Number</label>
                <input type="text" id="organizerNumber" name="organizerNumber" value="<?= htmlspecialchars($tournament['organizerNumber'] ?? '') ?>">
            </div>
             <div class="form-group">
                <label for="createdUser">Created User (Mobile)</label>
                <input type="text" id="createdUser" name="createdUser" value="<?= htmlspecialchars($tournament['createdUser'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="categories">Categories (comma-separated)</label>
                <input type="text" id="categories" name="categories" value="<?= isset($tournament['categories']) ? htmlspecialchars(implode(', ', $tournament['categories'])) : '' ?>">
            </div>
            <div class="form-group">
                <label for="formats">Formats (comma-separated)</label>
                <input type="text" id="formats" name="formats" value="<?= isset($tournament['formats']) ? htmlspecialchars(implode(', ', $tournament['formats'])) : '' ?>">
            </div>
            <button type="submit" class="btn">Update Tournament</button>
        </form>
        <?php else: ?>
        <p>Tournament not found.</p>
        <?php endif; ?>
        <br>
        <a href="index.php">Back to Tournaments List</a>
    </div>
</body>
</html>
