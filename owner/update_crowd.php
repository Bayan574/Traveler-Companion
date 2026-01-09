<?php
/**
 * Update Crowd Count
 * Owner can manually update the current crowd count for their place
 */

require_once __DIR__ . '/../includes/functions.php';
checkAuth('owner');

require_once __DIR__ . '/../config/db.php';

$conn = getDBConnection();
$owner_id = $_SESSION['user_id'];
$place_id = $_SESSION['place_id'];
$message = '';
$error = '';

// Verify owner owns this place
$stmt = $conn->prepare("SELECT place_id FROM owners WHERE id = ?");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $owner = $result->fetch_assoc();
    $place_id = $owner['place_id'];
} else {
    $conn->close();
    header('Location: /owner/dashboard.php?error=unauthorized');
    exit();
}
$stmt->close();

// Get current crowd count
$stmt = $conn->prepare("SELECT crowd_count FROM crowd_status WHERE place_id = ?");
$stmt->bind_param("i", $place_id);
$stmt->execute();
$result = $stmt->get_result();
$current_count = 0;
if ($result->num_rows > 0) {
    $current_count = (int)$result->fetch_assoc()['crowd_count'];
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crowd_count = isset($_POST['crowd_count']) ? (int)$_POST['crowd_count'] : 0;
    
    if ($crowd_count < 0) {
        $error = 'Crowd count cannot be negative.';
    } else {
        // Insert or update crowd_status
        $stmt = $conn->prepare("INSERT INTO crowd_status (place_id, crowd_count) VALUES (?, ?) 
                               ON DUPLICATE KEY UPDATE crowd_count = ?");
        $stmt->bind_param("iii", $place_id, $crowd_count, $crowd_count);
        
        if ($stmt->execute()) {
            $message = 'Crowd count updated successfully!';
            $current_count = $crowd_count;
        } else {
            $error = 'Failed to update crowd count.';
        }
        $stmt->close();
    }
}

$conn->close();

$page_title = 'Update Crowd Count';
include __DIR__ . '/../includes/header.php';
?>

<h1>Update Crowd Count</h1>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <p><strong>Current Crowd Count:</strong> 
                    <span class="badge bg-<?php 
                        if ($current_count <= 8) echo 'success';
                        elseif ($current_count <= 20) echo 'warning';
                        else echo 'danger';
                    ?> fs-6">
                        <?php echo $current_count; ?> people
                    </span>
                </p>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="crowd_count" class="form-label">New Crowd Count</label>
                        <input type="number" class="form-control" id="crowd_count" name="crowd_count" 
                               value="<?php echo $current_count; ?>" min="0" required>
                        <small class="form-text text-muted">
                            Status: 0-8 = Light (Green), 9-20 = Moderate (Orange), 21+ = Busy (Red)
                        </small>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Count</button>
                    <a href="/owner/dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

