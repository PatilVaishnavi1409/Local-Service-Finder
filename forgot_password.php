<?php
// forgot_password.php

session_start();

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "sem_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$error   = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT user_id FROM signup WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $error = "No account found with that email address.";
        } else {
            // Generate secure token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Save token & expiry
            $update = $conn->prepare("
                UPDATE signup 
                SET reset_token = ?, reset_expires = ? 
                WHERE email = ?
            ");
            $update->bind_param("sss", $token, $expires, $email);
            $update->execute();

            // In real project: send email here
            // For development: show link directly
            $resetLink = "http://localhost/sem_project/reset_password.php?email=" . urlencode($email) . "&token=$token";

            $message = "Password reset link has been generated.<br>";
            $message .= "<strong>Click here to reset:</strong><br>";
            $message .= "<a href='$resetLink'>$resetLink</a><br>";
            $message .= "<small>(Link expires in 1 hour)</small>";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - LocalServiceFinder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .reset-card {
            max-width: 450px;
            margin: auto;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            overflow: hidden;
        }
        .card-header {
            background: #4e73df;
            color: white;
            padding: 2rem 1.5rem;
            text-align: center;
        }
        .card-body {
            padding: 2.5rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="reset-card">
        <div class="card-header">
            <h3 class="mb-0">Reset Your Password</h3>
            <p class="mb-0 mt-2">Enter your email to receive a reset link</p>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control form-control-lg" 
                               id="email" name="email" 
                               placeholder="your.email@example.com" 
                               required autofocus>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        Send Reset Link
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="login.php" class="text-muted">← Back to Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>