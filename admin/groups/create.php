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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentId = $_POST['tournamentId'];
    $groupId = $_POST['groupId'];
    $groupData = [
        'groupName' => $_POST['groupName'],
        'tournamentId' => $tournamentId,
        'createdAt' => (new DateTime())->format('Y-m-d H:i:s')
    ];

    try {
        $database->getReference('groups/' . $tournamentId . '/' . $groupId)->set($groupData);
        $message = "Group created successfully!";
    } catch (Exception $e) {
        $message = "Error creating group: " . $e->getMessage();
    }
}
include __DIR__ . '/../header.php';
?>

<h1>Add New Group</h1>
<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>
 <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form action="create.php" method="POST">
    <div>
        <label for="tournamentId">Tournament</label>
        <select id="tournamentId" name="tournamentId" required>
            <option value="">Select a Tournament</option>
            <?php if (!empty($tournaments)): ?>
                <?php foreach ($tournaments as $id => $tournament): ?>
                    <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($tournament['name']) ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <div>
        <label for="groupId">Group ID</label>
        <input type="text" id="groupId" name="groupId" required>
    </div>
    <div>
        <label for="groupName">Group Name</label>
        <input type="text" id="groupName" name="groupName" required>
    </div>
    <button type="submit">Add Group</button>
</form>
<br>
<a href="index.php">Back to Groups List</a>

<?php include __DIR__ . '/../footer.php'; ?>
