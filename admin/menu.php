<?php
/**
 * Admin Menu Items CRUD
 * Single file handling list, add, edit, and delete operations for menu items
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
            $place_id = (int)($_POST['place_id'] ?? 0);
            $item_name = trim($_POST['item_name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $img = trim($_POST['img'] ?? '');
            
            $item_name = strip_tags($item_name);
            $img = strip_tags($img);
            
            if ($place_id <= 0 || empty($item_name) || $price <= 0) {
                $error = 'Please fill in all required fields.';
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
                    $stmt = $conn->prepare("UPDATE menu_items SET place_id = ?, item_name = ?, price = ?, img = ? WHERE id = ?");
                    $stmt->bind_param("isdsi", $place_id, $item_name, $price, $img, $id);
                    
                    if ($stmt->execute()) {
                        $message = 'Menu item updated successfully!';
                        $action = 'list';
                    } else {
                        $error = 'Failed to update menu item.';
                    }
                }
                $stmt->close();
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $message = 'Menu item deleted successfully!';
            } else {
                $error = 'Failed to delete menu item.';
            }
            $stmt->close();
        }
    }
}

// Get menu items with place names
$menu_items = [];
$query = "SELECT mi.*, r.name as place_name FROM menu_items mi 
          JOIN restaurants r ON mi.place_id = r.id 
          ORDER BY r.name, mi.item_name";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $menu_items[] = $row;
}

// Get all places for dropdown
$places = [];
$query = "SELECT id, name, type FROM restaurants ORDER BY name";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $places[] = $row;
}

// Get menu item for editing
$edit_item = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_item = $result->fetch_assoc();
    } else {
        $action = 'list';
        $error = 'Menu item not found.';
    }
    $stmt->close();
}

$conn->close();

$page_title = 'Manage Menu Items';
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
                    <th>Place</th>
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
                        <td><?php echo htmlspecialchars($item['place_name']); ?></td>
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
    <form method="POST" action="">
        <input type="hidden" name="action" value="<?php echo $action; ?>">
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
        <?php endif; ?>
        
        <div class="mb-3">
            <label for="place_id" class="form-label">Place *</label>
            <select class="form-control" id="place_id" name="place_id" required>
                <option value="">-- Select Place --</option>
                <?php foreach ($places as $place): ?>
                    <option value="<?php echo $place['id']; ?>" 
                            <?php echo ($edit_item['place_id'] ?? '') == $place['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($place['name'] . ' (' . $place['type'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
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
            <label for="img" class="form-label">Image Path (optional)</label>
            <input type="text" class="form-control" id="img" name="img" 
                   value="<?php echo htmlspecialchars($edit_item['img'] ?? ''); ?>" 
                   placeholder="menu_items/example.jpg">
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $action === 'add' ? 'Add Menu Item' : 'Update Menu Item'; ?></button>
        <a href="?action=list" class="btn btn-secondary">Cancel</a>
    </form>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

