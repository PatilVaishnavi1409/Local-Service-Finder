<?php
session_start(); // Required for dynamic navbar

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "sem_project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch providers + rating
$sql = "
    SELECT 
        s.user_id,
        s.first_name,
        s.last_name,
        sp.business_name,
        sp.service_category,
        sp.experience_years,
        sp.hourly_rate,
        sp.description,
        sp.rating
    FROM signup s
    INNER JOIN service_providers sp ON s.user_id = sp.user_id
    ORDER BY s.first_name ASC
";

$result = $conn->query($sql);

// Category → image mapping
$categoryImages = [
    'plumbing'     => 'https://images.unsplash.com/photo-1607472586893-edb57bdc0e39?auto=format&fit=crop&w=600&q=80',
    'electrical'   => 'https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?auto=format&fit=crop&w=600&q=80',
    'cleaning'     => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=600&q=80',
    'health'       => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=600&q=80',
    'transport'    => 'https://images.unsplash.com/photo-1502877338535-766e3a6052c0?auto=format&fit=crop&w=600&q=80',
    'tutoring'     => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=600&q=80',
    'transport'    => 'https://images.unsplash.com/photo-1567808291548-fc3ee04dbcf0?auto=format&fit=crop&w=600&q=80',
];

$fallbackImage = 'https://images.unsplash.com/photo-1556155092-490a1ba16284?auto=format&fit=crop&w=600&q=80';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Local Service Finder</title>
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
            padding: 10px 20px;
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
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-outline-danger {
            border-color: var(--danger);
            color: var(--danger);
        }
        
        .btn-outline-danger:hover {
            background-color: var(--danger);
            color: white;
        }
        
        .page-header {
            background: linear-gradient(rgba(78, 115, 223, 0.9), rgba(111, 66, 193, 0.9)), url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-4.0.3&auto=format&fit=crop&w=1300&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .search-section { padding: 40px 0; background: white; }
        
        .search-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .search-box {
            background: white;
            border-radius: 50px;
            padding: 5px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
        }
        
        .search-input {
            border: none;
            padding: 15px 20px;
            border-radius: 50px;
            width: 100%;
            font-size: 1.1rem;
            flex-grow: 1;
        }
        
        .search-input:focus { outline: none; }
        
        .search-button {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            font-weight: 600;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .search-button:hover { background: var(--primary-dark); }
        
        .search-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
            justify-content: center;
        }
        
        .filter-pill {
            background: var(--light);
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-pill:hover, .filter-pill.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .services-container { padding: 60px 0; }
        
        .service-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .service-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        
        .service-content { padding: 25px; }
        
        .service-category {
            font-size: 0.9rem;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .service-title {
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .service-description {
            color: #666;
            margin-bottom: 20px;
        }
        
        .service-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .service-rating {
            color: var(--warning);
            font-weight: 600;
        }
        
        .service-price {
            font-weight: 700;
            color: var(--dark);
            font-size: 1.2rem;
        }
        
        .service-action { margin-top: 20px; }
    </style>
</head>
<body>

    <!-- Dynamic Navigation Bar -->
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
                    <li class="nav-item"><a class="nav-link" href="book_service.php">Book Service</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_bookings.php">My Bookings</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.html">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                </ul>
                <div class="ms-lg-3 mt-3 mt-lg-0">
                    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                        <!-- Logged in → show Logout -->
                        <a href="logout.php" class="btn btn-outline-danger me-2">Logout</a>
                    <?php else: ?>
                        <!-- Not logged in → show Login & Sign Up -->
                        <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                        <a href="signup.php" class="btn btn-primary">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Find Your Service Provider</h1>
            <p class="page-subtitle">Discover trusted professionals for all your needs</p>
        </div>
    </section>

    <!-- Search & Filter Section -->
    <section class="search-section">
        <div class="search-container">
            <div class="search-box">
                <input type="text" class="search-input" id="searchInput" placeholder="What service are you looking for?">
                <button class="search-button" onclick="filterBySearch()">
                    <i class="fas fa-search me-2"></i> Search
                </button>
            </div>
            
            <div class="search-filters mt-4">
                <div class="filter-pill active" data-category="all">All Services</div>
                <div class="filter-pill" data-category="plumbing">Plumbing</div>
                <div class="filter-pill" data-category="electrical">Electrical</div>
                <div class="filter-pill" data-category="cleaning">Cleaning</div>
                <div class="filter-pill" data-category="health">Health</div>
                <div class="filter-pill" data-category="transport">Transport</div>
                <div class="filter-pill" data-category="tutoring">Tutoring</div>
            </div>
        </div>
    </section>

    <!-- Providers Section -->
    <section class="services-container">
        <div class="container">
            <div class="row">
                <!-- Filters Sidebar -->
                <div class="col-lg-3">
                    <div class="filter-sidebar">
                        <h3 class="filter-title">Filters</h3>
                        <!-- You can add more filters here (price, rating, etc.) later -->
                        <button class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                </div>
                
                <div class="col-lg-9">
                    <div class="row" id="providerCards">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                    $cat = strtolower(trim($row['service_category'] ?? ''));
                                    $serviceImage = $categoryImages[$cat] ?? $fallbackImage;

                                    $rating = $row['rating'] ?? 0;
                                    $rating_display = ($rating > 0) ? number_format($rating, 1) : 'No rating yet';
                                    $rating_class = ($rating > 0) ? 'text-warning' : 'text-muted';
                                ?>
                                <div class="col-md-6 col-lg-4 mb-4 provider-card" 
                                     data-category="<?= htmlspecialchars($cat) ?>">
                                    <div class="service-card">
                                        <img src="<?= htmlspecialchars($serviceImage) ?>" 
                                             alt="<?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>" 
                                             class="service-image">
                                        
                                        <div class="service-content">
                                            <div class="service-category">
                                                <?= htmlspecialchars($row['service_category'] ?: 'Services') ?>
                                            </div>
                                            <h3 class="service-title">
                                                <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>
                                            </h3>
                                            <p class="service-description">
                                                <?= htmlspecialchars(substr($row['description'] ?: 'Professional and reliable service provider.', 0, 100)) ?>...
                                            </p>
                                            
                                            <div class="service-meta">
                                                <div>
                                                    <span class="<?= $rating_class ?>">
                                                        <i class="fas fa-star"></i> <?= $rating_display ?>
                                                    </span>
                                                </div>
                                                <div class="service-price">
                                                    ₹<?= number_format($row['hourly_rate'] ?: 500, 0) ?>/hr
                                                </div>
                                            </div>
                                            
                                            <div class="service-action mt-3">
                                                <a href="provider_profile.php?id=<?= $row['user_id'] ?>" 
                                                   class="btn btn-primary w-100">View Profile</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-5">
                                <div class="alert alert-info">
                                    No service providers found yet.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Category pill filtering
        document.querySelectorAll('.filter-pill').forEach(pill => {
            pill.addEventListener('click', function() {
                document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
                this.classList.add('active');

                const category = this.getAttribute('data-category');
                const cards = document.querySelectorAll('.provider-card');

                cards.forEach(card => {
                    if (category === 'all' || card.getAttribute('data-category') === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Live search by name or category
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.provider-card');

            cards.forEach(card => {
                const title = card.querySelector('.service-title').textContent.toLowerCase();
                const category = card.querySelector('.service-category').textContent.toLowerCase();

                if (title.includes(searchTerm) || category.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>