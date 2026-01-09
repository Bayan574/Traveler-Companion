<?php
/**
 * Common Functions
 * Shared utility functions for the application
 */

require_once __DIR__ . '/../config/db.php';

/**
 * Auto-update crowd count if it hasn't been updated in the last 30 minutes
 * Decreases the count gradually to simulate natural crowd reduction
 * 
 * @param int $place_id Place ID to check and update
 * @return void
 */
function autoUpdateStaleCrowd($place_id) {
    $conn = getDBConnection();
    
    // Get current crowd status with updated_at timestamp
    $stmt = $conn->prepare("SELECT crowd_count, updated_at FROM crowd_status WHERE place_id = ?");
    $stmt->bind_param("i", $place_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_count = (int)$row['crowd_count'];
        $updated_at = $row['updated_at'];
        
        // Calculate time difference in minutes
        $updated_time = strtotime($updated_at);
        $current_time = time();
        $minutes_diff = ($current_time - $updated_time) / 60;
        
        // If more than 30 minutes have passed, auto-update
        if ($minutes_diff > 30) {
            // Decrease crowd count by 1-3 people (random decrease)
            $decrease = rand(1, 3);
            $new_count = max(0, $current_count - $decrease);
            
            // Update the crowd count
            $update_stmt = $conn->prepare("UPDATE crowd_status SET crowd_count = ? WHERE place_id = ?");
            $update_stmt->bind_param("ii", $new_count, $place_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    } else {
        // If no record exists, create one with count 0
        $zero_count = 0;
        $insert_stmt = $conn->prepare("INSERT INTO crowd_status (place_id, crowd_count) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $place_id, $zero_count);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    
    $stmt->close();
    $conn->close();
}

/**
 * Get crowd counts for multiple places
 * 
 * @param array $place_ids Array of place IDs
 * @return array Associative array with place_id => crowd_count
 */
function getCrowdCounts($place_ids) {
    if (empty($place_ids)) {
        return [];
    }
    
    $conn = getDBConnection();
    $result = [];
    
    foreach ($place_ids as $place_id) {
        $place_id = (int)trim($place_id);
        if ($place_id > 0) {
            // Auto-update stale crowd counts before fetching
            autoUpdateStaleCrowd($place_id);
            
            $stmt = $conn->prepare("SELECT crowd_count FROM crowd_status WHERE place_id = ?");
            $stmt->bind_param("i", $place_id);
            $stmt->execute();
            $query_result = $stmt->get_result();
            
            if ($query_result->num_rows > 0) {
                $row = $query_result->fetch_assoc();
                $result[$place_id] = (int)$row['crowd_count'];
            } else {
                $result[$place_id] = 0;
            }
            
            $stmt->close();
        }
    }
    
    $conn->close();
    return $result;
}

/**
 * Check user authentication and authorization
 * Protects admin and owner pages from unauthorized access
 * 
 * @param string|null $required_role Required user role ('admin' or 'owner')
 * @param int|null $required_place_id Required place_id for owner operations
 * @return void Exits and redirects if authentication/authorization fails
 */
function checkAuth($required_role = null, $required_place_id = null) {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        header('Location: /auth/login.php');
        exit();
    }
    
    // Get user role and ID
    $user_role = $_SESSION['user_role'];
    $user_id = $_SESSION['user_id'];
    
    // Check required role if specified
    if ($required_role !== null && $user_role !== $required_role) {
        header('Location: /auth/login.php?error=unauthorized');
        exit();
    }
    
    // For owner-specific operations, verify place_id ownership
    if ($user_role === 'owner' && $required_place_id !== null) {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("SELECT place_id FROM owners WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $owner = $result->fetch_assoc();
            if ($owner['place_id'] != $required_place_id) {
                $stmt->close();
                $conn->close();
                header('Location: /owner/dashboard.php?error=unauthorized');
                exit();
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}

