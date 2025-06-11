<?php
header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if booking ID is provided
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Booking ID not provided']);
    exit;
}

require_once 'config.php';

try {
    // Prepare the query to get booking details with car and location information
    $stmt = $pdo->prepare("
        SELECT 
            b.id,
            c.name as car_model,
            pl.name as pickup_location,
            rl.name as return_location,
            b.pickup_date,
            b.return_date,
            b.total_amount,
            b.payment_status,
            b.payment_method,
            b.status as booking_status
        FROM car_bookings b
        JOIN cars c ON b.car_id = c.id
        JOIN locations pl ON b.pickup_location_id = pl.id
        JOIN locations rl ON b.return_location_id = rl.id
        WHERE b.id = :booking_id AND b.user_id = :user_id
    ");

    $stmt->execute([
        ':booking_id' => $_GET['id'],
        ':user_id' => $_SESSION['user_id']
    ]);

    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        echo json_encode([
            'success' => true,
            'booking' => $booking
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Booking not found or unauthorized access'
        ]);
    }

} catch(Exception $e) {
    error_log("Error fetching booking details: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching booking details'
    ]);
}
?> 