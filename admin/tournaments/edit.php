<?php
require __DIR__ . '/../firebase_init.php';

$message = '';
$tournamentId = $_GET['id'] ?? null;
$tournament = null;

if (!$tournamentId) {
    die("Tournament ID not provided.");
}

// Fetch tournament data
try {
    $tournament = $database->getReference('tournaments/' . $tournamentId)->getSnapshot()->getValue();
} catch (Exception $e) {
    die("Error fetching tournament: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentData = [
        'name' => $_POST['name'],
        'city' => $_POST['city'],
        'court' => $_POST['court'],
        'startDate' => $_POST['startDate'],
        'endDate' => $_POST['endDate'],
        'organizerName' => $_POST['organizerName'],
        'organizerNumber' => $_POST['organizerNumber'],
        'createdUser' => $_POST['createdUser'],
        'categories' => array_map('trim', explode(',', $_POST['categories'])),
        'formats' => array_map('trim', explode(',', $_POST['formats'])),
    ];

    try {
        $database->getReference('tournaments/' . $tournamentId)->update($tournamentData);
        $message = "Tournament updated successfully!";
        // Refresh tournament data
        $tournament = $database->getReference('tournaments/' . $tournamentId)->getSnapshot()->getValue();
    } catch (Exception $e) {
        $message = "Error updating tournament: " . $e->getMessage();
    }
}

include __DIR__ . '/../header.php';
?>

<h1>Edit Tournament</h1>
<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>
<?php if ($tournament): ?>
<form action="edit.php?id=<?= htmlspecialchars($tournamentId) ?>" method="POST">
    <div>
        <label for="tournamentId">Tournament ID</label>
        <input type="text" id="tournamentId" name="tournamentId" value="<?= htmlspecialchars($tournamentId) ?>" disabled>
    </div>
    <div>
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($tournament['name'] ?? '') ?>" required>
    </div>
    <div>
        <label for="city">City</label>
        <input type="text" id="city" name="city" value="<?= htmlspecialchars($tournament['city'] ?? '') ?>">
    </div>
    <div>
        <label for="court">Court</label>
        <input type="text" id="court" name="court" value="<?= htmlspecialchars($tournament['court'] ?? '') ?>">
    </div>
    <div>
        <label for="startDate">Start Date</label>
        <input type="date" id="startDate" name="startDate" value="<?= htmlspecialchars($tournament['startDate'] ?? '') ?>">
    </div>
    <div>
        <label for="endDate">End Date</label>
        <input type="date" id="endDate" name="endDate" value="<?= htmlspecialchars($tournament['endDate'] ?? '') ?>">
    </div>
    <div>
        <label for="organizerName">Organizer Name</label>
        <input type="text" id="organizerName" name="organizerName" value="<?= htmlspecialchars($tournament['organizerName'] ?? '') ?>">
    </div>
    <div>
        <label for="organizerNumber">Organizer Number</label>
        <input type="text" id="organizerNumber" name="organizerNumber" value="<?= htmlspecialchars($tournament['organizerNumber'] ?? '') ?>">
    </div>
    <div>
        <label for="createdUser">Created User (Mobile)</label>
        <input type="text" id="createdUser" name="createdUser" value="<?= htmlspecialchars($tournament['createdUser'] ?? '') ?>">
    </div>
    <div>
        <label for="categories">Categories (comma-separated)</label>
        <input type="text" id="categories" name="categories" value="<?= isset($tournament['categories']) ? htmlspecialchars(implode(', ', $tournament['categories'])) : '' ?>">
    </div>
    <div>
        <label for="formats">Formats (comma-separated)</label>
        <input type="text" id="formats" name="formats" value="<?= isset($tournament['formats']) ? htmlspecialchars(implode(', ', $tournament['formats'])) : '' ?>">
    </div>
    <button type="submit">Update Tournament</button>
</form>
<?php else: ?>
<p>Tournament not found.</p>
<?php endif; ?>
<br>
<a href="index.php">Back to Tournaments List</a>

<?php include __DIR__ . '/../footer.php'; ?>
