<?php
require __DIR__ . '/../firebase_init.php';

// Fetch tournaments for the dropdown
$tournaments = [];
$error = null;
try {
    $tournamentReference = $database->getReference('tournaments');
    $tournaments = $tournamentReference->getSnapshot()->getValue();
} catch (Exception $e) {
    $error = "Error fetching tournaments: " . $e->getMessage();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matchId = $_POST['matchId'];
    $matchData = [
        'tournamentId' => $_POST['tournamentId'],
        'name' => $_POST['name'],
        'mobile' => $_POST['mobile'],
        'city' => $_POST['city'],
        'matchType' => $_POST['matchType'],
        'schedule_date' => $_POST['schedule_date'],
        'schedule_time' => $_POST['schedule_time'],
        'schedule_location' => $_POST['schedule_location'],
        'createdAt' => (new DateTime())->format('Y-m-d H:i:s'),
        'matchStatus' => [
            'matchCompleted' => false,
            'completedAt' => null,
            'winner' => ''
        ],
        // NOTE: The 'players' and 'gameResults' fields are complex and not included in this simplified form.
        // An advanced interface would be required to manage this nested data.
        'players' => [],
        'gameResults' => []
    ];

    try {
        $database->getReference('pickle_matches/' . $matchId)->set($matchData);
        $message = "Match created successfully!";
    } catch (Exception $e) {
        $message = "Error creating match: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pickle Match</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        .container { max-width: 500px; margin: auto; padding: 20px; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="date"], input[type="time"], select { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .message { margin-top: 20px; padding: 10px; background-color: #e7f3fe; border-left: 6px solid #2196F3; }
        .error { color: red; font-weight: bold; }
    </style>
</head><body>
    <div class="container">
        <h1>Add New Pickle Match</h1>
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="create.php" method="POST">
            <div class="form-group">
                <label for="matchId">Match ID</label>
                <input type="text" id="matchId" name="matchId" required>
            </div>
            <div class="form-group">
                <label for="tournamentId">Tournament</label>
                <select id="tournamentId" name="tournamentId" required>
                    <option value="">Select a Tournament</option>
                    <?php if (!empty($tournaments)): ?>
                        <?php foreach ($tournaments as $id => $tournament): ?>
                            <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($tournament['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Creator Name</label>
                <input type="text" id="name" name="name">
            </div>
            <div class="form-group">
                <label for="mobile">Creator Mobile</label>
                <input type="text" id="mobile" name="mobile">
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city">
            </div>
            <div class="form-group">
                <label for="matchType">Match Type</label>
                <input type="text" id="matchType" name="matchType">
            </div>
            <div class="form-group">
                <label for="schedule_date">Schedule Date</label>
                <input type="date" id="schedule_date" name="schedule_date">
            </div>
            <div class="form-group">
                <label for="schedule_time">Schedule Time</label>
                <input type="time" id="schedule_time" name="schedule_time">
            </div>
            <div class="form-group">
                <label for="schedule_location">Schedule Location</label>
                <input type="text" id="schedule_location" name="schedule_location">
            </div>
            <button type="submit" class="btn">Add Match</button>
        </form>
        <br>
        <a href="index.php">Back to Matches List</a>
    </div>
</body>
</html>
