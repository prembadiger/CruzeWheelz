<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

// Get car ID safely
$carId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$carId || $carId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Car ID']);
    exit;
}

// Prepare and execute SQL
$sql = "SELECT * FROM cars WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $carId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Car not found']);
    $stmt->close();
    $conn->close();
    exit;
}

$car = $result->fetch_assoc();

// Parse multiline strings to arrays
$features = array_filter(array_map('trim', explode("\n", str_replace("• ", "", $car['features'] ?? ''))));
$specifications = array_filter(array_map('trim', explode("\n", str_replace("• ", "", $car['specifications'] ?? ''))));
$locations = array_filter(array_map('trim', [$car['location_1'] ?? '', $car['location_2'] ?? '']));

// Return structured car data
echo json_encode([
    'success' => true,
    'car' => [
        'name' => $car['name'],
        'category' => $car['category'],
        'rating' => (float)$car['rating'],
        'image' => $car['image'],
        'price' => (float)$car['price'],
        'description' => $car['description'],
        'features' => $features,
        'specifications' => $specifications,
        'locations' => $locations
    ]
]);

$stmt->close();
$conn->close();         
?>
