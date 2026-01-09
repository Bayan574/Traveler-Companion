<?php
/**
 * Login Page
 * Handles authentication for both admin and owner users
 */

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: /admin/dashboard.php');
    } else {
        header('Location: /owner/dashboard.php');
    }
    exit();
}

require_once __DIR__ . '/../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $conn = getDBConnection();
        
        // Check admin table first
        $stmt = $conn->prepare("SELECT id, email, password, name FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['user_role'] = 'admin';
                $_SESSION['user_name'] = $admin['name'] ?? $admin['email'];
                header('Location: /admin/dashboard.php');
                exit();
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            // Check owner table
            $stmt = $conn->prepare("SELECT id, email, password, place_id FROM owners WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $owner = $result->fetch_assoc();
                if (password_verify($password, $owner['password'])) {
                    $_SESSION['user_id'] = $owner['id'];
                    $_SESSION['user_role'] = 'owner';
                    $_SESSION['place_id'] = $owner['place_id'];
                    $_SESSION['user_name'] = $owner['email'];
                    header('Location: /owner/dashboard.php');
                    exit();
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'Invalid email or password.';
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Traveler Companion</title>
    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Login</h1>
            <div id="errorMessage" class="error-message <?php echo $error ? 'show' : ''; ?>">
                <?php if ($error): ?>
                    <?php echo htmlspecialchars($error); ?>
                <?php endif; ?>
            </div>

            <form method="POST" action="" id="loginForm">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                
                <button type="submit" id="loginBtn" class="login-btn">Login</button>
            </form>

            <div class="text-center" style="margin-top: 15px;">
                <a href="/" class="btn btn-outline-secondary" style="text-decoration: none;">← Back to Home</a>
            </div>

            <div class="mt-3 text-center" style="margin-top: 20px; color: #666; font-size: 0.85em;">
                <strong>Default Admin:</strong> admin@airport.local <br>
               
            </div>
        </div>

        <div class="welcome-side">
            <div class="welcome-content">
                <h2>Welcome Back! 👋</h2>
                <p>Join the community of restaurants and cafes at Hail Airport. Manage your place, view customer messages, and update crowd levels.</p>
            </div>
        </div>
    </div>
</body>
</html>

