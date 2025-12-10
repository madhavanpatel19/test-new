<?php
require __DIR__ . '/../firebase_init.php';

// Fetch team players
$team_players_data = [];
$error = null;
try {
    $reference = $database->getReference('team_players');
    $snapshot = $reference->getSnapshot();
    $team_players_data = $snapshot->getValue();
} catch (Exception $e) {
    $error = "Error fetching team players: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team Players</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .error { color: red; font-weight: bold; }
        .action-links a { margin-right: 10px; text-decoration: none; }
        .add-link { display: inline-block; margin-bottom: 20px; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Manage Team Players</h1>
    <a href="create.php" class="add-link">Add New Player to Team</a>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif (empty($team_players_data)): ?>
        <p>No team players found in the database.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Tournament ID</th>
                    <th>Team ID</th>
                    <th>Player Mobile</th>
                    <th>Joined At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($team_players_data as $tournamentId => $teams): ?>
                    <?php foreach ($teams as $teamId => $players): ?>
                        <?php foreach ($players as $playerMobile => $playerData): ?>
                        <tr>
                            <td><?= htmlspecialchars($tournamentId) ?></td>
                            <td><?= htmlspecialchars($teamId) ?></td>
                            <td><?= htmlspecialchars($playerMobile) ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d H:i:s', $playerData['joinedAt'])) ?></td>
                            <td class="action-links">
                                <a href="delete.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&team_id=<?= htmlspecialchars($teamId) ?>&player_mobile=<?= htmlspecialchars($playerMobile) ?>" onclick="return confirm('Are you sure you want to remove this player from the team?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
