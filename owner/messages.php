<?php
/**
 * Owner Messages
 * Owner can view messages sent to their place
 */

require_once __DIR__ . '/../includes/functions.php';
checkAuth('owner');

require_once __DIR__ . '/../config/db.php';

$conn = getDBConnection();
$owner_id = $_SESSION['user_id'];
$place_id = $_SESSION['place_id'];

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

// Get messages for owner's place
$messages = [];
$stmt = $conn->prepare("SELECT * FROM messages WHERE place_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $place_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();
$conn->close();

$page_title = 'My Messages';
include __DIR__ . '/../includes/header.php';
?>

<h1>Customer Messages</h1>

<?php if (empty($messages)): ?>
    <p class="text-muted">No messages yet.</p>
<?php else: ?>
    <div class="row">
        <?php foreach ($messages as $msg): ?>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <strong><?php echo htmlspecialchars($msg['sender_name']); ?></strong>
                        <small class="text-muted float-end"><?php echo date('Y-m-d H:i', strtotime($msg['created_at'])); ?></small>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($msg['sender_email']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

