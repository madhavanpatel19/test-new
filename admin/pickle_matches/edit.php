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
$matchId = $_GET['id'] ?? null;
$match = null;

if (!$matchId) {
    die("Match ID not provided.");
}

// Fetch match data
try {
    $match = $database->getReference('pickle_matches/' . $matchId)->getSnapshot()->getValue();
} catch (Exception $e) {
    die("Error fetching match: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matchData = [
        'tournamentId' => $_POST['tournamentId'],
        'name' => $_POST['name'],
        'mobile' => $_POST['mobile'],
        'city' => $_POST['city'],
        'matchType' => $_POST['matchType'],
        'schedule_date' => $_POST['schedule_date'],
        'schedule_time' => $_POST['schedule_time'],
        'schedule_location' => $_POST['schedule_location'],
        'matchStatus' => [
            'matchCompleted' => isset($_POST['matchCompleted']),
            'winner' => $_POST['winner']
        ]
    ];

    try {
        $database->getReference('pickle_matches/' . $matchId)->update($matchData);
        $message = "Match updated successfully!";
        // Refresh match data
        $match = $database->getReference('pickle_matches/' . $matchId)->getSnapshot()->getValue();
    } catch (Exception $e) {
        $message = "Error updating match: " . $e->getMessage();
    }
}
include __DIR__ . '/../header.php';
?>

<h1>Edit Pickle Match</h1>
<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>
 <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($match): ?>
<form action="edit.php?id=<?= htmlspecialchars($matchId) ?>" method="POST">
    <div>
        <label for="matchId">Match ID</label>
        <input type="text" id="matchId" value="<?= htmlspecialchars($matchId) ?>" disabled>
    </div>
    <div>
        <label for="tournamentId">Tournament</label>
        <select id="tournamentId" name="tournamentId" required>
            <option value="">Select a Tournament</option>
            <?php if (!empty($tournaments)): ?>
                <?php foreach ($tournaments as $id => $tournament): ?>
                    <option value="<?= htmlspecialchars($id) ?>" <?= ($id === ($match['tournamentId'] ?? '')) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tournament['name']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <div>
        <label for="name">Creator Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($match['name'] ?? '') ?>">
    </div>
    <div>
        <label for="mobile">Creator Mobile</label>
        <input type="text" id="mobile" name="mobile" value="<?= htmlspecialchars($match['mobile'] ?? '') ?>">
    </div>
    <div>
        <label for="city">City</label>
        <input type="text" id="city" name="city" value="<?= htmlspecialchars($match['city'] ?? '') ?>">
    </div>
    <div>
        <label for="matchType">Match Type</label>
        <input type="text" id="matchType" name="matchType" value="<?= htmlspecialchars($match['matchType'] ?? '') ?>">
    </div>
    <div>
        <label for="schedule_date">Schedule Date</label>
        <input type="date" id="schedule_date" name="schedule_date" value="<?= htmlspecialchars($match['schedule_date'] ?? '') ?>">
    </div>
    <div>
        <label for="schedule_time">Schedule Time</label>
        <input type="time" id="schedule_time" name="schedule_time" value="<?= htmlspecialchars($match['schedule_time'] ?? '') ?>">
    </div>
    <div>
        <label for="schedule_location">Schedule Location</label>
        <input type="text" id="schedule_location" name="schedule_location" value="<?= htmlspecialchars($match['schedule_location'] ?? '') ?>">
    </div>
    <div>
        <label for="matchCompleted">Match Completed</label>
        <input type="checkbox" id="matchCompleted" name="matchCompleted" <?= ($match['matchStatus']['matchCompleted'] ?? false) ? 'checked' : '' ?>>
    </div>
    <div>
        <label for="winner">Winner</label>
        <input type="text" id="winner" name="winner" value="<?= htmlspecialchars($match['matchStatus']['winner'] ?? '') ?>">
    </div>
    <button type="submit">Update Match</button>
</form>
<?php else: ?>
<p>Match not found.</p>
<?php endif; ?>
<br>
<a href="index.php">Back to Matches List</a>

<?php include __DIR__ . '/../footer.php'; ?>
