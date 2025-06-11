<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // First verify current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!password_verify($data['current_password'], $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit;
    }

    // Start building the update query
    $updates = [];
    $params = [];

    // Update name if provided
    if (!empty($data['name'])) {
        $updates[] = "name = ?";
        $params[] = $data['name'];
    }

    // Update phone if provided
    if (!empty($data['phone'])) {
        $updates[] = "phone = ?";
        $params[] = $data['phone'];
    }

    // Update password if provided
    if (!empty($data['new_password'])) {
        $updates[] = "password = ?";
        $params[] = password_hash($data['new_password'], PASSWORD_DEFAULT);
    }

    if (empty($updates)) {
        echo json_encode(['success' => false, 'message' => 'No changes provided']);
        exit;
    }

    // Add user_id to params
    $params[] = $_SESSION['user_id'];

    // Execute update
    $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} 