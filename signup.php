<?php
session_start();

// ============================================================================
// Redirect if already logged in
// ============================================================================
if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'provider') {
        header("Location: provider_dashboard.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

// ============================================================================
// CSRF token generation
// ============================================================================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ============================================================================
// Database connection
// ============================================================================
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "sem_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors  = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Invalid request. Please try again.";
    } else {
        $user_type        = $_POST['user_type'] ?? 'customer';
        $first_name       = trim($_POST['first_name'] ?? '');
        $last_name        = trim($_POST['last_name'] ?? '');
        $email            = trim($_POST['email'] ?? '');
        $address          = trim($_POST['address'] ?? '');
        $phone            = trim($_POST['phone'] ?? '');
        $password         = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Provider-specific fields
        $business_name     = ($user_type === 'provider') ? trim($_POST['business_name'] ?? '') : null;
        $service_category  = ($user_type === 'provider') ? trim($_POST['service_category'] ?? '') : null;
        $experience_years  = ($user_type === 'provider') ? (int)($_POST['experience_years'] ?? 0) : 0;
        $hourly_rate       = ($user_type === 'provider') ? (float)($_POST['hourly_rate'] ?? 0.00) : 0.00;
        $description       = ($user_type === 'provider') ? trim($_POST['description'] ?? '') : null;

        // Validation
        if (empty($first_name))         $errors[] = "First name is required.";
        if (empty($last_name))          $errors[] = "Last name is required.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
        if (strlen($password) < 8)      $errors[] = "Password must be at least 8 characters.";
        if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

        if ($user_type === 'provider') {
            // Business name is now OPTIONAL → removed required check
            if (empty($service_category))  $errors[] = "Service category is required for providers.";
        }

        if (empty($errors)) {
            $conn->begin_transaction();

            try {
                // Check if email already exists
                $check = $conn->prepare("SELECT 1 FROM signup WHERE email = ?");
                $check->bind_param("s", $email);
                $check->execute();
                if ($check->get_result()->num_rows > 0) {
                    throw new Exception("Email already registered.");
                }
                $check->close();

                // Insert into signup table
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare(
                    "INSERT INTO signup (first_name, last_name, email, address, phone, password)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->bind_param("ssssss", $first_name, $last_name, $email, $address, $phone, $hashed);
                $stmt->execute();
                $user_id = $conn->insert_id;
                $stmt->close();

                // If provider, insert into service_providers
                if ($user_type === 'provider') {
                    $pstmt = $conn->prepare(
                        "INSERT INTO service_providers 
                         (user_id, business_name, service_category, experience_years, hourly_rate, description)
                         VALUES (?, ?, ?, ?, ?, ?)"
                    );
                    $pstmt->bind_param("isssds", $user_id, $business_name, $service_category, $experience_years, $hourly_rate, $description);
                    $pstmt->execute();
                    $pstmt->close();
                }

                $conn->commit();
                $success = true;

                // Refresh CSRF token after successful signup
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            } catch (Exception $e) {
                $conn->rollback();
                $errors[] = $e->getMessage();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Local Service Finder</title>
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
        
        * { margin:0; padding:0; box-sizing:border-box; }
        
        body {
            font-family: 'Nunito', sans-serif;
            color: #333;
            background-color: #f8f9fc;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
        
        .nav-link {
            color: var(--dark);
            font-weight: 600;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active { color: var(--primary); }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .signup-container {
            display: flex;
            flex: 1;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        
        .signup-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 1000px;
            display: flex;
            min-height: 600px;
        }
        
        .signup-form {
            padding: 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .signup-hero {
            flex: 1;
            background: linear-gradient(rgba(78, 115, 223, 0.85), rgba(111, 66, 193, 0.85)), url('https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 0 15px 15px 0;
        }
        
        .form-title {
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 10px;
            text-align: center;
        }
        
        .form-subtitle {
            color: var(--dark);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .form-control, .form-select {
            padding: 15px 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark);
        }
        
        .user-type-selector {
            display: flex;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #ddd;
        }
        
        .user-type-btn {
            flex: 1;
            padding: 12px;
            text-align: center;
            background: #f8f9fc;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }
        
        .user-type-btn.active {
            background: var(--primary);
            color: white;
        }
        
        .user-type-btn:not(.active):hover {
            background: #e9ecef;
        }
        
        .error-message {
            color: var(--danger);
            margin-bottom: 15px;
            text-align: center;
        }
        
        .success-message {
            color: var(--success);
            margin-bottom: 15px;
            text-align: center;
        }
        
        @media (max-width: 992px) {
            .signup-hero { display: none; }
            .signup-card { max-width: 500px; }
        }
        
        @media (max-width: 576px) {
            .signup-form { padding: 30px 20px; }
            .form-title { font-size: 1.8rem; }
            .user-type-selector { flex-direction: column; }
            .user-type-btn { padding: 10px; }
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
                    <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                    <a href="signup.php" class="btn btn-primary">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Signup Container -->
    <div class="signup-container">
        <div class="container">
            <div class="signup-card">
                <div class="signup-form">
                    <h2 class="form-title">Create Your Account</h2>
                    <p class="form-subtitle">Join thousands of users who find and provide services</p>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="error-message">
                            <?php foreach ($errors as $err): ?>
                                <div><?= htmlspecialchars($err) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="success-message">
                            Account created successfully! Please <a href="login.php">login</a>.
                        </div>
                    <?php endif; ?>
                    
                    <div class="user-type-selector">
                        <div class="user-type-btn active" id="customer-btn" onclick="setUserType('customer')">I'm a Customer</div>
                        <div class="user-type-btn" id="provider-btn" onclick="setUserType('provider')">I'm a Provider</div>
                    </div>
                    
                    <form action="" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="user_type" id="user_type" value="customer">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="first_name" placeholder="Enter your first name" required maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Enter your last name" required maxlength="100">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address">
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required minlength="8">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password" placeholder="Confirm your password" required>
                                </div>
                            </div>
                        </div>

                        <!-- Provider-only fields (hidden by default) -->
                        <div class="provider-field" id="provider-fields" style="display: none;">
                            <div class="mb-3">
                                <label for="businessName" class="form-label">
                                    Business Name <small class="text-muted">(optional)</small>
                                </label>
                                <input type="text" class="form-control" id="businessName" name="business_name" placeholder="Enter business name (optional)">
                            </div>

                            <div class="mb-3">
                                <label for="serviceCategory" class="form-label">Service Category</label>
                                <select class="form-control" id="serviceCategory" name="service_category">
                                    <option value="">Select your service category</option>
                                    <option value="plumbing">Plumbing</option>
                                    <option value="electrical">Electrical</option>
                                    <option value="cleaning">Cleaning</option>
                                    <option value="health">Health & Wellness</option>
                                    <option value="transport">Transport & Rental</option>
                                    <option value="tutoring">Tutoring</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="experienceYears" class="form-label">Years of Experience</label>
                                <input type="number" class="form-control" id="experienceYears" name="experience_years" min="0" value="0">
                            </div>

                            <div class="mb-3">
                                <label for="hourlyRate" class="form-label">Hourly Rate (₹)</label>
                                <input type="number" step="0.01" class="form-control" id="hourlyRate" name="hourly_rate" min="0" value="0.00">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Describe your services..."></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>
                    
                    <div class="signup-footer mt-4 text-center">
                        <p>Already have an account? <a href="login.php" class="text-primary">Log in</a></p>
                    </div>
                </div>
                
                <div class="signup-hero">
                    <h3 class="hero-title">Join Our Community</h3>
                    <p class="hero-subtitle">Connect with trusted professionals or grow your business</p>
                    
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Access thousands of verified service providers</li>
                        <li><i class="fas fa-check"></i> Book services with just a few clicks</li>
                        <li><i class="fas fa-check"></i> Manage all your appointments in one place</li>
                        <li><i class="fas fa-check"></i> Secure payment options</li>
                        <li><i class="fas fa-check"></i> Build your reputation with reviews</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setUserType(type) {
            document.getElementById('user_type').value = type;
            document.getElementById('customer-btn').classList.toggle('active', type === 'customer');
            document.getElementById('provider-btn').classList.toggle('active', type === 'provider');

            const providerFields = document.querySelectorAll('.provider-field');
            providerFields.forEach(field => {
                field.style.display = (type === 'provider') ? 'block' : 'none';
            });
        }

        // Client-side password match validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirmPassword').value;
            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>