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

include __DIR__ . '/../header.php';
?>

<h1>Manage Pickle Matches</h1>
<a href="create.php" class="button">Add New Match</a>

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
                    <td>
                        <a href="edit.php?id=<?= htmlspecialchars($matchId) ?>">Edit</a>
                        <a href="delete.php?id=<?= htmlspecialchars($matchId) ?>" onclick="return confirm('Are you sure you want to delete this match?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../footer.php'; ?>
