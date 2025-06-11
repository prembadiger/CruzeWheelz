<?php
session_start();
include __DIR__ . '/db_connection.php';  // Correct your file path here

$email = $_POST['email'] ?? '';
$number = $_POST['number'] ?? '';
$password = $_POST['password'] ?? '';

// Use prepared statements to prevent SQL injection
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        // Login successful, set session and return success       
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];

        // Send response back with success status
        echo json_encode([
            'status' => 'success',
            'user_name' => $row['name']
        ]);

        // Optionally, store the login state in localStorage for front-end
        echo "<script>
                localStorage.setItem('loggedIn', 'true');
                localStorage.setItem('userName', '{$row['name']}');
                window.location.href = '../index.html'; // Redirect to homepage after successful login
              </script>";
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Incorrect password'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not found'
    ]);
}
?>
