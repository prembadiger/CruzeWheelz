<?php
require_once 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get table structure
    $columns = $pdo->query("DESCRIBE car_bookings")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get a sample booking
    $sample = $pdo->query("SELECT * FROM car_bookings LIMIT 1")->fetch(PDO::FETCH_ASSOC);

    // Get all column names
    $columnNames = $pdo->query("SHOW COLUMNS FROM car_bookings")->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode([
        'success' => true,
        'columns' => $columns,
        'column_names' => $columnNames,
        'sample_booking' => $sample
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?> 