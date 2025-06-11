<?php
// Prevent any HTML error output
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Check if config.php exists
if (!file_exists('config.php')) {
    echo json_encode([
        'success' => false,
        'message' => 'Configuration file not found'
    ]);
    exit;
}

require_once 'config.php';

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Enable error logging
    error_log("Attempting to fetch locations from database");

    // Simple query to get all locations
    $stmt = $conn->query("SELECT * FROM locations");
    
    if (!$stmt) {
        throw new Exception("Failed to execute query");
    }

    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($locations)) {
        error_log("No locations found in database");
    } else {
        error_log("Found " . count($locations) . " locations");
    }
    
    echo json_encode([
        'success' => true,
        'locations' => $locations,
        'count' => count($locations)
    ]);

} catch(PDOException $e) {
    error_log("Database error in get_locations.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    error_log("General error in get_locations.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
