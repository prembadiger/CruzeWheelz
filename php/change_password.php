<?php
// Change password
session_start();
require_once 'db_connection.php'; // Database connection

$user_id = $_SESSION['user_id']; // Or replace with your user identifier
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];

// Fetch existing password from the database
$query = "SELECT password FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verify the current password
if (password_verify($current_password, $user['password'])) {
  // Hash the new password and update
  $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
  $update_query = "UPDATE users SET password = ? WHERE id = ?";
  $update_stmt = $conn->prepare($update_query);
  $update_stmt->bind_param("si", $hashed_password, $user_id);
  $update_stmt->execute();

  echo json_encode(['success' => true, 'message' => 'Password changed successfully!']);
} else {
  echo json_encode(['success' => false, 'message' => 'Incorrect current password']);
}
?>
