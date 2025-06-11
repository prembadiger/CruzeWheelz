<?php
require_once 'config.php';
header('Content-Type: application/json');

error_log("Starting auth check...");
error_log("Session data: " . print_r($_SESSION, true));

try {
    if (isset($_SESSION['admin_id'])) {
        error_log("Admin ID found in session: " . $_SESSION['admin_id']);
        
        // Check if the admin user still exists and is active
        $stmt = $pdo->prepare("
            SELECT id, name, status 
            FROM admin_users 
            WHERE id = ? AND status = 'active'
        ");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();
        
        error_log("Admin query result: " . print_r($admin, true));
        
        if ($admin) {
            error_log("Admin verified successfully");
            // Update last activity
            $_SESSION['last_activity'] = time();
            
            echo json_encode([
                'success' => true,
                'admin' => [
                    'id' => $admin['id'],
                    'name' => $admin['name']
                ]
            ]);
            exit;
        } else {
            error_log("Admin not found or inactive");
        }
    } else {
        error_log("No admin_id in session");
    }
    
    // If we get here, the user is not authenticated
    session_destroy(); // Clear invalid session
    error_log("Authentication failed - clearing session");
    
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated'
    ]);
    
} catch (Exception $e) {
    error_log("Auth check error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => 'Authentication check failed'
    ]);
}
?> 