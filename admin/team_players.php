<?php
require __DIR__ . '/firebase_init.php';

// Fetch team players
$team_players_data = [];
$error = null;
try {
    $reference = $database->getReference('team_players');
    $teams_reference = $database->getReference('teams');
    $tournaments_reference = $database->getReference('tournaments');
    $snapshot = $reference->getSnapshot();
    $teams_snapshot = $teams_reference->getSnapshot();
    $tournaments_snapshot = $tournaments_reference->getSnapshot();
    $team_players_data = $snapshot->getValue();
    $teams_data = $teams_snapshot->getValue();
    $tournaments = $tournaments_snapshot->getValue();
} catch (Exception $e) {
    $error = "Error fetching team players: " . $e->getMessage();
} catch (Exception $e) {
    $error = "Error fetching tournaments: " . $e->getMessage();
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
                <th>Tournament Name</th>
                <!-- <th>Team ID</th> -->
                <th>Team name</th>
                <th>Player Mobile</th>
                <th>Joined At</th>
                <!-- <th>Actions</th> -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($team_players_data as $tournamentId => $teams): ?>
                <?php foreach ($teams as $teamId => $players): ?>
                    <?php
                        // Look up team details by tournament/team id
                        $teamData = $teams_data[$tournamentId][$teamId] ?? [];
                        $teamName = $teamData['teamName'] ?? 'N/A';
                        $tournamentData = $tournaments[$tournamentId ?? 'N/A'] ?? [];
                        $tournamentName = $tournamentData['name'] ?? 'N/A';
                    ?>
                    <?php foreach ($players as $playerMobile => $playerData): ?>
                    <tr>
                        <!-- <td><?= htmlspecialchars($teamId) ?></td> -->
                        <td><?= htmlspecialchars($tournamentName) ?></td>
                        <td><?= htmlspecialchars($teamName) ?></td>
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
