<?php
// feedback.php - Rate & Review provider after completed booking
// Stores average rating + count in service_providers

session_start();

// Must be logged in
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "sem_project");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get provider ID from URL
$provider_id = isset($_GET['provider']) ? (int)$_GET['provider'] : 0;

$provider_name = 'Unknown Provider';
$can_rate = false;
$error = '';
$success = false;

// If no provider ID → redirect
if ($provider_id <= 0) {
    header("Location: services.php?msg=select_provider_to_review");
    exit();
}

// Fetch provider name
$stmt = $conn->prepare("
    SELECT CONCAT(first_name, ' ', last_name) AS name
    FROM signup 
    WHERE user_id = ?
");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $provider_name = $row['name'];
} else {
    $error = "Provider not found.";
}
$stmt->close();

// Check if user has completed booking with this provider
if (empty($error)) {
    $check = $conn->prepare("
        SELECT 1 
        FROM bookings 
        WHERE user_id = ? 
          AND provider_id = ? 
          AND LOWER(status) = 'completed'
        LIMIT 1
    ");
    $check->bind_param("ii", $user_id, $provider_id);
    $check->execute();
    $can_rate = $check->get_result()->num_rows > 0;
    $check->close();
}

// Handle form submission
if ($can_rate && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_rating = isset($_POST['rating']) ? (float)$_POST['rating'] : 0;

    if ($new_rating < 1 || $new_rating > 5) {
        $error = "Please select a rating between 1 and 5 stars.";
    } else {
        // Get current rating and count
        $stmt = $conn->prepare("
            SELECT rating, rating_count 
            FROM service_providers 
            WHERE user_id = ?
        ");
        $stmt->bind_param("i", $provider_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $current_avg = 0.00;
        $current_count = 0;

        if ($row = $result->fetch_assoc()) {
            $current_avg   = (float)$row['rating'];
            $current_count = (int)$row['rating_count'];
        }
        $stmt->close();

        // Calculate new average
        $new_count = $current_count + 1;
        $new_avg = ($current_avg * $current_count + $new_rating) / $new_count;

        // Update both fields
        $update = $conn->prepare("
            UPDATE service_providers 
            SET rating = ?, 
                rating_count = ? 
            WHERE user_id = ?
        ");
        $update->bind_param("dii", $new_avg, $new_count, $provider_id);

        if ($update->execute()) {
            $success = true;
        } else {
            $error = "Failed to save rating: " . $conn->error;
        }
        $update->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate & Review - <?= htmlspecialchars($provider_name) ?> | Local Service Finder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4e73df;
            --primary-dark: #3a56c4;
            --success: #1cc88a;
            --danger: #e74a3b;
            --warning: #f6c23e;
        }
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        .navbar-brand {
            font-weight: 800;
            color: var(--primary);
            font-size: 1.8rem;
        }
        .nav-link {
            color: #5a5c69;
            font-weight: 600;
            margin: 0 10px;
        }
        .nav-link:hover {
            color: var(--primary);
        }
        .rating-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 50px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .stars-container {
            direction: rtl;
            display: flex;
            justify-content: center;
            gap: 8px;
            font-size: 3.2rem;
        }
        .star-rating {
            color: #e0e0e0;
            cursor: pointer;
            transition: color 0.2s;
        }
        .stars-container label:hover,
        .stars-container label:hover ~ label,
        input[type="radio"]:checked ~ label {
            color: #ffc107;
        }
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 14px 50px;
            border-radius: 50px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
        }
    </style>
</head>
<body>

<!-- Dynamic Navigation Bar (same as your other pages) -->
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
                <li class="nav-item"><a class="nav-link" href="service.php">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="help-center.html">Help Center</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
            </ul>
            <div class="ms-lg-3 mt-3 mt-lg-0">
                <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="btn btn-outline-danger me-2">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                    <a href="signup.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="rating-container">
        <h2 class="text-center fw-bold mb-4">Rate & Review</h2>
        <p class="text-center text-muted mb-5">
            How would you rate <strong><?= htmlspecialchars($provider_name) ?></strong>?
        </p>

        <?php if ($success): ?>
            <div class="alert alert-success text-center">
                <strong>Thank you!</strong> Your rating has been saved.
                <div class="mt-4">
                    <a href="services.php" class="btn btn-primary">Back to Services</a>
                </div>
            </div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger text-center mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif (!$can_rate): ?>
            <div class="alert alert-info text-center">
                <h5 class="mb-3">Cannot Rate This Provider</h5>
                <p>You can only rate this provider after completing a booking with them.</p>
                <a href="services.php" class="btn btn-primary mt-3">Find Services</a>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <!-- Star Rating -->
                <div class="stars-container mb-5">
                    <input type="radio" name="rating" value="5" id="star5" class="d-none" required>
                    <label for="star5" class="star-rating"><i class="fas fa-star"></i></label>

                    <input type="radio" name="rating" value="4" id="star4" class="d-none">
                    <label for="star4" class="star-rating"><i class="fas fa-star"></i></label>

                    <input type="radio" name="rating" value="3" id="star3" class="d-none">
                    <label for="star3" class="star-rating"><i class="fas fa-star"></i></label>

                    <input type="radio" name="rating" value="2" id="star2" class="d-none">
                    <label for="star2" class="star-rating"><i class="fas fa-star"></i></label>

                    <input type="radio" name="rating" value="1" id="star1" class="d-none">
                    <label for="star1" class="star-rating"><i class="fas fa-star"></i></label>
                </div>

                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary btn-lg px-5">Submit Rating</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>