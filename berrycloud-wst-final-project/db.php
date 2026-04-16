<?php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Your MySQL username
define('DB_PASS', ''); // Your MySQL password
define('DB_NAME', 'berrycloud'); // Your database name

// Create a new MySQLi connection
//$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn = mysqli_connect("localhost", "root", "", "recipe_db");

// Check if the connection failed and stop with an error
if ($conn->connect_error) {
	die('Database connection failed: ' . $conn->connect_error);
}

// Set character encoding to UTF-8 for proper text handling
$conn->set_charset('utf8mb4');
?>