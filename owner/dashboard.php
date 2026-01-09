<?php
/**
 * Owner Dashboard
 * Shows owner's place information and quick actions
 */

require_once __DIR__ . '/../includes/functions.php';
checkAuth('owner');

require_once __DIR__ . '/../config/db.php';

$conn = getDBConnection();

// Get owner's place information
$owner_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT o.place_id, r.name, r.type, r.location_in_airport, r.is_visible,
                               COALESCE(cs.crowd_count, 0) as crowd_count
                        FROM owners o
                        JOIN restaurants r ON o.place_id = r.id
                        LEFT JOIN crowd_status cs ON r.id = cs.place_id
                        WHERE o.id = ?");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();

// Auto-update stale crowd count before displaying
if ($result->num_rows > 0) {
    $temp_place = $result->fetch_assoc();
    $place_id = $temp_place['place_id'];
    autoUpdateStaleCrowd($place_id);
    // Re-execute query to get updated count
    $stmt->close();
    $stmt = $conn->prepare("SELECT o.place_id, r.name, r.type, r.location_in_airport, r.is_visible,
                                   COALESCE(cs.crowd_count, 0) as crowd_count
                            FROM owners o
                            JOIN restaurants r ON o.place_id = r.id
                            LEFT JOIN crowd_status cs ON r.id = cs.place_id
                            WHERE o.id = ?");
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

if ($result->num_rows === 0) {
    $conn->close();
    header('Location: /auth/login.php?error=no_place');
    exit();
}

$place = $result->fetch_assoc();
$stmt->close();

// Get menu item count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM menu_items WHERE place_id = ?");
$stmt->bind_param("i", $place['place_id']);
$stmt->execute();
$menu_count = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

// Get unread message count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM messages WHERE place_id = ?");
$stmt->bind_param("i", $place['place_id']);
$stmt->execute();
$message_count = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

$conn->close();

// Determine crowd status
$count = (int)$place['crowd_count'];
if ($count <= 8) {
    $status = 'Light';
    $color = 'success';
} elseif ($count <= 20) {
    $status = 'Moderate';
    $color = 'warning';
} else {
    $status = 'Busy';
    $color = 'danger';
}

$page_title = 'Owner Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<h1>Welcome, <?php echo htmlspecialchars($place['name']); ?></h1>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Your Place: <?php echo htmlspecialchars($place['name']); ?></h5>
            </div>
            <div class="card-body">
                <p><strong>Type:</strong> <?php echo ucfirst($place['type']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($place['location_in_airport'] ?? 'N/A'); ?></p>
                <p><strong>Visibility Status:</strong> 
                    <?php if (isset($place['is_visible']) && $place['is_visible'] == 1): ?>
                        <span class="badge bg-success">Visible to Public</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Hidden from Public</span>
                        <small class="text-muted d-block mt-1">Your place is currently hidden from public pages. Contact admin to make it visible.</small>
                    <?php endif; ?>
                </p>
                <p><strong>Current Crowd Level:</strong> 
                    <span class="badge bg-<?php echo $color; ?> fs-6">
                        <?php echo $status; ?> (<?php echo $count; ?> people)
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Menu Items</h5>
                <h2><?php echo $menu_count; ?></h2>
                <a href="/owner/menu_edit.php" class="btn btn-primary">Manage Menu</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Messages</h5>
                <h2><?php echo $message_count; ?></h2>
                <a href="/owner/messages.php" class="btn btn-primary">View Messages</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Update Crowd</h5>
                <h2><?php echo $count; ?></h2>
                <a href="/owner/update_crowd.php" class="btn btn-primary">Update Now</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

