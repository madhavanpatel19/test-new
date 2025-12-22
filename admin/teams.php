<?php
require __DIR__ . '/firebase_init.php';

// Fetch all necessary data
$teams_data = [];
$tournaments_data = [];
$team_players_data = [];
$users_data = [];
$error = null;
try {
    $teams_ref = $database->getReference('teams');
    $tournaments_ref = $database->getReference('tournaments');
    $team_players_ref = $database->getReference('team_players');
    $users_ref = $database->getReference('users');

    $teams_data = $teams_ref->getSnapshot()->getValue();
    $tournaments_data = $tournaments_ref->getSnapshot()->getValue();
    $team_players_data = $team_players_ref->getSnapshot()->getValue();
    $users_data = $users_ref->getSnapshot()->getValue();
} catch (Exception $e) {
    $error = "Error fetching data: " . $e->getMessage();
}

include __DIR__ . '/header.php';
?>

<h1>Teams</h1>
<!-- <a href="create.php" class="button">Add New Team</a> -->

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php elseif (empty($teams_data)): ?>
    <p>No teams found in the database.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <!-- <th>Tournament ID</th> -->
                <th>Tournament Name</th>
                <!-- <th>Team ID</th> -->
                <th>Team name</th>
                <th>Captain Name</th>
                <th>Captain Number</th>
                <th>City</th>
                <th>Group Name</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach ($teams_data as $tournamentId => $teams): ?>
                <?php foreach ($teams as $teamId => $teamData): ?>
                    <?php
                        // Look up tournament details by tournament id
                        $tournamentData = $tournaments_data[$tournamentId] ?? [];
                        $tournamentName = $tournamentData['name'] ?? 'N/A';
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($tournamentName) ?></td>
                        <td><?= htmlspecialchars($teamData['teamName'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($teamData['captainName'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($teamData['captainNumber'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($teamData['city'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($teamData['groupName'] ?? 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div class="nested-table-container">
                                <?php
                                $players = $team_players_data[$tournamentId][$teamId] ?? [];
                                if (!empty($players)):
                                ?>
                                <table class="nested-table">
                                    <thead>
                                        <tr>
                                            <th>Player Name</th>
                                            <th>Player Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($players as $playerMobile => $playerData): ?>
                                            <?php
                                            $userDetails = $users_data[$playerMobile] ?? null;
                                            $playerName = $userDetails['name'] ?? 'N/A';
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($playerName) ?></td>
                                                <td><?= htmlspecialchars($playerMobile) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <p>No players in this team.</p>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
