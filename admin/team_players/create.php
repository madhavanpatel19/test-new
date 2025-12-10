<?php
require __DIR__ . '/../firebase_init.php';

// Fetch tournaments, teams, and users for the dropdowns
$tournaments = [];
$all_teams = [];
$users = [];
$error = null;
try {
    $tournaments = $database->getReference('tournaments')->getSnapshot()->getValue();
    $all_teams = $database->getReference('teams')->getSnapshot()->getValue();
    $users = $database->getReference('users')->getSnapshot()->getValue();
} catch (Exception $e) {
    $error = "Error fetching data for dropdowns: " . $e->getMessage();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentId = $_POST['tournamentId'];
    $teamId = $_POST['teamId'];
    $playerMobile = $_POST['playerMobile'];

    $playerData = [
        'joinedAt' => time()
    ];

    try {
        $database->getReference('team_players/' . $tournamentId . '/' . $teamId . '/' . $playerMobile)->set($playerData);
        $message = "Player added to team successfully!";
    } catch (Exception $e) {
        $message = "Error adding player to team: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Player to Team</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        .container { max-width: 500px; margin: auto; padding: 20px; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        select { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .message { margin-top: 20px; padding: 10px; background-color: #e7f3fe; border-left: 6px solid #2196F3; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Player to Team</h1>
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
                <label for="teamId">Team</label>
                <select id="teamId" name="teamId" required>
                    <option value="">Select a Team</option>
                </select>
            </div>
            <div class="form-group">
                <label for="playerMobile">Player</label>
                <select id="playerMobile" name="playerMobile" required>
                    <option value="">Select a Player</option>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $mobile => $user): ?>
                            <option value="<?= htmlspecialchars($mobile) ?>"><?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($mobile) ?>)</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <button type="submit" class="btn">Add Player</button>
        </form>
        <br>
        <a href="index.php">Back to Team Players List</a>
    </div>

    <script>
        const teamsData = <?= json_encode($all_teams ?? []) ?>;
        const tournamentSelect = document.getElementById('tournamentId');
        const teamSelect = document.getElementById('teamId');

        tournamentSelect.addEventListener('change', function() {
            const selectedTournamentId = this.value;
            // Clear existing options
            teamSelect.innerHTML = '<option value="">Select a Team</option>';

            if (selectedTournamentId && teamsData[selectedTournamentId]) {
                const teams = teamsData[selectedTournamentId];
                for (const teamId in teams) {
                    if (teams.hasOwnProperty(teamId)) {
                        const option = document.createElement('option');
                        option.value = teamId;
                        option.textContent = teams[teamId].teamName;
                        teamSelect.appendChild(option);
                    }
                }
            }
        });
    </script>
</body>
</html>
