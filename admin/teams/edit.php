<?php
require __DIR__ . '/../firebase_init.php';

// Fetch tournaments for the dropdown
$tournaments = [];
$error = null;
try {
    $tournamentReference = $database->getReference('tournaments');
    $tournaments = $tournamentReference->getSnapshot()->getValue();
} catch (Exception $e) {
    $error = "Error fetching tournaments: " . $e->getMessage();
}

// Fetch all groups to be used by the dynamic dropdown
$all_groups = [];
try {
    $groupReference = $database->getReference('groups');
    $all_groups = $groupReference->getSnapshot()->getValue();
} catch (Exception $e) {
    $error = ($error ? $error . "<br>" : "") . "Error fetching groups: " . $e->getMessage();
}

$message = '';
$tournamentId = $_GET['tournament_id'] ?? null;
$teamId = $_GET['team_id'] ?? null;
$team = null;

if (!$tournamentId || !$teamId) {
    die("Tournament ID or Team ID not provided.");
}

// Fetch team data
try {
    $team = $database->getReference('teams/' . $tournamentId . '/' . $teamId)->getSnapshot()->getValue();
} catch (Exception $e) {
    die("Error fetching team: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTournamentId = $_POST['tournamentId'];
    $teamData = [
        'teamName' => $_POST['teamName'],
        'captainName' => $_POST['captainName'],
        'captainNumber' => $_POST['captainNumber'],
        'city' => $_POST['city'],
        'groupName' => $_POST['groupName'],
        'groupId' => $_POST['groupId'],
        'tournamentId' => $newTournamentId
    ];

    try {
        // If the tournament ID has changed, we need to move the data
        if ($newTournamentId !== $tournamentId) {
            $database->getReference('teams/' . $newTournamentId . '/' . $teamId)->set($teamData);
            $database->getReference('teams/' . $tournamentId . '/' . $teamId)->remove();
             $message = "Team updated and moved to new tournament successfully!";
        } else {
            $database->getReference('teams/' . $tournamentId . '/' . $teamId)->update($teamData);
            $message = "Team updated successfully!";
        }

        // Refresh team data
        $tournamentId = $newTournamentId;
        $team = $database->getReference('teams/' . $tournamentId . '/' . $teamId)->getSnapshot()->getValue();
    } catch (Exception $e) {
        $message = "Error updating team: " . $e->getMessage();
    }
}
include __DIR__ . '/../header.php';
?>

<h1>Edit Team</h1>
<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>
 <?php if ($error): ?>
    <p class="error"><?= $error ?></p>
<?php endif; ?>
<?php if ($team): ?>
<form action="edit.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&team_id=<?= htmlspecialchars($teamId) ?>" method="POST">
    <div>
        <label for="tournamentId">Tournament</label>
        <select id="tournamentId" name="tournamentId" required>
            <option value="">Select a Tournament</option>
             <?php if (!empty($tournaments)): ?>
                <?php foreach ($tournaments as $id => $tournament): ?>
                    <option value="<?= htmlspecialchars($id) ?>" <?= ($id === $tournamentId) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tournament['name']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <div>
        <label for="groupId">Group</label>
        <select id="groupId" name="groupId" required>
            <option value="">Select a Group</option>
        </select>
    </div>
    <div>
        <label for="teamId">Team ID</label>
        <input type="text" id="teamId" value="<?= htmlspecialchars($teamId) ?>" disabled>
    </div>
    <div>
        <label for="teamName">Team Name</label>
        <input type="text" id="teamName" name="teamName" value="<?= htmlspecialchars($team['teamName'] ?? '') ?>" required>
    </div>
    <div>
        <label for="captainName">Captain Name</label>
        <input type="text" id="captainName" name="captainName" value="<?= htmlspecialchars($team['captainName'] ?? '') ?>">
    </div>
    <div>
        <label for="captainNumber">Captain Number</label>
        <input type="text" id="captainNumber" name="captainNumber" value="<?= htmlspecialchars($team['captainNumber'] ?? '') ?>">
    </div>
    <div>
        <label for="city">City</label>
        <input type="text" id="city" name="city" value="<?= htmlspecialchars($team['city'] ?? '') ?>">
    </div>
    <div>
        <label for="groupName">Group Name</label>
        <input type="text" id="groupName" name="groupName" value="<?= htmlspecialchars($team['groupName'] ?? '') ?>">
    </div>
    <button type="submit">Update Team</button>
</form>
<?php else: ?>
<p>Team not found.</p>
<?php endif; ?>
<br>
<a href="index.php">Back to Teams List</a>

<script>
    const groupsData = <?= json_encode($all_groups ?? []) ?>;
    const tournamentSelect = document.getElementById('tournamentId');
    const groupSelect = document.getElementById('groupId');
    const currentGroupId = '<?= $team['groupId'] ?? '' ?>';

    function populateGroups() {
        const selectedTournamentId = tournamentSelect.value;
        // Clear existing options
        groupSelect.innerHTML = '<option value="">Select a Group</option>';

        if (selectedTournamentId && groupsData[selectedTournamentId]) {
            const groups = groupsData[selectedTournamentId];
            for (const groupId in groups) {
                if (groups.hasOwnProperty(groupId)) {
                    const option = document.createElement('option');
                    option.value = groupId;
                    option.textContent = groups[groupId].groupName;
                    if (groupId === currentGroupId) {
                        option.selected = true;
                    }
                    groupSelect.appendChild(option);
                }
            }
        }
    }

    // Initial population
    populateGroups();

    tournamentSelect.addEventListener('change', populateGroups);
</script>

<?php include __DIR__ . '/../footer.php'; ?>
