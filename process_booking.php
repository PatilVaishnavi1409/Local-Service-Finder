<?php
session_start();

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: book_service.php?error=Invalid CSRF token");
    exit();
}

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "sem_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header("Location: book_service.php?error=Database connection failed");
    exit();
}

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: book_service.php?error=Invalid request method");
    exit();
}

// Get and sanitize form data
$email            = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$service_category = filter_var($_POST['service_category'] ?? '', FILTER_SANITIZE_STRING);
$provider_id      = filter_var($_POST['provider_id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
$booking_date     = filter_var($_POST['booking_date'] ?? '', FILTER_SANITIZE_STRING);
$booking_time     = filter_var($_POST['booking_time'] ?? '', FILTER_SANITIZE_STRING);
$notes            = isset($_POST['notes']) ? filter_var($_POST['notes'], FILTER_SANITIZE_STRING) : null;

// New: Proposed hourly rate (can be empty → NULL)
$proposed_rate = !empty($_POST['proposed_hourly_rate']) ? (float)$_POST['proposed_hourly_rate'] : null;

// Basic validation
if (empty($email) || empty($service_category) || $provider_id <= 0 || empty($booking_date) || empty($booking_time)) {
    $conn->close();
    header("Location: book_service.php?error=All required fields must be filled");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $conn->close();
    header("Location: book_service.php?error=Invalid email format");
    exit();
}

if (strtotime($booking_date) < strtotime(date('Y-m-d'))) {
    $conn->close();
    header("Location: book_service.php?error=Booking date cannot be in the past");
    exit();
}

// Optional: Validate time format (HH:MM)
if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $booking_time)) {
    $conn->close();
    header("Location: book_service.php?error=Invalid time format");
    exit();
}

// Optional: Validate proposed rate (if provided)
if ($proposed_rate !== null && ($proposed_rate < 1 || $proposed_rate > 10000)) {
    $conn->close();
    header("Location: book_service.php?error=Proposed rate must be between ₹1 and ₹10000");
    exit();
}

// Check if provider exists (basic integrity check)
$check = $conn->prepare("SELECT 1 FROM service_providers WHERE user_id = ?");
$check->bind_param("i", $provider_id);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    $check->close();
    $conn->close();
    header("Location: book_service.php?error=Selected provider does not exist");
    exit();
}
$check->close();

// Get user_id if logged in (optional - for registered users)
$user_id = null;
if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    // Optional: verify email matches logged-in user
    $stmt = $conn->prepare("SELECT email FROM signup WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_email);
    $stmt->fetch();
    $stmt->close();
    if ($db_email !== $email) {
        $conn->close();
        header("Location: book_service.php?error=Email mismatch with logged-in user");
        exit();
    }
}

// Insert booking
$stmt = $conn->prepare("
    INSERT INTO bookings (
        user_id, provider_id, service_category, 
        booking_date, booking_time, notes, email, 
        proposed_hourly_rate, status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
");

$stmt->bind_param(
    "iisssssd",   // i = int, s = string, d = double/decimal
    $user_id,     // NULL if guest
    $provider_id,
    $service_category,
    $booking_date,
    $booking_time,
    $notes,
    $email,
    $proposed_rate
);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    // Regenerate CSRF token for next form
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    header("Location: book_service.php?success=1");
    exit();
} else {
    $error = $stmt->error;
    $stmt->close();
    $conn->close();
    header("Location: book_service.php?error=" . urlencode("Booking failed: $error"));
    exit();
}
?>