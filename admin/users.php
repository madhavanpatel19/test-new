<?php
require __DIR__ . '/firebase_init.php';

// Fetch users
$users = [];
$error = null;
$message = '';
$searchTerm = trim($_GET['q'] ?? '');

// Handle inline edit submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mobile'])) {
    $userId = trim($_POST['mobile']);
    $userData = [
        'name'        => trim($_POST['name'] ?? ''),
        'city'        => trim($_POST['city'] ?? ''),
        'dob'         => trim($_POST['dob'] ?? ''),
        'gender'      => trim($_POST['gender'] ?? ''),
        'playingHand' => trim($_POST['playingHand'] ?? ''),
        'photoUrl'    => trim($_POST['photoUrl'] ?? ''),
        'updatedAt'   => (new DateTime())->format('Y-m-d H:i:s'),
    ];

    try {
        $database->getReference('users/' . $userId)->update($userData);
        $message = 'User updated successfully.';
    } catch (Exception $e) {
        $error = "Error updating user: " . $e->getMessage();
    }
}

try {
    $reference = $database->getReference('users');
    $snapshot = $reference->getSnapshot();
    $users = $snapshot->getValue();
} catch (Exception $e) {
    $error = "Error fetching users: " . $e->getMessage();
}

include __DIR__ . '/header.php';
?>

<div class="page-header">
    <h1>Users</h1>
    <form method="GET" class="search-form">
        <input type="text" name="q" placeholder="Search by name" value="<?= htmlspecialchars($searchTerm) ?>">
        <button type="submit">Search</button>
    </form>
</div>
<!-- <a href="create.php" class="button">Add New User</a> -->

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($message): ?>
    <p class="success"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php
// Apply search filter by name (case-insensitive)
if ($searchTerm !== '' && is_array($users)) {
    $lower = mb_strtolower($searchTerm);
    $users = array_filter($users, function ($user) use ($lower) {
        $name = isset($user['name']) ? mb_strtolower($user['name']) : '';
        return strpos($name, $lower) !== false;
    });
}
?>

<?php if (empty($users)): ?>
    <p>No users found in the database.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Mobile Number</th>
                <th>City</th>
                <th>DOB</th>
                <th>Gender</th>
                <th>Playing Hand</th>
                <th>Photo URL</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $mobileNumber => $userData): ?>
                <tr>
                    <td><?= htmlspecialchars($userData['name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($mobileNumber) ?></td>
                    <td><?= htmlspecialchars($userData['city'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($userData['dob'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($userData['gender'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($userData['playingHand'] ?? 'N/A') ?></td>
                    <td>
                        <?php if (!empty($userData['photoUrl'])): ?>
                            <a href="<?= htmlspecialchars($userData['photoUrl']) ?>" target="_blank">View Photo</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($userData['updatedAt'] ?? 'N/A') ?></td>
                    <td>
                        <button type="button"
                            class="edit-btn"
                            data-mobile="<?= htmlspecialchars($mobileNumber) ?>"
                            data-name="<?= htmlspecialchars($userData['name'] ?? '') ?>"
                            data-city="<?= htmlspecialchars($userData['city'] ?? '') ?>"
                            data-dob="<?= htmlspecialchars($userData['dob'] ?? '') ?>"
                            data-gender="<?= htmlspecialchars($userData['gender'] ?? '') ?>"
                            data-playinghand="<?= htmlspecialchars($userData['playingHand'] ?? '') ?>"
                            data-photourl="<?= htmlspecialchars($userData['photoUrl'] ?? '') ?>"
                        >Edit</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- Edit Modal -->
<div id="editModal" class="modal hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit User</h2>
            <button type="button" class="close-btn" id="closeModal">&times;</button>
        </div>
        <form id="editForm" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="mobile">Mobile Number</label>
                    <input type="text" id="mobile" name="mobile" readonly>
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city">
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob">
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <input type="text" id="gender" name="gender">
                </div>
                <div class="form-group">
                    <label for="playingHand">Playing Hand</label>
                    <input type="text" id="playingHand" name="playingHand">
                </div>
                <div class="form-group">
                    <label for="photoUrl">Photo URL</label>
                    <input type="text" id="photoUrl" name="photoUrl" placeholder="https://example.com/photo.jpg">
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="secondary" id="cancelModal">Cancel</button>
                <button type="submit" class="primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal.hidden { display: none; }
.modal {
    position: fixed; inset: 0; background: rgba(0,0,0,0.45);
    display: flex; align-items: center; justify-content: center; z-index: 999;
}
.modal-content {
    background: #fff; padding: 20px; border-radius: 8px;
    width: 90%; max-width: 640px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.close-btn { background: none; border: none; font-size: 24px; cursor: pointer; }
.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; }
.form-group { display: flex; flex-direction: column; }
.form-group label { margin-bottom: 4px; font-weight: 600; }
.form-group input { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
.modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 16px; }
.modal-actions .secondary { background:rgb(184, 43, 43); border: 1px solid #ccc; padding: 8px 12px; border-radius: 4px; cursor: pointer; }
.modal-actions .primary { background: #007bff; color: #fff; border: none; padding: 8px 14px; border-radius: 4px; cursor: pointer; }
.edit-btn { padding: 6px 10px; border: none; background: #007bff; color: #fff; border-radius: 4px; cursor: pointer; }
.success { color: #2e7d32; }
.error { color: #c62828; }
.page-header { display:flex; flex-direction: row; align-items: center; gap: 12px; flex-wrap: wrap; padding: 8px 0; }
.page-header h1 { flex: 1; text-align: center; margin: 0; margin-left: 327px}
.page-header .search-form { margin-left: auto; align-items: flex-start }
.search-form { display: flex; flex-direction: row; align-items: center; gap: 8px; }
.search-form input { padding: 6px 8px; border: 1px solid #ccc; border-radius: 4px; }
.search-form button { padding: 6px 10px; border: none; background: #007bff; color: #fff; border-radius: 4px; cursor: pointer;  }
</style>

<script>
(function() {
    const modal = document.getElementById('editModal');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelModalBtn = document.getElementById('cancelModal');
    const editForm = document.getElementById('editForm');

    function openModal(data) {
        editForm.mobile.value = data.mobile || '';
        editForm.name.value = data.name || '';
        editForm.city.value = data.city || '';
        editForm.dob.value = data.dob || '';
        editForm.gender.value = data.gender || '';
        editForm.playingHand.value = data.playingHand || '';
        editForm.photoUrl.value = data.photoUrl || '';
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            openModal({
                mobile: btn.dataset.mobile,
                name: btn.dataset.name,
                city: btn.dataset.city,
                dob: btn.dataset.dob,
                gender: btn.dataset.gender,
                playingHand: btn.dataset.playinghand,
                photoUrl: btn.dataset.photourl
            });
        });
    });

    closeModalBtn.addEventListener('click', closeModal);
    cancelModalBtn.addEventListener('click', closeModal);

    // Close when clicking outside content
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
})();
</script>

<?php include __DIR__ . '/footer.php'; ?>
