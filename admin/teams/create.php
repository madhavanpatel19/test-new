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

// Fetch all groups to be used by the dynamic dropdown
$all_groups = [];
try {
    $groupReference = $database->getReference('groups');
    $all_groups = $groupReference->getSnapshot()->getValue();
} catch (Exception $e) {
    $error = ($error ? $error . "<br>" : "") . "Error fetching groups: " . $e->getMessage();
}


$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentId = $_POST['tournamentId'];
    $teamId = $_POST['teamId'];
    $teamData = [
        'teamName' => $_POST['teamName'],
        'captainName' => $_POST['captainName'],
        'captainNumber' => $_POST['captainNumber'],
        'city' => $_POST['city'],
        'groupName' => $_POST['groupName'],
        'groupId' => $_POST['groupId'],
        'tournamentId' => $tournamentId,
        'createdAt' => (new DateTime())->format('Y-m-d H:i:s')
    ];

    try {
        $database->getReference('teams/' . $tournamentId . '/' . $teamId)->set($teamData);
        $message = "Team created successfully!";
    } catch (Exception $e) {
        $message = "Error creating team: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Team</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        .container { max-width: 500px; margin: auto; padding: 20px; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], select { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .message { margin-top: 20px; padding: 10px; background-color: #e7f3fe; border-left: 6px solid #2196F3; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Team</h1>
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
         <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form action="create.php" method="POST">
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
                <label for="groupId">Group</label>
                <select id="groupId" name="groupId" required>
                    <option value="">Select a Group</option>
                </select>
            </div>
            <div class="form-group">
                <label for="teamId">Team ID</label>
                <input type="text" id="teamId" name="teamId" required>
            </div>
            <div class="form-group">
                <label for="teamName">Team Name</label>
                <input type="text" id="teamName" name="teamName" required>
            </div>
            <div class="form-group">
                <label for="captainName">Captain Name</label>
                <input type="text" id="captainName" name="captainName">
            </div>
            <div class="form-group">
                <label for="captainNumber">Captain Number</label>
                <input type="text" id="captainNumber" name="captainNumber">
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city">
            </div>
            <div class="form-group">
                <label for="groupName">Group Name</label>
                <input type="text" id="groupName" name="groupName">
            </div>
            <button type="submit" class="btn">Add Team</button>
        </form>
        <br>
        <a href="index.php">Back to Teams List</a>
    </div>

    <script>
        const groupsData = <?= json_encode($all_groups ?? []) ?>;
        const tournamentSelect = document.getElementById('tournamentId');
        const groupSelect = document.getElementById('groupId');

        tournamentSelect.addEventListener('change', function() {
            const selectedTournamentId = this.value;
            // Clear existing options
            groupSelect.innerHTML = '<option value="">Select a Group</option>';

            if (selectedTournamentId && groupsData[selectedTournamentId]) {
                const groups = groupsData[selectedTournamentId];
                for (const groupId in groups) {
                    if (groups.hasOwnProperty(groupId)) {
                        const option = document.createElement('option');
                        option.value = groupId;
                        option.textContent = groups[groupId].groupName;
                        groupSelect.appendChild(option);
                    }
                }
            }
        });
    </script>
</body>
</html>
