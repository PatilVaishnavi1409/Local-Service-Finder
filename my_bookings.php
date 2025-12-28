<?php
// my_bookings.php - View all booked services for the logged-in user
// Updated: Correctly displays booking date + time (fixes 12:00 AM issue)

session_start();

// Redirect if not logged in
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

// Fetch bookings - include booking_time
$stmt = $conn->prepare("
    SELECT 
        b.booking_id,
        b.provider_id,
        b.booking_date,
        b.booking_time,                  -- ADDED: this is where the actual time is stored
        b.status,
        b.service_category,
        CONCAT(s.first_name, ' ', s.last_name) AS provider_name,
        sp.business_name,
        sp.service_category AS provider_category,
        sp.hourly_rate
    FROM bookings b
    LEFT JOIN signup s ON b.provider_id = s.user_id
    LEFT JOIN service_providers sp ON b.provider_id = sp.user_id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC, b.booking_time DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Local Service Finder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4e73df;
            --primary-dark: #3a56c4;
            --success: #1cc88a;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --info: #36b9cc;
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
        .bookings-container {
            max-width: 1100px;
            margin: 60px auto;
        }
        .booking-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            overflow: hidden;
            transition: transform 0.2s;
        }
        .booking-card:hover {
            transform: translateY(-5px);
        }
        .booking-header {
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 15px 20px;
            font-weight: 700;
        }
        .booking-body {
            padding: 20px;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .status-completed { background: var(--success); color: white; }
        .status-pending   { background: var(--warning); color: white; }
        .status-cancelled { background: var(--danger); color: white; }
        .btn-review {
            background: #ffc107;
            border: none;
            color: #212529;
            font-weight: 600;
        }
        .btn-review:hover {
            background: #e0a800;
        }
        .no-bookings {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
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
                <li class="nav-item"><a class="nav-link" href="help_center.html">Help Center</a></li>
                <li class="nav-item"><a class="nav-link" href="about.html">About Us</a></li>
            </ul>
            <div class="ms-lg-3 mt-3 mt-lg-0">
                <a href="logout.php" class="btn btn-outline-danger me-2">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="bookings-container container">
    <h2 class="text-center fw-bold mb-5">My Bookings</h2>

    <?php if (empty($bookings)): ?>
        <div class="no-bookings">
            <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
            <h4 class="text-muted">No bookings found</h4>
            <p class="text-muted mb-4">You haven't booked any services yet.</p>
            <a href="service.php" class="btn btn-primary btn-lg px-5">Browse Services</a>
        </div>
    <?php else: ?>
        <?php foreach ($bookings as $booking): ?>
            <div class="booking-card">
                <div class="booking-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><?= htmlspecialchars($booking['provider_name']) ?></h5>
                        <?php if (!empty($booking['business_name'])): ?>
                            <small><?= htmlspecialchars($booking['business_name']) ?></small>
                        <?php endif; ?>
                    </div>
                    <span class="status-badge status-<?= strtolower($booking['status']) ?>">
                        <?= ucfirst($booking['status']) ?>
                    </span>
                </div>
                <div class="booking-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Service:</strong> <?= htmlspecialchars($booking['service_category'] ?: $booking['provider_category'] ?: 'N/A') ?></p>

                            <?php
                            // Combine date + time safely (handles missing time gracefully)
                            $date_part = $booking['booking_date'] ?? 'Unknown';
                            $time_part = $booking['booking_time'] ?? '00:00:00';
                            $full_datetime = $date_part . ' ' . $time_part;

                            // Format for display
                            $display_datetime = date('d M Y, h:i A', strtotime($full_datetime));
                            ?>

                            <p><strong>Booked for:</strong> <?= htmlspecialchars($display_datetime) ?></p>

                            <?php if (!empty($booking['hourly_rate'])): ?>
                                <p><strong>Hourly Rate:</strong> ₹<?= number_format($booking['hourly_rate'], 2) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <?php if ($booking['status'] === 'completed'): ?>
                                <a href="feedback.php?provider=<?= urlencode($booking['provider_id']) ?>" 
                                   class="btn btn-review btn-sm px-4">
                                    <i class="fas fa-star me-1"></i> Rate & Review
                                </a>
                            <?php elseif ($booking['status'] === 'pending'): ?>
                                <small class="text-warning">Waiting for confirmation</small>
                            <?php elseif ($booking['status'] === 'cancelled'): ?>
                                <small class="text-danger">Booking cancelled</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>