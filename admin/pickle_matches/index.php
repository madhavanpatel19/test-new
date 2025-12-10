<?php
require __DIR__ . '/../firebase_init.php';

// Fetch pickle matches
$matches = [];
$error = null;
try {
    $reference = $database->getReference('pickle_matches');
    $snapshot = $reference->getSnapshot();
    $matches = $snapshot->getValue();
} catch (Exception $e) {
    $error = "Error fetching pickle matches: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pickle Matches</title>
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
    <h1>Manage Pickle Matches</h1>
    <a href="create.php" class="add-link">Add New Match</a>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif (empty($matches)): ?>
        <p>No matches found in the database.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Match ID</th>
                    <th>Tournament ID</th>
                    <th>Creator Name</th>
                    <th>Match Type</th>
                    <th>Schedule Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matches as $matchId => $matchData): ?>
                    <tr>
                        <td><?= htmlspecialchars($matchId) ?></td>
                        <td><?= htmlspecialchars($matchData['tournamentId'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($matchData['name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($matchData['matchType'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($matchData['schedule_date'] ?? 'N/A') ?></td>
                        <td><?= ($matchData['matchStatus']['matchCompleted'] ?? false) ? 'Completed' : 'Pending' ?></td>
                        <td class="action-links">
                            <a href="edit.php?id=<?= htmlspecialchars($matchId) ?>">Edit</a>
                            <a href="delete.php?id=<?= htmlspecialchars($matchId) ?>" onclick="return confirm('Are you sure you want to delete this match?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
