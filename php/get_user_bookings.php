<?php
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required'
    ]);
    exit;
}

require_once 'config.php';

try {
    // First check if the user exists
    $userCheck = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $userCheck->execute([$_SESSION['user_id']]);
    if (!$userCheck->fetch()) {
        throw new Exception('User not found');
    }

    // Get user's bookings with car details
    $stmt = $pdo->prepare("
        SELECT 
            b.id,
            b.pickup_date,
            b.return_date,
            b.total_amount,
            b.status,
            b.rental_days,
            b.payment_method,
            b.payment_status,
            b.cancellation_reason,
            b.created_at,
            b.updated_at,
            l1.name as pickup_location,
            l2.name as return_location,
            c.name as car_model,
            c.image as car_image,
            c.name as car_name
        FROM car_bookings b
        LEFT JOIN cars c ON b.car_id = c.id
        LEFT JOIN locations l1 ON b.pickup_location_id = l1.id
        LEFT JOIN locations l2 ON b.return_location_id = l2.id
        WHERE b.user_id = ?
        ORDER BY 
            CASE 
                WHEN b.status = 'ongoing' THEN 1
                WHEN b.status = 'confirmed' THEN 2
                WHEN b.status = 'pending' THEN 3
                WHEN b.status = 'completed' THEN 4
                WHEN b.status = 'cancelled' THEN 5
            END,
            b.pickup_date DESC
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Log the query results for debugging
    error_log("Found " . count($bookings) . " bookings for user " . $_SESSION['user_id']);

    echo json_encode([
        'success' => true,
        'bookings' => array_map(function($booking) {
            return [
                'id' => $booking['id'],
                'pickup_date' => $booking['pickup_date'],
                'return_date' => $booking['return_date'],
                'total_amount' => $booking['total_amount'],
                'status' => $booking['status'],
                'rental_days' => $booking['rental_days'],
                'payment_method' => $booking['payment_method'],
                'payment_status' => $booking['payment_status'],
                'cancellation_reason' => $booking['cancellation_reason'],
                'created_at' => $booking['created_at'],
                'updated_at' => $booking['updated_at'],
                'location' => $booking['pickup_location'],
                'return_location' => $booking['return_location'],
                'car_name' => $booking['car_name'],
                'car_model' => $booking['car_model'],
                'car_image' => $booking['car_image']
            ];
        }, $bookings)
    ]);

} catch (Exception $e) {
    error_log("Error fetching user bookings: " . $e->getMessage());
    error_log("User ID: " . $_SESSION['user_id']);
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching bookings: ' . $e->getMessage()
    ]);
}
?> 