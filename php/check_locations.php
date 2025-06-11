<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    require_once '../../php/config.php';
    
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all locations
    $stmt = $conn->query("SELECT * FROM locations ORDER BY id");
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get the locations being used in bookings
    $stmt = $conn->query("
        SELECT DISTINCT pickup_location_id, return_location_id 
        FROM car_bookings 
        ORDER BY pickup_location_id, return_location_id
    ");
    $used_locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'locations' => $locations,
        'used_locations' => $used_locations,
        'total_locations' => count($locations),
        'message' => 'Location check completed successfully'
    ], JSON_PRETTY_PRINT);
    
} catch(PDOException $e) {
    error_log("Database error in check_locations.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error_type' => 'database',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
} catch(Exception $e) {
    error_log("General error in check_locations.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error_type' => 'general',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?> 