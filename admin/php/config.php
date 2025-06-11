<?php
// Start session with secure settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error.log');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'cruzewheelz');
define('DB_USER', 'root');
define('DB_PASS', '');

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Database connection
try {
    $admin_db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

// For backward compatibility
$main_db = $admin_db;
$pdo = $admin_db;

// Helper Functions
function requireAdmin() {
    error_log("Checking admin session... Session data: " . print_r($_SESSION, true));
    
    if (!isset($_SESSION['admin_id'])) {
        error_log("No admin_id in session");
        header('Location: ../login.html');
        exit;
    }
    
    if (!isset($_SESSION['last_activity']) || (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        error_log("Session timeout");
        session_destroy();
        header('Location: ../login.html');
        exit;
    }
    
    $_SESSION['last_activity'] = time();
    error_log("Admin check passed for user ID: " . $_SESSION['admin_id']);
    return true;
}

function logActivity($action, $description = '') {
    global $admin_db;
    try {
        $stmt = $admin_db->prepare("
            INSERT INTO admin_logs (admin_id, action, description, ip_address)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['admin_id'] ?? null,
            $action,
            $description,
            $_SERVER['REMOTE_ADDR']
        ]);
    } catch (Exception $e) {
        error_log("Error logging activity: " . $e->getMessage());
    }
}

// Check if this is an AJAX request
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

// If not an AJAX request and we're not on the login page, check authentication
$current_script = basename($_SERVER['SCRIPT_NAME']);
if (!isAjaxRequest() && $current_script !== 'login.html' && $current_script !== 'admin_actions.php') {
    requireAdmin();
}

// CORS headers for security
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
?>