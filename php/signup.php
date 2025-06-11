<?php
require_once '../php/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $fullname = isset($_POST["fullname"]) ? trim($_POST["fullname"]) : null;
    $email    = isset($_POST["email"]) ? trim($_POST["email"]) : null;
    $phone    = isset($_POST["phone"]) ? trim($_POST["phone"]) : null;
    $password = isset($_POST["password"]) ? $_POST["password"] : null;

    // Basic validation
    if (!$fullname || !$email || !$phone || !$password) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.history.back();</script>";
        exit;
    }

    if (!preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
        echo "<script>alert('Invalid phone number. Enter a valid number.'); window.history.back();</script>";
        exit;
    }

    // Check for duplicates
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email or phone number is already registered. Redirecting to login...'); window.location.href = '../html/login.html';</script>";
        exit;
    }
    $stmt->close();

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $fullname, $email, $phone, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully! Redirecting to login...'); window.location.href = '../html/login.html';</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again later.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request method.'); window.history.back();</script>";
}
?>
