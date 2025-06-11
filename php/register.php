<?php
ob_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email already exists
    $check_query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
            alert('This email is already registered. Redirecting to login page...');
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 1000); // Redirect after 1 second
        </script>";
        exit();
    }
    
    
    // Continue with registration if email is new
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $insert_query = "INSERT INTO users (email, password) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ss", $email, $password);

    if ($stmt->execute()) {
        echo "<script>
            alert('Registration successful! Redirecting to login...');
            window.location.href = 'login.php';
        </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

}
?>