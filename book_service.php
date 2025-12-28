<?php
// book_service.php - Autofetch email + 12-hour time picker
// Updated: Clear visible display of selected time in 12-hour format
//          Prevents confusion where time might appear as 12:00 AM elsewhere

session_start();

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Login status & user email
$is_logged_in = isset($_SESSION['user_id']);
$user_email = '';

$service_types = [];
$providers = [];
$error_message = '';

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "sem_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    $error_message = "Cannot connect to database: " . $conn->connect_error;
} else {
    // Fetch logged-in user's email
    if ($is_logged_in) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT email FROM signup WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($user_email);
        $stmt->fetch();
        $stmt->close();
    }

    // Distinct service categories
    $result = $conn->query("
        SELECT DISTINCT sp.service_category 
        FROM service_providers sp 
        INNER JOIN signup s ON sp.user_id = s.user_id
        WHERE sp.service_category IS NOT NULL 
          AND sp.service_category != ''
        ORDER BY sp.service_category ASC
    ");

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $service_types[] = ['service_type' => $row['service_category']];
        }
    }

    // Providers with name and hourly rate
    $result = $conn->query("
        SELECT 
            s.user_id AS provider_id,
            CONCAT(s.first_name, ' ', s.last_name) AS name,
            sp.service_category,
            sp.hourly_rate
        FROM signup s
        INNER JOIN service_providers sp ON s.user_id = sp.user_id
        WHERE s.first_name IS NOT NULL 
          AND s.last_name IS NOT NULL
        ORDER BY s.first_name ASC, s.last_name ASC
    ");

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $providers[] = [
                'provider_id'    => $row['provider_id'],
                'name'           => $row['name'],
                'service_type'   => $row['service_category'],
                'hourly_rate'    => $row['hourly_rate'] ?? 0
            ];
        }
    }

    $conn->close();
}

if (empty($service_types)) {
    $error_message .= ($error_message ? "<br>" : "") . "No service categories found.";
}
if (empty($providers)) {
    $error_message .= ($error_message ? "<br>" : "") . "No registered service providers available yet.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Service - Local Service Finder</title>
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
        
        .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .booking-container {
            display: flex;
            flex: 1;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        
        .booking-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 1000px;
            display: flex;
            min-height: 650px;
        }
        
        .booking-form {
            padding: 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .booking-hero {
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
        
        .form-control, .form-select, .time-picker-input {
            padding: 15px 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus, .time-picker-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
            outline: none;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark);
        }
        
        .time-picker-wrapper {
            position: relative;
        }
        
        .time-picker-input {
            width: 100%;
            cursor: pointer;
        }
        
        .time-picker-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            max-height: 240px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        
        .time-picker-dropdown.show {
            display: block;
        }
        
        .time-option {
            padding: 10px 15px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .time-option:hover {
            background: #f0f4ff;
            color: var(--primary);
        }
        
        .time-option.selected {
            background: var(--primary);
            color: white;
        }
        
        .error-message, .warning-message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .error-message {
            color: var(--danger);
            background: #f8d7da;
            border: 1px solid #f5c2c7;
        }
        
        .success-message {
            color: var(--success);
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }
        
        .db-warning, .no-data-message, .login-required {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .login-required a {
            color: var(--primary);
            font-weight: 700;
        }
        
        .selected-time-display {
            margin-top: -5px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            color: var(--primary);
            font-weight: 600;
        }
        
        @media (max-width: 992px) {
            .booking-hero { display: none; }
            .booking-card { max-width: 500px; }
        }
        
        @media (max-width: 576px) {
            .booking-form { padding: 30px 20px; }
            .form-title { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

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
                    <li class="nav-item"><a class="nav-link active" href="service.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="help_center.html">Help Center</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.html">About Us</a></li>
                </ul>
                <div class="ms-lg-3 mt-3 mt-lg-0">
                    <?php if ($is_logged_in): ?>
                        <a href="logout.php" class="btn btn-outline-danger me-2">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                        <a href="signup.php" class="btn btn-primary">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="booking-container">
        <div class="container">
            <div class="booking-card">
                <div class="booking-form">
                    <h2 class="form-title">Book a Service</h2>
                    <p class="form-subtitle">Schedule your service with trusted professionals</p>

                    <?php if (!empty($error_message)): ?>
                        <div class="db-warning">
                            <?= htmlspecialchars($error_message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
                    <?php endif; ?>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="success-message">Service booked successfully! Check your email for confirmation.</div>
                    <?php endif; ?>

                    <?php if (!$is_logged_in): ?>
                        <div class="login-required">
                            <strong>Please log in to book a service.</strong><br>
                            You need an account to schedule with our verified providers.<br><br>
                            <a href="login.php?redirect=book_service.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i> Login to Continue
                            </a>
                            <p class="mt-3 mb-0">
                                Don't have an account? 
                                <a href="signup.php" class="text-primary">Sign up now</a>
                            </p>
                        </div>
                    <?php elseif (empty($providers)): ?>
                        <div class="no-data-message">
                            <strong>No service providers available at the moment.</strong><br>
                            Please check back later or contact support if you believe this is an error.
                        </div>
                    <?php else: ?>
                        <form action="process_booking.php" method="POST" id="bookingForm">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                            <div class="mb-3">
                                <label for="serviceCategory" class="form-label">Service Category</label>
                                <select class="form-control" id="serviceCategory" name="service_category" required>
                                    <option value="">Select a service category</option>
                                    <?php foreach ($service_types as $type): ?>
                                        <option value="<?= htmlspecialchars($type['service_type']) ?>">
                                            <?= htmlspecialchars($type['service_type']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="provider" class="form-label">Service Provider</label>
                                <select class="form-control" id="provider" name="provider_id" required>
                                    <option value="">Select a provider</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bookingDate" class="form-label">Preferred Date</label>
                                        <input type="date" class="form-control" id="bookingDate" name="booking_date" 
                                               required min="<?= date('Y-m-d') ?>" 
                                               title="Select a date from today onwards">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="timePickerInput" class="form-label">Preferred Time (12-hour format)</label>
                                        <div class="time-picker-wrapper">
                                            <input type="text" class="form-control time-picker-input" id="timePickerInput" 
                                                   placeholder="Select time (e.g. 2:30 PM)" readonly required>
                                            <input type="hidden" id="bookingTime" name="booking_time" required>
                                            <div class="time-picker-dropdown" id="timePickerDropdown"></div>
                                        </div>
                                        <!-- Show exactly what the user selected in 12-hour format -->
                                        <div id="selectedTimeDisplay" class="selected-time-display" style="display:none;">
                                            Selected time: <span id="displayTimeText"></span>
                                        </div>
                                        <small class="form-text text-muted mt-1">Available between 8:00 AM and 8:00 PM</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="4" 
                                          placeholder="Any special requirements..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Your Email (for confirmation)</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Enter your email" 
                                       value="<?= htmlspecialchars($user_email) ?>" 
                                       <?= $user_email ? 'readonly' : 'required' ?> 
                                       maxlength="150">
                                <?php if ($user_email): ?>
                                    <small class="form-text text-muted">Your registered email (cannot be changed here)</small>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Confirm Booking</button>
                        </form>
                    <?php endif; ?>
                </div>

                <div class="booking-hero">
                    <h3 class="hero-title">Book with Confidence</h3>
                    <p class="hero-subtitle">Connect with trusted professionals for all your service needs</p>
                    
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Verified service providers</li>
                        <li><i class="fas fa-check"></i> Flexible scheduling options</li>
                        <li><i class="fas fa-check"></i> Secure booking process</li>
                        <li><i class="fas fa-check"></i> Real-time availability</li>
                        <li><i class="fas fa-check"></i> Easy cancellation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Providers data from PHP → JavaScript
        const providers = <?= json_encode($providers) ?>;

        // Filter providers by selected category
        document.getElementById('serviceCategory')?.addEventListener('change', function() {
            const selected = this.value.trim();
            const providerSelect = document.getElementById('provider');

            providerSelect.innerHTML = '<option value="">Select a provider</option>';

            let count = 0;
            providers.forEach(p => {
                if (p.service_type === selected) {
                    count++;
                    const opt = document.createElement('option');
                    opt.value = p.provider_id;
                    opt.textContent = `${p.name} (₹${parseFloat(p.hourly_rate).toFixed(0)}/hr)`;
                    opt.dataset.rate = p.hourly_rate;
                    providerSelect.appendChild(opt);
                }
            });

            if (count === 0 && selected !== '') {
                providerSelect.innerHTML = '<option value="">No providers available for this category</option>';
            }
        });

        // Auto-trigger on page load if category is pre-selected
        document.getElementById('serviceCategory')?.dispatchEvent(new Event('change'));

        // ───────────────────────────────────────────────
        // 12-hour time picker with visible confirmation
        // ───────────────────────────────────────────────
        const timeDisplayInput = document.getElementById('timePickerInput');
        const hiddenTimeField  = document.getElementById('bookingTime');
        const dropdown         = document.getElementById('timePickerDropdown');
        const selectedDisplay  = document.getElementById('selectedTimeDisplay');
        const displayTimeSpan  = document.getElementById('displayTimeText');

        function populateTimeOptions() {
            dropdown.innerHTML = '';
            for (let hour = 8; hour <= 20; hour++) {
                for (let min = 0; min < 60; min += 15) {
                    const hour12 = hour % 12 || 12;
                    const ampm   = hour >= 12 ? 'PM' : 'AM';
                    const displayText = `${hour12}:${min.toString().padStart(2, '0')} ${ampm}`;
                    const value24     = `${hour.toString().padStart(2, '0')}:${min.toString().padStart(2, '0')}`;

                    const option = document.createElement('div');
                    option.className = 'time-option';
                    option.textContent = displayText;
                    option.dataset.value = value24;

                    option.addEventListener('click', () => {
                        timeDisplayInput.value = displayText;
                        hiddenTimeField.value  = value24;
                        displayTimeSpan.textContent = displayText;
                        selectedDisplay.style.display = 'block';
                        dropdown.classList.remove('show');

                        // Visual feedback
                        dropdown.querySelectorAll('.time-option').forEach(opt => opt.classList.remove('selected'));
                        option.classList.add('selected');
                    });

                    dropdown.appendChild(option);
                }
            }
        }

        timeDisplayInput?.addEventListener('click', () => {
            if (!dropdown.classList.contains('show')) {
                populateTimeOptions();
                dropdown.classList.add('show');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!timeDisplayInput?.contains(e.target) && !dropdown?.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Basic client-side validation
        document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
            const dateInput = document.getElementById('bookingDate');
            const timeValue = hiddenTimeField.value;

            if (dateInput.value < '<?= date('Y-m-d') ?>') {
                e.preventDefault();
                alert('Please select a date from today onwards.');
                dateInput.focus();
                return;
            }

            if (!timeValue) {
                e.preventDefault();
                alert('Please select a preferred time.');
                timeDisplayInput.focus();
                return;
            }

            // Optional: warn if outside normal hours (though picker already restricts)
            const hour = parseInt(timeValue.split(':')[0], 10);
            if (hour < 8 || hour > 20) {
                if (!confirm('Selected time is outside typical service hours (8 AM – 8 PM). Proceed anyway?')) {
                    e.preventDefault();
                    timeDisplayInput.focus();
                }
            }
        });
    </script>
</body>
</html>