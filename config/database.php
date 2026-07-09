<?php
// Database Configuration
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'gaming_portal';
$db_port = 3306;

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name, $db_port);

// Check connection
if (!$conn) {
    die('Connection Failed: ' . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($conn, 'utf8mb4');

?>