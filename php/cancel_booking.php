<?php
header('Content-Type: application/json');
require_once 'session_auth.php';
require_once 'config.php';

// Require login and get user ID
$user_id = requireLogin();

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['booking_id']) || empty($data['booking_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Booking ID is required'
    ]);
    exit;
}

try {
    global $pdo;
    
    // First check if the booking belongs to the user and is in pending status
    $stmt = $pdo->prepare("
        SELECT id, status 
        FROM car_bookings 
        WHERE id = :booking_id 
        AND user_id = :user_id
    ");
    
    $stmt->execute([
        ':booking_id' => $data['booking_id'],
        ':user_id' => $user_id
    ]);
    
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        echo json_encode([
            'success' => false,
            'message' => 'Booking not found or unauthorized'
        ]);
        exit;
    }
    
    if ($booking['status'] !== 'pending') {
        echo json_encode([
            'success' => false,
            'message' => 'Only pending bookings can be cancelled'
        ]);
        exit;
    }
    
    // Validate cancellation reason
    if (!isset($data['cancellation_reason']) || empty($data['cancellation_reason'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Cancellation reason is required'
        ]);
        exit;
    }
    
    // Update booking status to cancelled and store the reason
    $stmt = $pdo->prepare("
        UPDATE car_bookings 
        SET status = 'cancelled',
            cancellation_reason = :reason
        WHERE id = :booking_id
    ");
    
    $stmt->execute([
        ':booking_id' => $data['booking_id'],
        ':reason' => $data['cancellation_reason']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking cancelled successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Error cancelling booking: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error cancelling booking'
    ]);
}
?> 