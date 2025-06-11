<?php
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $identifier = trim($_POST["identifier"]);

    if (empty($identifier)) {
        echo "Please enter email or phone number.";
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo "No account found with that email or phone.";
        exit;
    }

    // You can implement token generation and email sending here
    echo "Password reset link has been sent (simulation).";
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
