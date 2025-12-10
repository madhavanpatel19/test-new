<?php
require __DIR__ . '/../firebase_init.php';

// Fetch teams
$teams_data = [];
$error = null;
try {
    $reference = $database->getReference('teams');
    $snapshot = $reference->getSnapshot();
    $teams_data = $snapshot->getValue();
} catch (Exception $e) {
    $error = "Error fetching teams: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teams</title>
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
    <h1>Manage Teams</h1>
    <a href="create.php" class="add-link">Add New Team</a>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif (empty($teams_data)): ?>
        <p>No teams found in the database.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Tournament ID</th>
                    <th>Team ID</th>
                    <th>Team Name</th>
                    <th>Captain Name</th>
                    <th>City</th>
                    <th>Group Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams_data as $tournamentId => $teams): ?>
                    <?php foreach ($teams as $teamId => $teamData): ?>
                    <tr>
                        <td><?= htmlspecialchars($tournamentId) ?></td>
                        <td><?= htmlspecialchars($teamId) ?></td>
                        <td><?= htmlspecialchars($teamData['teamName'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($teamData['captainName'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($teamData['city'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($teamData['groupName'] ?? 'N/A') ?></td>
                        <td class="action-links">
                            <a href="edit.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&team_id=<?= htmlspecialchars($teamId) ?>">Edit</a>
                            <a href="delete.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&team_id=<?= htmlspecialchars($teamId) ?>" onclick="return confirm('Are you sure you want to delete this team?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
