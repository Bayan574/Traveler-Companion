<?php
/**
 * Place Details Page
 * Shows place information, menu items, and contact form
 */

require_once __DIR__ . '/../config/db.php';

$place_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($place_id <= 0) {
    header('Location: /');
    exit();
}

$conn = getDBConnection();

// Auto-update stale crowd count before fetching
require_once __DIR__ . '/../includes/functions.php';
autoUpdateStaleCrowd($place_id);

// Get place details (only if visible)
$stmt = $conn->prepare("SELECT r.id, r.name, r.type, r.location_in_airport, r.description, r.logo,
                               COALESCE(cs.crowd_count, 0) as crowd_count
                        FROM restaurants r
                        LEFT JOIN crowd_status cs ON r.id = cs.place_id
                        WHERE r.id = ? AND r.is_visible = 1");
$stmt->bind_param("i", $place_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $conn->close();
    header('Location: /');
    exit();
}

$place = $result->fetch_assoc();
$stmt->close();

// Get menu items
$stmt = $conn->prepare("SELECT id, item_name, price, img FROM menu_items WHERE place_id = ? ORDER BY item_name");
$stmt->bind_param("i", $place_id);
$stmt->execute();
$menu_result = $stmt->get_result();

$menu_items = [];
while ($row = $menu_result->fetch_assoc()) {
    $menu_items[] = $row;
}
$stmt->close();
$conn->close();

$page_title = $place['name'] . ' - Traveler Companion';
include __DIR__ . '/../includes/header.php';

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
?>

<div class="row">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-3">
            <?php if (!empty($place['logo'])): ?>
                <img src="/uploads/<?php echo htmlspecialchars($place['logo']); ?>" 
                     alt="<?php echo htmlspecialchars($place['name']); ?> logo" 
                     style="max-height: 100px; max-width: 200px; object-fit: contain; margin-right: 20px;"
                     onerror="this.style.display='none'">
            <?php endif; ?>
            <div>
                <h1 class="mb-0"><?php echo htmlspecialchars($place['name']); ?></h1>
                <p class="text-muted mb-0"><?php echo htmlspecialchars($place['location_in_airport'] ?? 'Location TBD'); ?></p>
            </div>
        </div>
        
        <div class="mb-3">
            <span class="badge bg-<?php echo $color; ?> fs-6">
                <?php echo $status; ?> (<?php echo $count; ?> people)
            </span>
        </div>
        
        <?php if ($place['description']): ?>
            <p><?php echo nl2br(htmlspecialchars($place['description'])); ?></p>
        <?php endif; ?>
        
        <h2 class="section-title mt-4">Menu</h2>
        <?php if (empty($menu_items)): ?>
            <p class="text-muted">No menu items available yet.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($menu_items as $item): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <?php if ($item['img']): ?>
                                    <img src="/uploads/<?php echo htmlspecialchars($item['img']); ?>"
                                         class="card-img-top mb-2" alt="<?php echo htmlspecialchars($item['item_name']); ?>" 
                                         style="max-height: 150px; object-fit: cover;"
                                         onerror="this.style.display='none'">
                                <?php endif; ?>
                                <h5 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h5>
                                <p class="card-text">
                                    <strong><?php echo number_format($item['price'], 2); ?> SAR</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Contact This Place</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/public_user/contact.php">
                    <input type="hidden" name="place_id" value="<?php echo $place_id; ?>">
                    <div class="mb-3">
                        <label for="sender_name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="sender_name" name="sender_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="sender_email" class="form-label">Your Email</label>
                        <input type="email" class="form-control" id="sender_email" name="sender_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

