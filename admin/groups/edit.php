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

$message = '';
$tournamentId = $_GET['tournament_id'] ?? null;
$groupId = $_GET['group_id'] ?? null;
$group = null;

if (!$tournamentId || !$groupId) {
    die("Tournament ID or Group ID not provided.");
}

// Fetch group data
try {
    $group = $database->getReference('groups/' . $tournamentId . '/' . $groupId)->getSnapshot()->getValue();
} catch (Exception $e) {
    die("Error fetching group: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTournamentId = $_POST['tournamentId'];
    $groupData = [
        'groupName' => $_POST['groupName'],
        'tournamentId' => $newTournamentId
    ];

    try {
        // If the tournament ID has changed, we need to move the data
        if ($newTournamentId !== $tournamentId) {
            $database->getReference('groups/' . $newTournamentId . '/' . $groupId)->set($groupData);
            $database->getReference('groups/' . $tournamentId . '/' . $groupId)->remove();
        } else {
            $database->getReference('groups/' . $tournamentId . '/' . $groupId)->update($groupData);
        }

        $message = "Group updated successfully!";
        // Refresh group data
        $tournamentId = $newTournamentId; // Update the tournamentId to the new one
        $group = $database->getReference('groups/' . $tournamentId . '/' . $groupId)->getSnapshot()->getValue();
    } catch (Exception $e) {
        $message = "Error updating group: " . $e->getMessage();
    }
}
include __DIR__ . '/../header.php';
?>

<h1>Edit Group</h1>
<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>
 <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($group): ?>
<form action="edit.php?tournament_id=<?= htmlspecialchars($tournamentId) ?>&group_id=<?= htmlspecialchars($groupId) ?>" method="POST">
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
        <label for="groupId">Group ID</label>
        <input type="text" id="groupId" value="<?= htmlspecialchars($groupId) ?>" disabled>
    </div>
    <div>
        <label for="groupName">Group Name</label>
        <input type="text" id="groupName" name="groupName" value="<?= htmlspecialchars($group['groupName'] ?? '') ?>" required>
    </div>
    <button type="submit">Update Group</button>
</form>
<?php else: ?>
<p>Group not found.</p>
<?php endif; ?>
<br>
<a href="index.php">Back to Groups List</a>

<?php include __DIR__ . '/../footer.php'; ?>
