<?php
session_start();
header('Content-Type: application/json');

$response = [
    'loggedIn' => false,
    'name' => ''
];

if (isset($_SESSION["user_id"])) {
    $response['loggedIn'] = true;
    $response['name'] = $_SESSION["user_name"];
}

echo json_encode($response);
?>
