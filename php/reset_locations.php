<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    require_once '../../php/config.php';
    
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // First, check if locations exist
    $stmt = $conn->query("SELECT COUNT(*) as count FROM locations");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($count == 0) {
        // Insert the default locations
        $locations = [
            'Bagalkot', 'Ballari', 'Belagavi', 'Bengaluru Rural',
            'Bengaluru Urban', 'Bidar', 'Chikkamagaluru', 'Davanagere',
            'Dharwad', 'Gadag', 'Haveri', 'Mysuru', 'Uttar Kannada'
        ];
        
        $stmt = $conn->prepare("INSERT INTO locations (name) VALUES (?)");
        
        foreach ($locations as $location) {
            $stmt->execute([$location]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Locations have been reset and repopulated',
            'locations_added' => count($locations)
        ], JSON_PRETTY_PRINT);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Locations already exist',
            'count' => $count
        ], JSON_PRETTY_PRINT);
    }
    
} catch(PDOException $e) {
    error_log("Database error in reset_locations.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error_type' => 'database',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
} catch(Exception $e) {
    error_log("General error in reset_locations.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error_type' => 'general',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?> 