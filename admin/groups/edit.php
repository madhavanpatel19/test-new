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
$tournamentId = $_GET['tournament_id'] ?? null;
$groupId = $_GET['group_id'] ?? null;
$group = null;

if (!$tournamentId || !$groupId) {
    die("Tournament ID or Group ID not provided.");
}

// Fetch group data
try {
    $group = $database->getReference('groups/' . $tournamentId . '/' . $groupId)->getSnapshot()->getValue();
} catch (Exception $e) {
    die("Error fetching group: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTournamentId = $_POST['tournamentId'];
    $groupData = [
        'groupName' => $_POST['groupName'],
        'tournamentId' => $newTournamentId
    ];

    try {
        // If the tournament ID has changed, we need to move the data
        if ($newTournamentId !== $tournamentId) {
            $database->getReference('groups/' . $newTournamentId . '/' . $groupId)->set($groupData);
            $database->getReference('groups/' . $tournamentId . '/' . $groupId)->remove();
        } else {
            $database->getReference('groups/' . $tournamentId . '/' . $groupId)->update($groupData);
        }

        $message = "Group updated successfully!";
        // Refresh group data
        $tournamentId = $newTournamentId; // Update the tournamentId to the new one
        $group = $database->getReference('groups/' . $tournamentId . '/' . $groupId)->getSnapshot()->getValue();
    } catch (Exception $e) {
        $message = "Error updating group: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Group</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        .container { max-width: 500px; margin: auto; padding: 20px; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], select { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .message { margin-top: 20px; padding: 10px; background-color: #e7f3fe; border-left: 6px solid #2196F3; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Group</h1>
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
         <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($group): ?>
        <form action="edit.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&group_id=<?= htmlspecialchars($groupId) ?>" method="POST">
            <div class="form-group">
                <label for="tournamentId">Tournament</label>
                <select id="tournamentId" name="tournamentId" required>
                    <option value="">Select a Tournament</option>
                    <?php if (!empty($tournaments)): ?>
                        <?php foreach ($tournaments as $id => $tournament): ?>
                            <option value="<?= htmlspecialchars($id) ?>" <?= ($id === $tournamentId) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tournament['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="groupId">Group ID</label>
                <input type="text" id="groupId" value="<?= htmlspecialchars($groupId) ?>" disabled>
            </div>
            <div class="form-group">
                <label for="groupName">Group Name</label>
                <input type="text" id="groupName" name="groupName" value="<?= htmlspecialchars($group['groupName'] ?? '') ?>" required>
            </div>
            <button type="submit" class="btn">Update Group</button>
        </form>
        <?php else: ?>
        <p>Group not found.</p>
        <?php endif; ?>
        <br>
        <a href="index.php">Back to Groups List</a>
    </div>
</body>
</html>
