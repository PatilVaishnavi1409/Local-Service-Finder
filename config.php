<?php
$servername = "localhost";   // Usually "localhost"
$username = "root";          // Default for XAMPP/WAMP
$password = "";              // Leave empty if no password is set
$dbname = "sem_project";     // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Uncomment this line for debugging successful connection
// echo "Connected successfully";
?>
