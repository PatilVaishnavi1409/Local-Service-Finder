<?php
session_start();

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: signup.php?error=Invalid CSRF token");
    exit();
}

// Database connection configuration
$servername = "localhost";   // Usually "localhost"
$username = "root";          // Default for XAMPP/WAMP
$password = "";              // Leave empty if no password is set
$dbname = "sem_project";     // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    header("Location: signup.php?error=Database connection failed");
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $address = isset($_POST['address']) ? filter_var($_POST['address'], FILTER_SANITIZE_STRING) : null;
    $phone = isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_STRING) : null;
    $password = $_POST['password'];
    $user_type = filter_var($_POST['user_type'], FILTER_SANITIZE_STRING);
    
    // Input validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($user_type)) {
        header("Location: signup.php?error=All required fields must be filled");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: signup.php?error=Invalid email format");
        exit();
    }
    
    if (strlen($password) < 8) {
        header("Location: signup.php?error=Password must be at least 8 characters long");
        exit();
    }
    
    if (strlen($last_name) > 10) {
        header("Location: signup.php?error=Last name must not exceed 10 characters");
        exit();
    }
    
    if (!in_array($user_type, ['customer', 'provider'])) {
        header("Location: signup.php?error=Invalid user type");
        exit();
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM signup WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        header("Location: signup.php?error=Email already exists");
        exit();
    }
    $stmt->close();
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO signup (first_name, last_name, email, password, address, phone, user_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $first_name, $last_name, $email, $hashed_password, $address, $phone, $user_type);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // Clear CSRF token
        unset($_SESSION['csrf_token']);
        header("Location: signup.php?success=1");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: signup.php?error=Registration failed");
        exit();
    }
}

$conn->close();
?> 