<?php
/**
 * Admin Owners CRUD
 * Single file handling list, add, edit, and delete operations for owners
 */

require_once __DIR__ . '/../includes/functions.php';
checkAuth('admin');

require_once __DIR__ . '/../config/db.php';

$conn = getDBConnection();
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add' || $action === 'edit') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $place_id = (int)($_POST['place_id'] ?? 0);
            
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            
            if (empty($email) || $place_id <= 0 || ($action === 'add' && empty($password))) {
                $error = 'Please fill in all required fields.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email address.';
            } else {
                if ($action === 'add') {
                    // Check if email already exists
                    $stmt = $conn->prepare("SELECT id FROM owners WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    if ($stmt->get_result()->num_rows > 0) {
                        $error = 'Email already exists.';
                    } else {
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("INSERT INTO owners (email, password, place_id) VALUES (?, ?, ?)");
                        $stmt->bind_param("ssi", $email, $password_hash, $place_id);
                        
                        if ($stmt->execute()) {
                            $message = 'Owner added successfully!';
                            $action = 'list';
                        } else {
                            $error = 'Failed to add owner.';
                        }
                    }
                } else {
                    $id = (int)$_POST['id'];
                    if (!empty($password)) {
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("UPDATE owners SET email = ?, password = ?, place_id = ? WHERE id = ?");
                        $stmt->bind_param("ssii", $email, $password_hash, $place_id, $id);
                    } else {
                        $stmt = $conn->prepare("UPDATE owners SET email = ?, place_id = ? WHERE id = ?");
                        $stmt->bind_param("sii", $email, $place_id, $id);
                    }
                    
                    if ($stmt->execute()) {
                        $message = 'Owner updated successfully!';
                        $action = 'list';
                    } else {
                        $error = 'Failed to update owner.';
                    }
                }
                if (isset($stmt)) $stmt->close();
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM owners WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $message = 'Owner deleted successfully!';
            } else {
                $error = 'Failed to delete owner.';
            }
            $stmt->close();
        }
    }
}

// Get owners with place names
$owners = [];
$query = "SELECT o.*, r.name as place_name FROM owners o 
          JOIN restaurants r ON o.place_id = r.id 
          ORDER BY r.name";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $owners[] = $row;
}

// Get all places for dropdown
$places = [];
$query = "SELECT id, name, type FROM restaurants ORDER BY name";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $places[] = $row;
}

// Get owner for editing
$edit_owner = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM owners WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_owner = $result->fetch_assoc();
    } else {
        $action = 'list';
        $error = 'Owner not found.';
    }
    $stmt->close();
}

$conn->close();

$page_title = 'Manage Owners';
include __DIR__ . '/../includes/header.php';
?>

<h1>Manage Owners</h1>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link <?php echo $action === 'list' ? 'active' : ''; ?>" href="?action=list">List</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $action === 'add' ? 'active' : ''; ?>" href="?action=add">Add New</a>
    </li>
</ul>

<?php if ($action === 'list'): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Place</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($owners as $owner): ?>
                    <tr>
                        <td><?php echo $owner['id']; ?></td>
                        <td><?php echo htmlspecialchars($owner['email']); ?></td>
                        <td><?php echo htmlspecialchars($owner['place_name']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($owner['created_at'])); ?></td>
                        <td>
                            <a href="?action=edit&id=<?php echo $owner['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this owner?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $owner['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): ?>
    <form method="POST" action="">
        <input type="hidden" name="action" value="<?php echo $action; ?>">
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id" value="<?php echo $edit_owner['id']; ?>">
        <?php endif; ?>
        
        <div class="mb-3">
            <label for="email" class="form-label">Email *</label>
            <input type="email" class="form-control" id="email" name="email" 
                   value="<?php echo htmlspecialchars($edit_owner['email'] ?? ''); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">Password <?php echo $action === 'edit' ? '(leave blank to keep current)' : '*'; ?></label>
            <input type="password" class="form-control" id="password" name="password" 
                   <?php echo $action === 'add' ? 'required' : ''; ?>>
        </div>
        
        <div class="mb-3">
            <label for="place_id" class="form-label">Place *</label>
            <select class="form-control" id="place_id" name="place_id" required>
                <option value="">-- Select Place --</option>
                <?php foreach ($places as $place): ?>
                    <option value="<?php echo $place['id']; ?>" 
                            <?php echo ($edit_owner['place_id'] ?? '') == $place['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($place['name'] . ' (' . $place['type'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $action === 'add' ? 'Add Owner' : 'Update Owner'; ?></button>
        <a href="?action=list" class="btn btn-secondary">Cancel</a>
    </form>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

