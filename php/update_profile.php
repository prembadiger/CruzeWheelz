<?php
session_start();
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required'
    ]);
    exit;
}

require_once 'config.php';

// Get JSON data from request body
$data = json_decode(file_get_contents('php/input'), true);

if (!$data) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request data'
    ]);
    exit;
}

// Validate required fields
if (empty($data['name']) || empty($data['email'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Name and email are required'
    ]);
    exit;
}

try {
    // Check if email is already taken by another user
    $stmt = $pdo->prepare("
        SELECT id 
        FROM users 
        WHERE email = :email 
        AND id != :user_id
    ");
    $stmt->execute([
        ':email' => $data['email'],
        ':user_id' => $_SESSION['user_id']
    ]);

    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Email is already taken by another user'
        ]);
        exit;
    }

    // Update user profile
    $stmt = $pdo->prepare("
        UPDATE users 
        SET 
            name = :name,
            email = :email,
            phone = :phone,
            updated_at = NOW()
        WHERE id = :user_id
    ");

    $stmt->execute([
        ':name' => $data['name'],
        ':email' => $data['email'],
        ':phone' => $data['phone'] ?? null,
        ':user_id' => $_SESSION['user_id']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);

} catch (Exception $e) {
    error_log("Error updating profile: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error updating profile'
    ]);
}
?> 