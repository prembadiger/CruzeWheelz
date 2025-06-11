<?php
require 'db_connection.php';
header('Content-Type: application/json');

// Turn on error reporting (useful for development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get input values safely
    $location = $_POST['location'] ?? '';
    $start = $_POST['start_date'] ?? '';
    $end = $_POST['end_date'] ?? '';

    // Check for missing inputs
    if (!$location || !$start || !$end) {
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    // SQL query to check available cars
    $sql = "SELECT COUNT(*) as available_cars
        FROM cars 
        WHERE (location_1 = ? OR location_2 = ?) 
        AND id NOT IN (
            SELECT car_id FROM car_bookings 
            WHERE (? <= car_bookings.return_date AND ? >= car_bookings.pickup_date)
        )";


    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("ssss", $location, $location, $start, $end);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Query failed: " . $stmt->error);
    }

    // Get result and return JSON
    $row = $result->fetch_assoc();
    echo json_encode(['available_cars' => $row['available_cars'] ?? 0]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
