<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // First, get a car ID
    $car = $pdo->query("SELECT id FROM cars LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$car) {
        throw new Exception('No cars found in database');
    }

    // Insert a test booking
    $stmt = $pdo->prepare("
        INSERT INTO car_bookings (
            user_id, 
            car_id, 
            pickup_date, 
            return_date, 
            pickup_location,
            return_location,
            total_amount,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

    $stmt->execute([
        $_SESSION['user_id'],
        $car['id'],
        date('Y-m-d'),
        date('Y-m-d', strtotime('+3 days')),
        'Test Pickup Location',
        'Test Return Location',
        5000,
        'pending'
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Test booking added successfully',
        'booking_id' => $pdo->lastInsertId()
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 