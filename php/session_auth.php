<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Please login to continue',
            'redirect' => 'login.html'
        ]);
        exit;
    }
    return $_SESSION['user_id'];
}

function getUserId() {
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}
?> 