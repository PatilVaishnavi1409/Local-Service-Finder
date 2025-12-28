<?php
// logout.php
session_start();

// ============================================================================
// Destroy the session completely and securely
// ============================================================================

// 1. Clear all session variables
$_SESSION = [];

// 2. If session cookie exists → delete it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,                          // expire in the past
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Destroy the session
session_destroy();

// ============================================================================
// Optional: redirect immediately (recommended)
// or show a nice logout confirmation page
// ============================================================================

// You can choose one of these two approaches:

// Option A: Instant redirect (most common & secure)
// header("Location: index.php");
// exit();

// Option B: Show a nice logout confirmation page (what this file implements)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out - Local Service Finder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4e73df;
            --primary-dark: #3a56c4;
            --secondary: #6f42c1;
            --light: #f8f9fc;
            --dark: #5a5c69;
            --success: #1cc88a;
            --danger: #e74a3b;
        }
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        .navbar-brand {
            font-weight: 800;
            color: var(--primary);
            font-size: 1.8rem;
        }
        .logout-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .logout-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            max-width: 500px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
        }
        .icon-circle {
            width: 100px;
            height: 100px;
            background: rgba(28, 200, 138, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        .icon-circle i {
            font-size: 3.5rem;
            color: var(--success);
        }
        h1 {
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 20px;
        }
        p {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 35px;
        }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 14px 40px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <!-- Navigation Bar (same as other pages) -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-hands-helping"></i> LocalServiceFinder
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="service.html">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="help-center.html">Help Center</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.html">About Us</a></li>
                </ul>
                <div class="ms-lg-3">
                    <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                    <a href="signup.php" class="btn btn-primary">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Logout Confirmation -->
    <div class="logout-container">
        <div class="logout-card">
            <div class="icon-circle">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h1>You've been logged out</h1>
            <p>Thank you for using Local Service Finder.<br>See you again soon!</p>
            <a href="index.php" class="btn btn-primary">Return to Home</a>
            <p class="mt-4 text-muted small">or <a href="login.php" class="text-primary">log in again</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>