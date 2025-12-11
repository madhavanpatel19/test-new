<?php
require __DIR__ . '/firebase_init.php';

// Fetch groups
$groups_data = [];
$error = null;
try {
    $reference = $database->getReference('groups');
    $tournaments_reference = $database->getReference('tournaments');
    $snapshot = $reference->getSnapshot();
    $tournaments_snapshot = $tournaments_reference->getSnapshot();
    $groups_data = $snapshot->getValue();
    $tournaments = $tournaments_snapshot->getValue();
} catch (Exception $e) {
    $error = "Error fetching groups: " . $e->getMessage();
}

include __DIR__ . '/header.php';
?>

<h1>Groups</h1>
<!-- <a href="create.php" class="button">Add New Group</a> -->

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php elseif (empty($groups_data)): ?>
    <p>No groups found in the database.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Tournament Name</th>
                <th>Group ID</th>
                <th>Group Name</th>
                <th>Created At</th>
                <!-- <th>Actions</th> -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groups_data as $tournamentId => $groups): ?>
                <?php foreach ($groups as $groupId => $groupData): ?>
                    <?php
                        // Look up tournament details by tournament id
                        $tournamentData = $tournaments[$tournamentId ?? 'N/A'] ?? [];
                        $tournamentName = $tournamentData['name'] ?? 'N/A';
                    ?>
                <tr>
                    <td><?= htmlspecialchars($tournamentName) ?></td>
                    <td><?= htmlspecialchars($groupId) ?></td>
                    <td><?= htmlspecialchars($groupData['groupName'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($groupData['createdAt'] ?? 'N/A') ?></td>
                    <!-- <td>
                        <a href="edit.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&group_id=<?= htmlspecialchars($groupId) ?>">Edit</a>
                        <a href="delete.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&group_id=<?= htmlspecialchars($groupId) ?>" onclick="return confirm('Are you sure you want to delete this group?');">Delete</a>
                    </td> -->
                </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
