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

include __DIR__ . '/../header.php';
?>

<h1>Manage Teams</h1>
<a href="create.php" class="button">Add New Team</a>

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
                    <td>
                        <a href="edit.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&team_id=<?= htmlspecialchars($teamId) ?>">Edit</a>
                        <a href="delete.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&team_id=<?= htmlspecialchars($teamId) ?>" onclick="return confirm('Are you sure you want to delete this team?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../footer.php'; ?>
