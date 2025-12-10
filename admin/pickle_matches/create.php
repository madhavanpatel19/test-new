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
    $matchId = $_POST['matchId'];
    $matchData = [
        'tournamentId' => $_POST['tournamentId'],
        'name' => $_POST['name'],
        'mobile' => $_POST['mobile'],
        'city' => $_POST['city'],
        'matchType' => $_POST['matchType'],
        'schedule_date' => $_POST['schedule_date'],
        'schedule_time' => $_POST['schedule_time'],
        'schedule_location' => $_POST['schedule_location'],
        'createdAt' => (new DateTime())->format('Y-m-d H:i:s'),
        'matchStatus' => [
            'matchCompleted' => false,
            'completedAt' => null,
            'winner' => ''
        ],
        // NOTE: The 'players' and 'gameResults' fields are complex and not included in this simplified form.
        // An advanced interface would be required to manage this nested data.
        'players' => [],
        'gameResults' => []
    ];

    try {
        $database->getReference('pickle_matches/' . $matchId)->set($matchData);
        $message = "Match created successfully!";
    } catch (Exception $e) {
        $message = "Error creating match: " . $e->getMessage();
    }
}
include __DIR__ . '/../header.php';
?>

<h1>Add New Pickle Match</h1>
<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>
<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form action="create.php" method="POST">
    <div>
        <label for="matchId">Match ID</label>
        <input type="text" id="matchId" name="matchId" required>
    </div>
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
        <label for="name">Creator Name</label>
        <input type="text" id="name" name="name">
    </div>
    <div>
        <label for="mobile">Creator Mobile</label>
        <input type="text" id="mobile" name="mobile">
    </div>
    <div>
        <label for="city">City</label>
        <input type="text" id="city" name="city">
    </div>
    <div>
        <label for="matchType">Match Type</label>
        <input type="text" id="matchType" name="matchType">
    </div>
    <div>
        <label for="schedule_date">Schedule Date</label>
        <input type="date" id="schedule_date" name="schedule_date">
    </div>
    <div>
        <label for="schedule_time">Schedule Time</label>
        <input type="time" id="schedule_time" name="schedule_time">
    </div>
    <div>
        <label for="schedule_location">Schedule Location</label>
        <input type="text" id="schedule_location" name="schedule_location">
    </div>
    <button type="submit">Add Match</button>
</form>
<br>
<a href="index.php">Back to Matches List</a>

<?php include __DIR__ . '/../footer.php'; ?>
