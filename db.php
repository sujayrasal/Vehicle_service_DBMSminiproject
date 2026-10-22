<?php

$servername = "localhost";
$username = "root";
$password = "MyNewPassword123!";
$database = "vehicle_service_db";

$conn = new mysqli(
    $servername,
    $username,
    $password,
    $database
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

?>