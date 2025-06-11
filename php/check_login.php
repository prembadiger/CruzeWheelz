<?php
header('Content-Type: application/json');
require_once 'session_auth.php';

echo json_encode([
    'success' => true,
    'logged_in' => isLoggedIn(),
    'user_id' => getUserId()
]);
?> 