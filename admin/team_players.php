<?php
require __DIR__ . '/firebase_init.php';

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

include __DIR__ . '/header.php';
?>

<h1>Team Players</h1>
<!-- <a href="create.php" class="button">Add New Player to Team</a> -->

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
                <!-- <th>Actions</th> -->
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
                        <!-- <td>
                            <a href="delete.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&team_id=<?= htmlspecialchars($teamId) ?>&player_mobile=<?= htmlspecialchars($playerMobile) ?>" onclick="return confirm('Are you sure you want to remove this player from the team?');">Delete</a>
                        </td> -->
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
