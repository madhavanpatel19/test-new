<?php
require __DIR__ . '/../firebase_init.php';

// Fetch tournaments
$tournaments = [];
$error = null;
try {
    $reference = $database->getReference('tournaments');
    $snapshot = $reference->getSnapshot();
    $tournaments = $snapshot->getValue();
} catch (Exception $e) {
    $error = "Error fetching tournaments: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tournaments</title>
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
    <h1>Manage Tournaments</h1>
    <a href="create.php" class="add-link">Add New Tournament</a>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif (empty($tournaments)): ?>
        <p>No tournaments found in the database.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Organizer</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tournaments as $tournamentId => $tournamentData): ?>
                    <tr>
                        <td><?= htmlspecialchars($tournamentId) ?></td>
                        <td><?= htmlspecialchars($tournamentData['name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($tournamentData['city'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($tournamentData['startDate'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($tournamentData['endDate'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($tournamentData['organizerName'] ?? 'N/A') ?></td>
                        <td class="action-links">
                            <a href="edit.php?id=<?= htmlspecialchars($tournamentId) ?>">Edit</a>
                            <a href="delete.php?id=<?= htmlspecialchars($tournamentId) ?>" onclick="return confirm('Are you sure you want to delete this tournament?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
