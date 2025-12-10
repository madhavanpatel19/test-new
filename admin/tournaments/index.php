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

include __DIR__ . '/../header.php';
?>

<h1>Manage Tournaments</h1>
<a href="create.php" class="button">Add New Tournament</a>

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
                    <td>
                        <a href="edit.php?id=<?= htmlspecialchars($tournamentId) ?>">Edit</a>
                        <a href="delete.php?id=<?= htmlspecialchars($tournamentId) ?>" onclick="return confirm('Are you sure you want to delete this tournament?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../footer.php'; ?>
