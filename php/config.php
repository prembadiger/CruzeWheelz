<?php
// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'cruzewheelz');
define('DB_USER', 'root');
define('DB_PASS', '');

// Razorpay Configuration
define('RAZORPAY_KEY_ID', 'rzp_test_RtmrzqChi9YPHo'); // Replace with your actual test key
define('RAZORPAY_KEY_SECRET', 'CclSdGC67XXD421NwJh9Qddu'); // Replace with your actual secret key

// Create PDO connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]));
}
?>