<?php
/**
 * Owner Menu Edit
 * Owner can CRUD menu items for their own place only
 */

require_once __DIR__ . '/../includes/functions.php';
checkAuth('owner');

require_once __DIR__ . '/../config/db.php';

$conn = getDBConnection();
$owner_id = $_SESSION['user_id'];
$place_id = $_SESSION['place_id'];
$action = $_GET['action'] ?? 'list';
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

// Handle file uploads
function handleFileUpload($file, $upload_dir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Create upload directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/jfif'];
    $file_type = $file['type'];
    if (!in_array($file_type, $allowed_types)) {
        return ['error' => 'Invalid file type. Only images (JPEG, PNG, GIF, WEBP) are allowed.'];
    }
    
    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $max_size) {
        return ['error' => 'File size too large. Maximum size is 5MB.'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('menu_', true) . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . '/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    } else {
        return ['error' => 'Failed to upload file.'];
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add' || $action === 'edit') {
            $item_name = trim($_POST['item_name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $img = trim($_POST['img'] ?? ''); // Keep existing image if no new upload
            
            $item_name = strip_tags($item_name);
            
            // Handle file upload
            $upload_dir = __DIR__ . '/../uploads';
            $upload_result = null;
            
            if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] === UPLOAD_ERR_OK) {
                $upload_result = handleFileUpload($_FILES['img_file'], $upload_dir);
                if (is_array($upload_result) && isset($upload_result['error'])) {
                    $error = $upload_result['error'];
                } elseif (is_string($upload_result)) {
                    $img = $upload_result; // Use new uploaded filename
                }
            }
            
            if (empty($item_name) || $price <= 0) {
                $error = 'Please fill in all required fields.';
            } elseif (isset($error) && strpos($error, 'file') !== false) {
                // File upload error already set
            } else {
                if ($action === 'add') {
                    $stmt = $conn->prepare("INSERT INTO menu_items (place_id, item_name, price, img) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isds", $place_id, $item_name, $price, $img);
                    
                    if ($stmt->execute()) {
                        $message = 'Menu item added successfully!';
                        $action = 'list';
                    } else {
                        $error = 'Failed to add menu item.';
                    }
                } else {
                    $id = (int)$_POST['id'];
                    // Verify item belongs to owner's place
                    $check_stmt = $conn->prepare("SELECT img FROM menu_items WHERE id = ? AND place_id = ?");
                    $check_stmt->bind_param("ii", $id, $place_id);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();
                    
                    if ($check_result->num_rows > 0) {
                        $old_item = $check_result->fetch_assoc();
                        $old_img = $old_item['img'];
                        
                        // If new image uploaded, delete old one
                        if (is_string($upload_result) && !empty($old_img) && file_exists($upload_dir . '/' . $old_img)) {
                            @unlink($upload_dir . '/' . $old_img);
                        }
                        
                        // Use new image if uploaded, otherwise keep old one
                        $final_img = is_string($upload_result) ? $upload_result : ($img ?: $old_img);
                        
                        $stmt = $conn->prepare("UPDATE menu_items SET item_name = ?, price = ?, img = ? WHERE id = ? AND place_id = ?");
                        $stmt->bind_param("sdsii", $item_name, $price, $final_img, $id, $place_id);
                        
                        if ($stmt->execute()) {
                            $message = 'Menu item updated successfully!';
                            $action = 'list';
                        } else {
                            $error = 'Failed to update menu item.';
                        }
                    } else {
                        $error = 'Unauthorized: Menu item does not belong to your place.';
                    }
                    $check_stmt->close();
                }
                if (isset($stmt)) $stmt->close();
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            // Get image filename before deleting
            $check_stmt = $conn->prepare("SELECT img FROM menu_items WHERE id = ? AND place_id = ?");
            $check_stmt->bind_param("ii", $id, $place_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $item = $check_result->fetch_assoc();
                $img_file = $item['img'];
                
                // Delete menu item
                $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ? AND place_id = ?");
                $stmt->bind_param("ii", $id, $place_id);
                
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    // Delete associated image file
                    $upload_dir = __DIR__ . '/../uploads';
                    if (!empty($img_file) && file_exists($upload_dir . '/' . $img_file)) {
                        @unlink($upload_dir . '/' . $img_file);
                    }
                    $message = 'Menu item deleted successfully!';
                } else {
                    $error = 'Failed to delete menu item.';
                }
                $stmt->close();
            } else {
                $error = 'Menu item not found or unauthorized.';
            }
            $check_stmt->close();
        }
    }
}

// Get menu items for owner's place
$menu_items = [];
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE place_id = ? ORDER BY item_name");
$stmt->bind_param("i", $place_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $menu_items[] = $row;
}
$stmt->close();

// Get menu item for editing
$edit_item = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ? AND place_id = ?");
    $stmt->bind_param("ii", $id, $place_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_item = $result->fetch_assoc();
    } else {
        $action = 'list';
        $error = 'Menu item not found or unauthorized.';
    }
    $stmt->close();
}

$conn->close();

$page_title = 'Manage Menu';
include __DIR__ . '/../includes/header.php';
?>

<h1>Manage Menu Items</h1>

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
                    <th>Item Name</th>
                    <th>Price (SAR)</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menu_items as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <?php if ($item['img']): ?>
                                <img src="/uploads/<?php echo htmlspecialchars($item['img']); ?>" 
                                     alt="Menu item" 
                                     style="max-height: 50px; max-width: 80px; object-fit: cover; border-radius: 5px;"
                                     onerror="this.style.display='none'">
                            <?php else: ?>
                                <span class="text-muted">No image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this menu item?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?php echo $action; ?>">
        <input type="hidden" name="img" value="<?php echo htmlspecialchars($edit_item['img'] ?? ''); ?>">
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
        <?php endif; ?>
        
        <div class="mb-3">
            <label for="item_name" class="form-label">Item Name *</label>
            <input type="text" class="form-control" id="item_name" name="item_name" 
                   value="<?php echo htmlspecialchars($edit_item['item_name'] ?? ''); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="price" class="form-label">Price (SAR) *</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" 
                   value="<?php echo htmlspecialchars($edit_item['price'] ?? ''); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="img_file" class="form-label">Image (optional)</label>
            <input type="file" class="form-control" id="img_file" name="img_file" 
                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/jfif">
            <small class="form-text text-muted">Max file size: 5MB. Allowed types: JPEG, PNG, GIF, WEBP</small>
            <?php if (isset($edit_item['img']) && !empty($edit_item['img'])): ?>
                <div class="mt-2">
                    <small class="text-muted">Current image:</small><br>
                    <img src="/uploads/<?php echo htmlspecialchars($edit_item['img']); ?>" 
                         alt="Current image" 
                         style="max-height: 100px; max-width: 200px; object-fit: contain; margin-top: 5px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;"
                         onerror="this.style.display='none'">
                    <br>
                    <small class="text-muted">Upload a new image to replace this one</small>
                </div>
            <?php endif; ?>
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $action === 'add' ? 'Add Menu Item' : 'Update Menu Item'; ?></button>
        <a href="?action=list" class="btn btn-secondary">Cancel</a>
    </form>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

