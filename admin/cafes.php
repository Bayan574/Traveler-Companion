<?php
/**
 * Admin Cafes CRUD
 * Single file handling list, add, edit, and delete operations for cafes
 */

require_once __DIR__ . '/../includes/functions.php';
checkAuth('admin');

require_once __DIR__ . '/../config/db.php';

// Handle file uploads for logos
function handleLogoUpload($file, $upload_dir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Create upload directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    $file_type = $file['type'];
    if (!in_array($file_type, $allowed_types)) {
        return ['error' => 'Invalid file type. Only images (JPEG, PNG, GIF, WEBP, SVG) are allowed.'];
    }
    
    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $max_size) {
        return ['error' => 'File size too large. Maximum size is 5MB.'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'logo_' . uniqid('', true) . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . '/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    } else {
        return ['error' => 'Failed to upload file.'];
    }
}

$conn = getDBConnection();
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// Handle form submissions (same logic as restaurants.php but for cafes)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add' || $action === 'edit') {
            $name = trim($_POST['name'] ?? '');
            $type = 'cafe'; // Always cafe for this page
            $location = trim($_POST['location_in_airport'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $is_visible = isset($_POST['is_visible']) && $_POST['is_visible'] === '1' ? 1 : 0;
            $logo = trim($_POST['logo'] ?? ''); // Keep existing logo if no new upload
            
            // Owner account fields (optional when adding)
            $create_owner = isset($_POST['create_owner']) && $_POST['create_owner'] === '1';
            $owner_email = trim($_POST['owner_email'] ?? '');
            $owner_password = $_POST['owner_password'] ?? '';
            
            $name = strip_tags($name);
            $location = strip_tags($location);
            $description = strip_tags($description);
            
            // Handle logo file upload
            $upload_dir = __DIR__ . '/../uploads';
            $upload_result = null;
            
            if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
                $upload_result = handleLogoUpload($_FILES['logo_file'], $upload_dir);
                if (is_array($upload_result) && isset($upload_result['error'])) {
                    $error = $upload_result['error'];
                } elseif (is_string($upload_result)) {
                    $logo = $upload_result; // Use new uploaded filename
                }
            }
            
            if (empty($name)) {
                $error = 'Please fill in all required fields.';
            } elseif ($action === 'add' && $create_owner && (empty($owner_email) || empty($owner_password))) {
                $error = 'If creating owner account, email and password are required.';
            } elseif ($action === 'add' && $create_owner && !filter_var($owner_email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid owner email address.';
            } elseif (isset($error) && strpos($error, 'file') !== false) {
                // File upload error already set
            } else {
                if ($action === 'add') {
                    $stmt = $conn->prepare("INSERT INTO restaurants (name, type, location_in_airport, description, is_visible, logo) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssis", $name, $type, $location, $description, $is_visible, $logo);
                    
                    if ($stmt->execute()) {
                        $place_id = $conn->insert_id;
                        $stmt->close();
                        
                        // Create owner account if requested
                        if ($create_owner) {
                            // Check if email already exists
                            $check_stmt = $conn->prepare("SELECT id FROM owners WHERE email = ?");
                            $check_stmt->bind_param("s", $owner_email);
                            $check_stmt->execute();
                            if ($check_stmt->get_result()->num_rows > 0) {
                                $error = 'Owner email already exists. Cafe was added but owner account was not created.';
                            } else {
                                $password_hash = password_hash($owner_password, PASSWORD_DEFAULT);
                                $owner_stmt = $conn->prepare("INSERT INTO owners (email, password, place_id) VALUES (?, ?, ?)");
                                $owner_stmt->bind_param("ssi", $owner_email, $password_hash, $place_id);
                                
                                if ($owner_stmt->execute()) {
                                    $message = 'Cafe and owner account added successfully!';
                                } else {
                                    $message = 'Cafe added successfully, but failed to create owner account.';
                                }
                                $owner_stmt->close();
                            }
                            $check_stmt->close();
                        } else {
                            $message = 'Cafe added successfully!';
                        }
                        $action = 'list';
                    } else {
                        $error = 'Failed to add cafe.';
                    }
                } else {
                    $id = (int)$_POST['id'];
                    
                    // Get old logo before updating
                    $check_stmt = $conn->prepare("SELECT logo FROM restaurants WHERE id = ?");
                    $check_stmt->bind_param("i", $id);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();
                    $old_logo = '';
                    if ($check_result->num_rows > 0) {
                        $old_cafe = $check_result->fetch_assoc();
                        $old_logo = $old_cafe['logo'] ?? '';
                    }
                    $check_stmt->close();
                    
                    // If new logo uploaded, delete old one
                    if (is_string($upload_result) && !empty($old_logo) && file_exists($upload_dir . '/' . $old_logo)) {
                        @unlink($upload_dir . '/' . $old_logo);
                    }
                    
                    // Use new logo if uploaded, otherwise keep old one
                    $final_logo = is_string($upload_result) ? $upload_result : ($logo ?: $old_logo);
                    
                    $stmt = $conn->prepare("UPDATE restaurants SET name = ?, location_in_airport = ?, description = ?, is_visible = ?, logo = ? WHERE id = ? AND type = 'cafe'");
                    $stmt->bind_param("sssisi", $name, $location, $description, $is_visible, $final_logo, $id);
                    
                    if ($stmt->execute()) {
                        $message = 'Cafe updated successfully!';
                        $action = 'list';
                    } else {
                        $error = 'Failed to update cafe.';
                    }
                    $stmt->close();
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            // Get logo filename before deleting
            $check_stmt = $conn->prepare("SELECT logo FROM restaurants WHERE id = ?");
            $check_stmt->bind_param("i", $id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $logo_file = '';
            if ($check_result->num_rows > 0) {
                $cafe = $check_result->fetch_assoc();
                $logo_file = $cafe['logo'] ?? '';
            }
            $check_stmt->close();
            
            $stmt = $conn->prepare("DELETE FROM restaurants WHERE id = ? AND type = 'cafe'");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                // Delete associated logo file
                $upload_dir = __DIR__ . '/../uploads';
                if (!empty($logo_file) && file_exists($upload_dir . '/' . $logo_file)) {
                    @unlink($upload_dir . '/' . $logo_file);
                }
                $message = 'Cafe deleted successfully!';
            } else {
                $error = 'Failed to delete cafe.';
            }
            $stmt->close();
        }
    }
}

// Get cafes list
$cafes = [];
$query = "SELECT * FROM restaurants WHERE type = 'cafe' ORDER BY name";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $cafes[] = $row;
}

// Get cafe for editing
$edit_cafe = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM restaurants WHERE id = ? AND type = 'cafe'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_cafe = $result->fetch_assoc();
    } else {
        $action = 'list';
        $error = 'Cafe not found.';
    }
    $stmt->close();
}

$conn->close();

$page_title = 'Manage Cafes';
include __DIR__ . '/../includes/header.php';
?>

<h1>Manage Cafes</h1>

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
                    <th>Logo</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Visible</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cafes as $cafe): ?>
                    <tr>
                        <td><?php echo $cafe['id']; ?></td>
                        <td>
                            <?php if (!empty($cafe['logo'])): ?>
                                <img src="/uploads/<?php echo htmlspecialchars($cafe['logo']); ?>" 
                                     alt="Logo" 
                                     style="max-height: 40px; max-width: 60px; object-fit: contain; border-radius: 5px;"
                                     onerror="this.style.display='none'">
                            <?php else: ?>
                                <span class="text-muted">No logo</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($cafe['name']); ?></td>
                        <td><?php echo htmlspecialchars($cafe['location_in_airport'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(substr($cafe['description'] ?? '', 0, 50)) . '...'; ?></td>
                        <td>
                            <?php if (isset($cafe['is_visible']) && $cafe['is_visible'] == 1): ?>
                                <span class="badge bg-success">Visible</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Hidden</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?action=edit&id=<?php echo $cafe['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this cafe?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $cafe['id']; ?>">
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
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id" value="<?php echo $edit_cafe['id']; ?>">
        <?php endif; ?>
        
        <div class="mb-3">
            <label for="name" class="form-label">Cafe Name *</label>
            <input type="text" class="form-control" id="name" name="name" 
                   value="<?php echo htmlspecialchars($edit_cafe['name'] ?? ''); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="location_in_airport" class="form-label">Location in Airport</label>
            <input type="text" class="form-control" id="location_in_airport" name="location_in_airport" 
                   value="<?php echo htmlspecialchars($edit_cafe['location_in_airport'] ?? ''); ?>">
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($edit_cafe['description'] ?? ''); ?></textarea>
        </div>
        
        <div class="mb-3">
            <label for="logo_file" class="form-label">Logo (optional)</label>
            <input type="file" class="form-control" id="logo_file" name="logo_file" 
                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/svg+xml">
            <small class="form-text text-muted">Max file size: 5MB. Allowed types: JPEG, PNG, GIF, WEBP, SVG</small>
            <input type="hidden" name="logo" value="<?php echo htmlspecialchars($edit_cafe['logo'] ?? ''); ?>">
            <?php if (isset($edit_cafe['logo']) && !empty($edit_cafe['logo'])): ?>
                <div class="mt-2">
                    <small class="text-muted">Current logo:</small><br>
                    <img src="/uploads/<?php echo htmlspecialchars($edit_cafe['logo']); ?>" 
                         alt="Cafe logo" 
                         style="max-height: 100px; max-width: 200px; object-fit: contain; margin-top: 5px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;"
                         onerror="this.style.display='none'">
                    <br>
                    <small class="text-muted">Upload a new logo to replace this one</small>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_visible" name="is_visible" value="1" 
                       <?php echo (!isset($edit_cafe['is_visible']) || $edit_cafe['is_visible'] == 1) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="is_visible">
                    Visible to public
                </label>
                <small class="form-text text-muted d-block">Uncheck to hide this cafe from public pages</small>
            </div>
        </div>
        
        <?php if ($action === 'add'): ?>
        <hr class="my-4">
        <h5>Owner Account </h5>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="create_owner" name="create_owner" value="1" onchange="toggleOwnerFields()">
                <label class="form-check-label" for="create_owner">
                    Create owner account for this cafe
                </label>
            </div>
        </div>
        <div id="ownerFields" style="display: none;">
            <div class="mb-3">
                <label for="owner_email" class="form-label">Owner Email *</label>
                <input type="email" class="form-control" id="owner_email" name="owner_email" 
                       placeholder="owner@example.com">
            </div>
            <div class="mb-3">
                <label for="owner_password" class="form-label">Owner Password *</label>
                <input type="password" class="form-control" id="owner_password" name="owner_password" 
                       placeholder="••••••••">
            </div>
        </div>
        <?php endif; ?>
        
        <button type="submit" class="btn btn-primary"><?php echo $action === 'add' ? 'Add Cafe' : 'Update Cafe'; ?></button>
        <a href="?action=list" class="btn btn-secondary">Cancel</a>
    </form>
    
    <?php if ($action === 'add'): ?>
    <script>
    function toggleOwnerFields() {
        const checkbox = document.getElementById('create_owner');
        const fields = document.getElementById('ownerFields');
        const emailInput = document.getElementById('owner_email');
        const passwordInput = document.getElementById('owner_password');
        
        if (checkbox.checked) {
            fields.style.display = 'block';
            emailInput.required = true;
            passwordInput.required = true;
        } else {
            fields.style.display = 'none';
            emailInput.required = false;
            passwordInput.required = false;
            emailInput.value = '';
            passwordInput.value = '';
        }
    }
    </script>
    <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

