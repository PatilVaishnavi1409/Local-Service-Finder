<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "sem_project";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $service_type = trim($_POST['service_type'] ?? '');
    $price_range = trim($_POST['price_range'] ?? '');
    $rating = trim($_POST['rating'] ?? '');

    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($phone) || empty($service_type) || empty($price_range)) {
        $error = "All fields except address and rating are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error = "Phone number must be 10 digits.";
    } elseif (strlen($price_range) > 50) {
        $error = "Price range must not exceed 50 characters.";
    } elseif (!empty($rating) && (!is_numeric($rating) || $rating < 0 || $rating > 5)) {
        $error = "Rating must be a number between 0 and 5.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT email FROM signup WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Start transaction
            $conn->begin_transaction();

            try {
                // Insert into signup table
                $user_type = 'provider';
                $stmt = $conn->prepare("INSERT INTO signup (first_name, last_name, email, password, address, phone, user_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $first_name, $last_name, $email, $hashed_password, $address, $phone, $user_type);
                if (!$stmt->execute()) {
                    throw new Exception("Error registering user.");
                }

                // Get the inserted user_id
                $user_id = $conn->insert_id;

                // Insert into service_provider table
                $name = $first_name . ' ' . $last_name; // Combine first_name and last_name
                $stmt = $conn->prepare("INSERT INTO service_provider (provider_id, name, email, service_type, address, phone, price_range, rating, datejoined) VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE())");
                // Handle nullable rating
                $rating = !empty($rating) ? $rating : null;
                $stmt->bind_param("issssssd", $user_id, $name, $email, $service_type, $address, $phone, $price_range, $rating);
                if (!$stmt->execute()) {
                    throw new Exception("Error registering service provider.");
                }

                // Commit transaction
                $conn->commit();

                // Redirect to login page
                header("Location: login.php");
                exit();
            } catch (Exception $e) {
                // Rollback transaction on error
                $conn->rollback();
                $error = "Error registering: " . $e->getMessage();
            }
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
    <title>Register as Service Provider - Local Service Finder</title>
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Nunito', sans-serif;
            color: #333;
            background-color: #f8f9fc;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        .nav-link:hover, .nav-link.active {
            color: var(--primary);
        }
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
        .btn-google {
            background-color: #fff;
            color: #757575;
            border: 1px solid #ddd;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
            margin-bottom: 15px;
        }
        .btn-google:hover {
            background-color: #f5f5f5;
            border-color: #ccc;
        }
        .btn-facebook {
            background-color: #3b5998;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
        }
        .btn-facebook:hover {
            background-color: #344e86;
            color: white;
        }
        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1;
            min-height: calc(100vh - 70px); /* Adjust for navbar height */
            padding: 20px;
        }
        .register-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1000px;
            display: flex;
            min-height: 550px;
        }
        .register-form {
            padding: 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .register-hero {
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
        .form-control {
            padding: 15px 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark);
        }
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #ddd;
        }
        .divider span {
            padding: 0 15px;
            color: #777;
            font-weight: 600;
        }
        .register-footer {
            text-align: center;
            margin-top: 20px;
        }
        .hero-title {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 20px;
        }
        .hero-subtitle {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .feature-list {
            list-style-type: none;
            padding: 0;
        }
        .feature-list li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .feature-list i {
            background: rgba(255, 255, 255, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        .error-message {
            color: var(--danger);
            text-align: center;
            margin-bottom: 20px;
        }
        @media (max-width: 992px) {
            .register-hero {
                display: none;
            }
            .register-card {
                max-width: 500px;
            }
        }
        @media (max-width: 576px) {
            .register-form {
                padding: 30px 20px;
            }
            .form-title {
                font-size: 1.8rem;
            }
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
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="service.html">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="help-center.html">Help Center</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.html">About Us</a>
                    </li>
                </ul>
                <div class="ms-lg-3 mt-3 mt-lg-0">
                    <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                    <a href="signup.php" class="btn btn-primary">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Register Container -->
    <div class="register-container">
        <div class="container">
            <div class="register-card">
                <div class="register-form">
                    <h2 class="form-title">Register as Service Provider</h2>
                    <p class="form-subtitle">Join our platform to offer your services</p>

                    <?php if (!empty($error)): ?>
                        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter your first name" required>
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter your last name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address (Optional)</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
                        </div>

                        <div class="mb-3">
                            <label for="service_type" class="form-label">Service Type</label>
                            <select class="form-control" id="service_type" name="service_type" required>
                                <option value="" disabled selected>Select your service type</option>
                                <option value="Plumber">Plumber</option>
                                <option value="Electrician">Electrician</option>
                                <option value="Doctor">Doctor</option>
                                <option value="Tutor">Tutor</option>
                                <option value="Mechanic">Mechanic</option>
                                <option value="Rental Service">Rental Service</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="price_range" class="form-label">Price Range</label>
                            <input type="text" class="form-control" id="price_range" name="price_range" placeholder="Enter price range (e.g., 50-100)" required maxlength="50">
                        </div>

                        <div class="mb-3">
                            <label for="rating" class="form-label">Initial Rating (Optional, 0 to 5)</label>
                            <input type="number" class="form-control" id="rating" name="rating" placeholder="Enter initial rating (e.g., 4.5)" step="0.01" min="0" max="5">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Register</button>

                        <div class="divider">
                            <span>Or continue with</span>
                        </div>

                        <button type="button" class="btn btn-google">
                            <i class="fab fa-google me-2"></i> Google
                        </button>

                        <button type="button" class="btn btn-facebook">
                            <i class="fab fa-facebook-f me-2"></i> Facebook
                        </button>
                    </form>

                    <div class="register-footer">
                        <p>Already have an account? <a href="login.php" class="text-primary">Login now</a></p>
                        <p>Register as a customer? <a href="signup.php" class="text-primary">Sign up here</a></p>
                    </div>
                </div>

                <div class="register-hero">
                    <h3 class="hero-title">Become a Trusted Service Provider</h3>
                    <p class="hero-subtitle">Join our platform to offer your expertise to local customers</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Reach thousands of local customers</li>
                        <li><i class="fas fa-check"></i> Manage your bookings easily</li>
                        <li><i class="fas fa-check"></i> Grow your business with our platform</li>
                        <li><i class="fas fa-check"></i> Secure payment processing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>