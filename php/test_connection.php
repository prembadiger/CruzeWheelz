<?php
header('Content-Type: application/json');
include 'db_connection.php';
echo json_encode(["success" => true, "message" => "DB connected successfully"]);
?>
