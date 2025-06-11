<?php
require 'db_connection.php';

// Get filter and sort parameters from the request
$filter = $_GET['type'] ?? 'All';  // Default is "All"
$sort = $_GET['sort'] ?? 'default'; // Default is "default"

// Prepare the SQL query for filtering
$filterQuery = $filter === 'All' ? '' : "AND category = '$filter'";

// Prepare the SQL query for sorting
switch ($sort) {
    case 'price_asc':
        $orderBy = 'ORDER BY price ASC';
        break;
    case 'price_desc':
        $orderBy = 'ORDER BY price DESC';
        break;
    case 'rating_desc':
        $orderBy = 'ORDER BY rating DESC';
        break;
    case 'featured':
        $orderBy = 'ORDER BY featured DESC';
        break;
    default:
        $orderBy = '';  // Default sorting (e.g., by name)
        break;
}

// Fetch the cars based on the filter and sort
$sql = "SELECT * FROM cars WHERE 1=1 $filterQuery $orderBy";
$stmt = $conn->prepare($sql);

$stmt->execute();
$result = $stmt->get_result();
$cars = $result->fetch_all(MYSQLI_ASSOC);

// Return the cars as a JSON response
echo json_encode($cars);
?>
