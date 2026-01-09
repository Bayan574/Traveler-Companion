<?php
/**
 * Contact Form Handler
 * Processes contact form submissions and saves to messages table
 */

require_once __DIR__ . '/../config/db.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $place_id = isset($_POST['place_id']) ? (int)$_POST['place_id'] : 0;
    $sender_name = trim($_POST['sender_name'] ?? '');
    $sender_email = trim($_POST['sender_email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if ($place_id <= 0 || empty($sender_name) || empty($sender_email) || empty($message)) {
        $error = 'Please fill in all fields.';
    } else {
        // Sanitize inputs
        $sender_name = strip_tags($sender_name);
        $sender_email = filter_var($sender_email, FILTER_SANITIZE_EMAIL);
        $message = strip_tags($message);
        
        if (!filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } else {
            $conn = getDBConnection();
            
            // Verify place exists
            $stmt = $conn->prepare("SELECT id FROM restaurants WHERE id = ?");
            $stmt->bind_param("i", $place_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Insert message
                $stmt = $conn->prepare("INSERT INTO messages (place_id, sender_name, sender_email, message) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $place_id, $sender_name, $sender_email, $message);
                
                if ($stmt->execute()) {
                    $success = true;
                } else {
                    $error = 'Failed to send message. Please try again.';
                }
            } else {
                $error = 'Invalid place selected.';
            }
            
            $stmt->close();
            $conn->close();
        }
    }
}

$page_title = $success ? 'Message Sent' : 'Contact';
include __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <h5>Message Sent Successfully!</h5>
                        <p>Your message has been sent to the establishment. They will contact you soon.</p>
                        <a href="/" class="btn btn-primary">Return Home</a>
                    </div>
                <?php else: ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <p>Please go back to the place page and use the contact form there.</p>
                    <a href="/" class="btn btn-primary">Return Home</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

