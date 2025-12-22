<?php
require __DIR__ . '/firebase_init.php';

// Fetch teams
$teams_data = [];
$error = null;
try {
    $reference = $database->getReference('teams');
    $tournaments_reference = $database->getReference('tournaments');
    $snapshot = $reference->getSnapshot();
    $tournaments_snapshot = $tournaments_reference->getSnapshot();
    $teams_data = $snapshot->getValue();
    $tournaments = $tournaments_snapshot->getValue();
} catch (Exception $e) {
    $error = "Error fetching teams: " . $e->getMessage();
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
                        $tournamentData = $tournaments[$tournamentId] ?? [];
                        $tournamentName = $tournamentData['name'] ?? 'N/A';
                    ?>
                <tr>
                    <!-- <td><?= htmlspecialchars($tournamentId) ?></td> -->
                    <td><?= htmlspecialchars($tournamentName) ?></td>
                    <!-- <td><?= htmlspecialchars($teamId) ?></td> -->
                    <td><a href="teams.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&team_id=<?= htmlspecialchars($teamId) ?>"><?= htmlspecialchars($teamData['teamName'] ?? 'N/A') ?></a></td>
                    <td><?= htmlspecialchars($teamData['captainName'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($teamData['captainNumber'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($teamData['city'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($teamData['groupName'] ?? 'N/A') ?></td>
                    <!-- <td>
                        <a href="edit.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&team_id=<?= htmlspecialchars($teamId) ?>">Edit</a>
                        <a href="delete.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&team_id=<?= htmlspecialchars($teamId) ?>" onclick="return confirm('Are you sure you want to delete this team?');">Delete</a>
                    </td> -->
                </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
// Fetch and display team players if a team is selected
if (isset($_GET['tournament_id']) && isset($_GET['team_id'])) {
    $selected_tournament_id = $_GET['tournament_id'];
    $selected_team_id = $_GET['team_id'];
    $team_players = [];
    $users_data = [];
    $team_name = 'N/A';

    try {
        // Fetch players for the selected team
        $team_players_ref = $database->getReference("team_players/{$selected_tournament_id}/{$selected_team_id}");
        $team_players_snapshot = $team_players_ref->getSnapshot();
        $team_players = $team_players_snapshot->getValue();

        // Fetch all users to get player details
        $users_ref = $database->getReference('users');
        $users_snapshot = $users_ref->getSnapshot();
        $users_data = $users_snapshot->getValue();

        // Fetch team name
        $team_ref = $database->getReference("teams/{$selected_tournament_id}/{$selected_team_id}");
        $team_snapshot = $team_ref->getSnapshot();
        $team_info = $team_snapshot->getValue();
        $team_name = $team_info['teamName'] ?? 'N/A';

    } catch (Exception $e) {
        $error = "Error fetching team players: " . $e->getMessage();
    }
?>
    <h2>Players in <?= htmlspecialchars($team_name) ?></h2>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif (empty($team_players)): ?>
        <p>No players found for this team.</p>
    <?php else: ?>
        <table class="player-table">
            <thead>
                <tr>
                    <th>Player Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Phone Number</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($team_players as $player_mobile => $player_data): ?>
                    <?php
                        // Look up user details by phone number (key of users node)
                        $player_details = null;
                        if (isset($users_data[$player_mobile])) {
                            $player_details = $users_data[$player_mobile];
                        }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($player_details['name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($player_details['email'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($player_details['gender'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($player_mobile) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php
}
?>

<?php include __DIR__ . '/footer.php'; ?>
