<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required'
    ]);
    exit;
}

require_once 'config.php';

try {
    $stmt = $pdo->prepare("
        SELECT id, name, email, status 
        FROM users 
        WHERE id = :user_id AND status = 'active'
    ");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid user session'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ]
    ]);

} catch (Exception $e) {
    error_log("Error checking authentication: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error checking authentication'
    ]);
}
?> 