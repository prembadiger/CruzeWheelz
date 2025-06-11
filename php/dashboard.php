<?php
session_start();
require '../php/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.html"); // Redirect to homepage if not logged in
    exit();
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Fetch the user's bookings
$stmt = $conn->prepare("SELECT * FROM car_bookings WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user has any bookings
$car_bookings = $result->fetch_all(MYSQLI_ASSOC);
?>
