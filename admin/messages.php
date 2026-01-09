<?php
/**
 * Admin Messages Management
 * List and delete customer messages
 */

require_once __DIR__ . '/../includes/functions.php';
checkAuth('admin');

require_once __DIR__ . '/../config/db.php';

$conn = getDBConnection();
$message = '';
$error = '';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = 'Message deleted successfully!';
    } else {
        $error = 'Failed to delete message.';
    }
    $stmt->close();
}

// Get all messages with place names
$messages = [];
$query = "SELECT m.*, r.name as place_name FROM messages m 
          JOIN restaurants r ON m.place_id = r.id 
          ORDER BY m.created_at DESC";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$conn->close();

$page_title = 'Manage Messages';
include __DIR__ . '/../includes/header.php';
?>

<h1>Customer Messages</h1>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if (empty($messages)): ?>
    <p class="text-muted">No messages yet.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Place</th>
                    <th>Sender Name</th>
                    <th>Sender Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?php echo $msg['id']; ?></td>
                        <td><?php echo htmlspecialchars($msg['place_name']); ?></td>
                        <td><?php echo htmlspecialchars($msg['sender_name']); ?></td>
                        <td><?php echo htmlspecialchars($msg['sender_email']); ?></td>
                        <td><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . '...'; ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($msg['created_at'])); ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#messageModal<?php echo $msg['id']; ?>">View</button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    
                    <!-- Modal for full message -->
                    <div class="modal fade" id="messageModal<?php echo $msg['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Message from <?php echo htmlspecialchars($msg['sender_name']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Place:</strong> <?php echo htmlspecialchars($msg['place_name']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($msg['sender_email']); ?></p>
                                    <p><strong>Date:</strong> <?php echo date('Y-m-d H:i', strtotime($msg['created_at'])); ?></p>
                                    <hr>
                                    <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

