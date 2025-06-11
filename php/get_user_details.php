<?php
session_start();
include('db_connection.php');

$user_id = $_SESSION['user_id']; // Assuming user is logged in and user_id is stored in session

$query = "SELECT username, profile_pic FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo json_encode($user);
?>
