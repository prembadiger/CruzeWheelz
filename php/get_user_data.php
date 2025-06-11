<?php
header('Content-Type: application/json');
require_once 'session_auth.php';
require_once 'config.php';

// Require login and get user ID
$user_id = requireLogin();

try {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id, name, email, phone FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
    }
} catch (Exception $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching user data'
    ]);
}
?> 