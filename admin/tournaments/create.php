<?php
require __DIR__ . '/../firebase_init.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentId = $_POST['tournamentId'];
    $tournamentData = [
        'name' => $_POST['name'],
        'city' => $_POST['city'],
        'court' => $_POST['court'],
        'startDate' => $_POST['startDate'],
        'endDate' => $_POST['endDate'],
        'organizerName' => $_POST['organizerName'],
        'organizerNumber' => $_POST['organizerNumber'],
        'createdUser' => $_POST['createdUser'],
        'createdAt' => (new DateTime())->format('Y-m-d H:i:s'),
        'categories' => array_map('trim', explode(',', $_POST['categories'])),
        'formats' => array_map('trim', explode(',', $_POST['formats'])),
        'bannerLocalPath' => '',
        'logoLocalPath' => ''
    ];

    try {
        $database->getReference('tournaments/' . $tournamentId)->set($tournamentData);
        $message = "Tournament created successfully!";
    } catch (Exception $e) {
        $message = "Error creating tournament: " . $e->getMessage();
    }
}
include __DIR__ . '/../header.php';
?>

<h1>Add New Tournament</h1>
<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>
<form action="create.php" method="POST">
    <div>
        <label for="tournamentId">Tournament ID</label>
        <input type="text" id="tournamentId" name="tournamentId" required>
    </div>
    <div>
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div>
        <label for="city">City</label>
        <input type="text" id="city" name="city">
    </div>
    <div>
        <label for="court">Court</label>
        <input type="text" id="court" name="court">
    </div>
    <div>
        <label for="startDate">Start Date</label>
        <input type="date" id="startDate" name="startDate">
    </div>
    <div>
        <label for="endDate">End Date</label>
        <input type="date" id="endDate" name="endDate">
    </div>
    <div>
        <label for="organizerName">Organizer Name</label>
        <input type="text" id="organizerName" name="organizerName">
    </div>
    <div>
        <label for="organizerNumber">Organizer Number</label>
        <input type="text" id="organizerNumber" name="organizerNumber">
    </div>
    <div>
        <label for="createdUser">Created User (Mobile)</label>
        <input type="text" id="createdUser" name="createdUser">
    </div>
    <div>
        <label for="categories">Categories (comma-separated)</label>
        <input type="text" id="categories" name="categories">
    </div>
    <div>
        <label for="formats">Formats (comma-separated)</label>
        <input type="text" id="formats" name="formats">
    </div>
    <button type="submit">Add Tournament</button>
</form>
<br>
<a href="index.php">Back to Tournaments List</a>

<?php include __DIR__ . '/../footer.php'; ?>
