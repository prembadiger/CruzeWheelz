<?php
require_once 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Check tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    // Check bookings table structure
    $bookingsColumns = $pdo->query("DESCRIBE bookings")->fetchAll(PDO::FETCH_ASSOC);
    
    // Check cars table structure
    $carsColumns = $pdo->query("DESCRIBE cars")->fetchAll(PDO::FETCH_ASSOC);
    
    // Check users table structure
    $usersColumns = $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'tables' => $tables,
        'structure' => [
            'bookings' => $bookingsColumns,
            'cars' => $carsColumns,
            'users' => $usersColumns
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 