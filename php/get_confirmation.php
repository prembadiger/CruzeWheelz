<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cruzewheelz";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Get booking ID from query parameters
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit();   
}

$bookingId = $_GET['id'];

try {
    // Fetch booking + car details
    $stmt = $pdo->prepare("
        SELECT 
            cb.*, 
            c.name AS car_name, 
            c.image AS car_image 
        FROM car_bookings cb 
        JOIN cars c ON cb.car_id = c.id 
        WHERE cb.id = :bookingId
    ");
    $stmt->execute([':bookingId' => $bookingId]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        echo json_encode([
            'success' => true,
            'car_name' => $booking['car_name'],
            'user_id' => $booking['user_id'],
            'car_image' => $booking['car_image'],
            'pickup_date' => $booking['pickup_date'],
            'return_date' => $booking['return_date'],
            'rental_days' => $booking['rental_days'],
            'total_amount' => $booking['total_amount'],
            'created_at' => $booking['created_at']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching booking: ' . $e->getMessage()]);
}
