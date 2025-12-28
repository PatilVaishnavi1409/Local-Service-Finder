<?php
// reset_password.php

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

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

if (empty($email) || empty($token)) {
    $error = "Invalid or missing reset link.";
} else {
    // Verify token and expiry
    $stmt = $conn->prepare("
        SELECT user_id, reset_expires 
        FROM signup 
        WHERE email = ? AND reset_token = ?
    ");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $error = "Invalid or expired reset link.";
    } else {
        $row = $result->fetch_assoc();
        if (strtotime($row['reset_expires']) < time()) {
            $error = "This reset link has expired.";
        } else {
            // Valid → allow password change
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $new_password = trim($_POST['new_password'] ?? '');
                $confirm_password = trim($_POST['confirm_password'] ?? '');

                if (strlen($new_password) < 6) {
                    $error = "Password must be at least 6 characters.";
                } elseif ($new_password !== $confirm_password) {
                    $error = "Passwords do not match.";
                } else {
                    // Hash password (use proper hashing!)
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update password & clear reset token
                    $update = $conn->prepare("
                        UPDATE signup 
                        SET password = ?, 
                            reset_token = NULL, 
                            reset_expires = NULL 
                        WHERE user_id = ?
                    ");
                    $update->bind_param("si", $hashed_password, $row['user_id']);
                    if ($update->execute()) {
                        $message = "Password has been successfully reset!<br>You can now <a href='login.php'>log in</a> with your new password.";
                    } else {
                        $error = "Failed to update password. Please try again.";
                    }
                }
            }
        }
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - LocalServiceFinder</title>
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
        }
        .card-header {
            background: #4e73df;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .card-body {
            padding: 2.5rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="reset-card card">
        <div class="card-header">
            <h3 class="mb-0">Create New Password</h3>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-success text-center"><?= $message ?></div>
            <?php elseif (!$error): ?>
                <form method="POST">
                    <div class="mb-4">
                        <label for="new_password" class="form-label fw-bold">New Password</label>
                        <input type="password" class="form-control form-control-lg" 
                               id="new_password" name="new_password" 
                               required minlength="6">
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-bold">Confirm Password</label>
                        <input type="password" class="form-control form-control-lg" 
                               id="confirm_password" name="confirm_password" 
                               required minlength="6">
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        Reset Password
                    </button>
                </form>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="login.php" class="text-muted">← Back to Login</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>