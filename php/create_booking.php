<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include session authentication
require_once 'session_auth.php';

// Require login and get user ID
$user_id = requireLogin();

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

// Log received data for debugging
error_log("Received booking data: " . print_r($data, true));

// Validate required fields
$required_fields = ['car_id', 'pickup_location_id', 'return_location_id', 'pickup_date', 'return_date', 'payment_method', 'total_amount'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => 'Missing required field: ' . $field]);
        exit;
    }
}

require_once 'config.php';

try {
    // Use the existing PDO connection from config.php
    global $pdo;
    if (!isset($pdo)) {
        throw new Exception("Database connection not available");
    }

    // Check if car exists
    $stmt = $pdo->prepare("SELECT id FROM cars WHERE id = :car_id");
    $stmt->execute([':car_id' => $data['car_id']]);
    if (!$stmt->fetch()) {
        throw new Exception("Invalid car_id");
    }

    // Log location IDs for debugging
    error_log("Checking locations - Pickup ID: " . $data['pickup_location_id'] . ", Return ID: " . $data['return_location_id']);
    
    // Check if both locations exist (separately)
    $stmt = $pdo->prepare("SELECT id FROM locations WHERE id IN (:pickup_id, :return_id)");
    $stmt->execute([
        ':pickup_id' => $data['pickup_location_id'],
        ':return_id' => $data['return_location_id']
    ]);
    
    $found_locations = $stmt->fetchAll(PDO::FETCH_COLUMN);
    error_log("Found locations: " . print_r($found_locations, true));
    
    // Check if both pickup and return locations exist
    if (!in_array($data['pickup_location_id'], $found_locations)) {
        throw new Exception("Invalid pickup location ID");
    }
    if (!in_array($data['return_location_id'], $found_locations)) {
        throw new Exception("Invalid return location ID");
    }

    // Check if car is available for the selected dates
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM car_bookings 
        WHERE car_id = :car_id 
        AND (
            (:pickup_date BETWEEN pickup_date AND return_date)
            OR (:return_date BETWEEN pickup_date AND return_date)
            OR (pickup_date BETWEEN :pickup_date AND :return_date)
        )
    ");
    
    $stmt->execute([
        ':car_id' => $data['car_id'],
        ':pickup_date' => $data['pickup_date'],
        ':return_date' => $data['return_date']
    ]);
    
    if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Car is not available for selected dates']);
        exit;
    }

    // Calculate rental days
    $pickup = new DateTime($data['pickup_date']);
    $return = new DateTime($data['return_date']);
    $rental_days = $return->diff($pickup)->days + 1;

    // Begin transaction
    $pdo->beginTransaction();

    // Create booking
    $stmt = $pdo->prepare("
        INSERT INTO car_bookings (
            car_id, 
            user_id, 
            pickup_date, 
            return_date, 
            pickup_location_id,
            return_location_id,
            rental_days,
            total_amount,
            payment_method,
            payment_status,
            status
        ) VALUES (
            :car_id,
            :user_id,
            :pickup_date,
            :return_date,
            :pickup_location_id,
            :return_location_id,
            :rental_days,
            :total_amount,
            :payment_method,
            'pending',
            'pending'
        )
    ");

    $stmt->execute([
        ':car_id' => $data['car_id'],
        ':user_id' => $user_id, // Use the verified user ID from session
        ':pickup_date' => $data['pickup_date'],
        ':return_date' => $data['return_date'],
        ':pickup_location_id' => $data['pickup_location_id'],
        ':return_location_id' => $data['return_location_id'],
        ':rental_days' => $rental_days,
        ':total_amount' => $data['total_amount'],
        ':payment_method' => $data['payment_method']
    ]);

    $booking_id = $pdo->lastInsertId();

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'booking_id' => $booking_id,
        'message' => 'Booking created successfully'
    ]);

} catch(Exception $e) {
    error_log("Booking error: " . $e->getMessage());
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}