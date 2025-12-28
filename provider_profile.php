<?php
// provider_profile.php - Updated: shows personal name, no business name, no profile photo

session_start();

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "sem_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get provider ID from URL
$provider_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$provider = null;

if ($provider_id > 0) {
    $sql = "
        SELECT 
            s.user_id,
            s.first_name,
            s.last_name,
            s.email,
            s.phone,
            s.address,
            sp.service_category,
            sp.experience_years,
            sp.hourly_rate,
            sp.description,
            sp.rating,
            sp.created_at
        FROM signup s
        INNER JOIN service_providers sp ON s.user_id = sp.user_id
        WHERE s.user_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $provider_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $provider = $result->fetch_assoc();
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
    <title>
        <?php echo $provider ? htmlspecialchars($provider['first_name'] . ' ' . $provider['last_name']) . ' - Profile' : 'Provider Profile'; ?> 
        | Local Service Finder
    </title>
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
            --info: #36b9cc;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
            color: #333;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 80px 0 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-name {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
        }

        .profile-tag {
            font-size: 1.4rem;
            opacity: 0.95;
            margin-bottom: 15px;
        }

        .profile-badge {
            background: rgba(255,255,255,0.25);
            padding: 8px 24px;
            border-radius: 30px;
            font-weight: 600;
            display: inline-block;
            backdrop-filter: blur(4px);
        }

        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 40px;
            margin-top: -60px;
            position: relative;
            z-index: 10;
        }

        .info-label {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
            display: block;
        }

        .info-value {
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .rating-stars {
            color: #f6c23e;
            font-size: 1.4rem;
        }

        .contact-btn {
            font-size: 1.1rem;
            padding: 14px 30px;
            border-radius: 50px;
        }

        .section-title {
            font-weight: 800;
            color: var(--dark);
            margin: 40px 0 25px;
            position: relative;
            display: inline-block;
        }

        .section-title:after {
            content: '';
            position: absolute;
            width: 60px;
            height: 4px;
            background: var(--primary);
            bottom: -10px;
            left: 0;
            border-radius: 2px;
        }

        @media (max-width: 992px) {
            .profile-header {
                padding: 60px 0 40px;
            }
            .profile-name {
                font-size: 2.4rem;
            }
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
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

    <?php if ($provider): ?>

        <!-- Profile Header -->
        <section class="profile-header">
            <div class="container">
                <h1 class="profile-name">
                    <?= htmlspecialchars($provider['first_name'] . ' ' . $provider['last_name']) ?>
                </h1>
                
                <p class="profile-tag">
                    <?= htmlspecialchars($provider['service_category']) ?> Specialist
                </p>

                <div class="profile-badge">
                    <i class="fas fa-check-circle me-2"></i> Verified Provider
                </div>
            </div>
        </section>

        <!-- Main Profile Content -->
        <section class="container" style="margin-top: -60px;">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="profile-card">
                        <div class="row g-5">
                            <!-- Left Column -->
                            <div class="col-lg-7">
                                <h3 class="section-title">About Me</h3>
                                <p class="mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                                    <?= nl2br(htmlspecialchars($provider['description'] ?: 'No description provided yet.')) ?>
                                </p>

                                <h3 class="section-title">Services Offered</h3>
                                <div class="d-flex flex-wrap gap-2 mb-4">
                                    <span class="badge bg-primary fs-6 px-3 py-2">
                                        <?= htmlspecialchars($provider['service_category']) ?>
                                    </span>
                                </div>

                                <h3 class="section-title">Experience & Rates</h3>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="info-label">Experience</div>
                                        <div class="info-value">
                                            <i class="fas fa-clock me-2 text-primary"></i>
                                            <?= $provider['experience_years'] ?> years
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Hourly Rate</div>
                                        <div class="info-value">
                                            <i class="fas fa-rupee-sign me-2 text-primary"></i>
                                            <?= number_format($provider['hourly_rate'], 0) ?>/hr
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-lg-5">
                                <div class="bg-light rounded-4 p-4 mb-4">
                                    <h4 class="mb-4">Contact Information</h4>
                                    
                                    <div class="mb-3">
                                        <div class="info-label">Full Name</div>
                                        <div class="info-value">
                                            <?= htmlspecialchars($provider['first_name'] . ' ' . $provider['last_name']) ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="info-label">Location</div>
                                        <div class="info-value">
                                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                            <?= htmlspecialchars($provider['address'] ?: 'Not specified') ?>
                                        </div>
                                    </div>

                                    <?php if (!empty($provider['phone'])): ?>
                                    <div class="mb-3">
                                        <div class="info-label">Phone</div>
                                        <div class="info-value">
                                            <i class="fas fa-phone me-2 text-primary"></i>
                                            <?= htmlspecialchars($provider['phone']) ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <div class="mb-4">
                                        <div class="info-label">Member Since</div>
                                        <div class="info-value">
                                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                            <?= date('F Y', strtotime($provider['created_at'])) ?>
                                        </div>
                                    </div>

                                    <a href="tel:<?= htmlspecialchars($provider['phone'] ?? '') ?>" 
                                       class="btn btn-primary btn-lg contact-btn w-100 mb-3">
                                        <i class="fas fa-phone me-2"></i> Call Now
                                    </a>

                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <?php else: ?>
        <section class="py-5">
            <div class="container text-center py-5">
                <h2 class="text-danger mb-4">Provider Not Found</h2>
                <p class="lead mb-4">The requested provider profile could not be found.</p>
                <a href="services.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i> Back to Services
                </a>
            </div>
        </section>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>