<?php
// provider_dashboard.php - Provider dashboard to manage bookings

session_start();

// Redirect if not logged in or not a provider
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'provider') {
    header("Location: login.php?redirect=provider_dashboard.php");
    exit();
}

$provider_id = $_SESSION['user_id'];
$error_message = '';
$success_message = '';

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "sem_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_POST['new_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $new_status = $_POST['new_status'];

    // Validate status
    $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        $stmt = $conn->prepare("
            UPDATE bookings 
            SET status = ? 
            WHERE booking_id = ? AND provider_id = ?
        ");
        $stmt->bind_param("sii", $new_status, $booking_id, $provider_id);
        
        if ($stmt->execute()) {
            $success_message = "Booking status updated successfully.";
        } else {
            $error_message = "Failed to update status. Please try again.";
        }
        $stmt->close();
    } else {
        $error_message = "Invalid status selected.";
    }
}

// Fetch all bookings for this provider
$bookings = [];
$stmt = $conn->prepare("
    SELECT 
        b.booking_id,
        b.service_category,
        b.booking_date,
        b.booking_time,
        b.notes,
        b.email AS customer_email,
        b.status,
        CONCAT(s.first_name, ' ', s.last_name) AS customer_name
    FROM bookings b
    LEFT JOIN signup s ON b.user_id = s.user_id
    WHERE b.provider_id = ?
    ORDER BY b.booking_date DESC, b.booking_time DESC
");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Dashboard - Local Service Finder</title>
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
            --warning: #f6c23e;
            --danger: #e74a3b;
        }
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
            color: #333;
        }
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 60px 0 40px;
            text-align: center;
        }
        .dashboard-header h1 {
            font-weight: 800;
            font-size: 2.8rem;
            margin-bottom: 10px;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .status-pending    { background: #fff3cd; color: #856404; }
        .status-confirmed  { background: #d1e7dd; color: #0f5132; }
        .status-completed  { background: #badbcc; color: #0f5132; font-weight: bold; }
        .status-cancelled  { background: #f8d7da; color: #842029; }
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .table th {
            background: var(--primary);
            color: white;
            font-weight: 700;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-hands-helping"></i> LocalServiceFinder
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="provider_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Header -->
    <section class="dashboard-header">
        <div class="container">
            <h1>Welcome to Your Dashboard</h1>
            <p class="lead">Manage your service bookings and customer requests</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="container py-5">
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Your Bookings</h4>
            </div>
            <div class="card-body p-0">
                <?php if (empty($bookings)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No bookings yet</h5>
                        <p class="text-muted">When customers book your services, they will appear here.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Date & Time</th>
                                    <th>Notes</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($booking['customer_name'] ?: 'Guest') ?></td>
                                        <td><?= htmlspecialchars($booking['service_category']) ?></td>
                                        <td>
                                            <?= date('d M Y', strtotime($booking['booking_date'])) ?><br>
                                            <small><?= date('h:i A', strtotime($booking['booking_time'])) ?></small>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars(substr($booking['notes'] ?: '-', 0, 60)) ?>
                                            <?= strlen($booking['notes'] ?? '') > 60 ? '...' : '' ?>
                                        </td>
                                        <td><?= htmlspecialchars($booking['customer_email'] ?: '-') ?></td>
                                        <td>
                                            <span class="status-badge status-<?= $booking['status'] ?>">
                                                <?= ucfirst($booking['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                <select name="new_status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                                    <option value="pending"    <?= $booking['status'] === 'pending'    ? 'selected' : '' ?>>Pending</option>
                                                    <option value="confirmed"  <?= $booking['status'] === 'confirmed'  ? 'selected' : '' ?>>Confirm</option>
                                                    <option value="completed"  <?= $booking['status'] === 'completed'  ? 'selected' : '' ?>>Complete</option>
                                                    <option value="cancelled"  <?= $booking['status'] === 'cancelled'  ? 'selected' : '' ?>>Cancel</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>