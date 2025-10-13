<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Service Finder - Find Trusted Professionals</title>
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
        
        .hero-section {
            background: linear-gradient(rgba(78, 115, 223, 0.85), rgba(111, 66, 193, 0.85)), url('https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1300&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 40px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .search-box {
            background: white;
            border-radius: 50px;
            padding: 5px;
            margin: 0 auto;
            max-width: 700px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }
        
        .search-input {
            border: none;
            padding: 15px 20px;
            border-radius: 50px;
            width: 70%;
        }
        
        .search-input:focus {
            outline: none;
        }
        
        .search-btn {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            font-weight: 600;
            width: 28%;
            transition: all 0.3s;
        }
        
        .search-btn:hover {
            background: var(--secondary);
        }
        
        .section-title {
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 40px;
            position: relative;
            text-align: center;
        }
        
        .section-title:after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: var(--primary);
            margin: 15px auto;
        }
        
        .service-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 30px;
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .service-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        
        .service-content {
            padding: 25px;
        }
        
        .service-title {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .features-section {
            background: white;
            padding: 80px 0;
        }
        
        .feature-box {
            text-align: center;
            padding: 30px;
            border-radius: 15px;
            transition: all 0.3s ease;
            background: var(--light);
            height: 100%;
        }
        
        .feature-box:hover {
            background: white;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .cta-section {
            background: linear-gradient(rgba(78, 115, 223, 0.9), rgba(111, 66, 193, 0.9)), url('https://images.unsplash.com/photo-1516387938699-a93567ec168e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1300&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        
        .help-section {
            padding: 80px 0;
            background: var(--light);
        }
        
        .accordion-button {
            font-weight: 700;
            padding: 20px;
            border-radius: 10px !important;
        }
        
        .accordion-button:not(.collapsed) {
            background-color: var(--primary);
            color: white;
        }
        
        footer {
            background: var(--dark);
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-title {
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            display: block;
            margin-bottom: 10px;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer-links a:hover {
            color: white;
            transform: translateX(5px);
        }
        
        .social-icons a {
            color: white;
            font-size: 1.5rem;
            margin-right: 15px;
            transition: all 0.3s;
        }
        
        .social-icons a:hover {
            color: var(--primary);
            transform: translateY(-3px);
        }
        
        .copyright {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 5px;
        }
        
        .service-category {
            margin-bottom: 30px;
        }
        
        .category-title {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-hands-helping"></i> LocalServiceFinder
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="provider_signup.php">Register Provider</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="service.html">Services</a>
                    </li>
                   
                    <li class="nav-item">
                        <a class="nav-link" href="help_center.html">Help Center</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.html">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
                <div class="ms-lg-3 mt-3 mt-lg-0">
                    <a href="login.html" class="btn btn-outline-primary me-2">Login</a>
                    <a href="signup.html" class="btn btn-primary">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Find Trusted Local Service Professionals</h1>
            <p class="hero-subtitle">Connect with verified plumbers, electricians, doctors, tutors, mechanics, rental services and more</p>
            
            <div class="search-box">
                <input type="text" class="search-input" placeholder="What service do you need? (e.g., 'water leakage', 'fan not working')">
                <button class="search-btn">Search</button>
            </div>
            
          
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container py-5">
            <h2 class="text-center section-title">Our Services</h2>
            
            <div class="service-category">
                <h3 class="category-title">Home Services</h3>
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="service-card">
                            <img src="https://techsquadteam.com/assets/profile/blogimages/6c04a4953d85a1c3771857d3d22e9240.jpg" alt="Plumbing" class="service-img">
                            <div class="service-content">
                                <h4 class="service-title">Plumbing</h4>
                                <p>Fix leaks, clogs, installations and more with certified plumbers.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Find Plumbers</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="service-card">
                            <img src="https://images.unsplash.com/photo-1581291518633-83b4ebd1d83e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Electrical" class="service-img">
                            <div class="service-content">
                                <h4 class="service-title">Electrical</h4>
                                <p>Wiring, repairs, installations by certified electricians.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Find Electricians</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="service-card">
                            <img src="https://plus.unsplash.com/premium_photo-1663011218145-c1d0c3ba3542?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8Y2xlYW5pbmd8ZW58MHx8MHx8fDA%3D" alt="Cleaning" class="service-img">
                            <div class="service-content">
                                <h4 class="service-title">Cleaning</h4>
                                <p>Professional home and office cleaning services.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Find Cleaners</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="service-card">
                            <img src="https://images.jdmagicbox.com/comp/def_content/residential_pest_control_services/default-residential-pest-control-services-304-250.jpg" alt="Pest Control" class="service-img">
                            <div class="service-content">
                                <h4 class="service-title">Pest Control</h4>
                                <p>Professional pest control and extermination services.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Find Experts</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="service-category">
                <h3 class="category-title">Health & Wellness</h3>
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="service-card">
                            <img src="https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Doctors" class="service-img">
                            <div class="service-content">
                                <h4 class="service-title">Doctors</h4>
                                <p>Find and book appointments with qualified doctors.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Find Doctors</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="service-card">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqS6_QFL3ZhbwrQOFwWlGQOwAQzxVtPEGR2g&s" alt="Dentists" class="service-img">
                            <div class="service-content">
                                <h4 class="service-title">Dentists</h4>
                                <p>Professional dental care and treatment services.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Find Dentists</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="service-card">
                            <img src="https://media.istockphoto.com/id/1478745519/photo/close-up-of-physiotherapist-working-with-patient-on-the-bed.jpg?s=612x612&w=0&k=20&c=etDD6btysRBkAtl_0-L71kB50Pl_oNgFvLO_PyS49cM=" alt="Physiotherapy" class="service-img">
                            <div class="service-content">
                                <h4 class="service-title">Physiotherapy</h4>
                                <p>Rehabilitation and physical therapy services.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Find Therapists</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="service-card">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTJZWBjl9K9l_YmNVdUSiQ6Y7ecqdhEv1hYSA&s" alt="Yoga Trainers" class="service-img">
                            <div class="service-content">
                                <h4 class="service-title">Yoga Trainers</h4>
                                <p>Professional yoga instructors for fitness and wellness.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Find Trainers</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="service-category">
                <h3 class="category-title">Transport & Rental</h3>
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="service-card">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSoMu3dzKMCx8VrZS4OOrHLQNuhUTlHtuCDLA&s" alt="Mechanics" class="service-img">
                            <div class="service-content">
                                <h4 class="service-title">Car Repair</h4>
                                <p>Car repairs, maintenance, and diagnostics by expert mechanics.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Find Mechanics</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="service-card">
                            <img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Car Rental" class="service-img">
                            <div class="service-content">
                                <h4 class="service-title">Car Rental</h4>
                                <p>Rent cars for short or long durations at affordable rates.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Rent a Car</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="service-card">
                            <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Moving Services" class="service-img">
                            <div class="service-content">
                                <h4 class="service-title">Moving Services</h4>
                                <p>Professional packing and moving services for home and office.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Find Movers</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="service-card">
                            <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Property Rental" class="service-img">
                            <div class="service-content">
                                <h4 class="service-title">Property Rental</h4>
                                <p>Find apartments, houses, and commercial spaces for rent.</p>
                                <a href="#" class="btn btn-sm btn-outline-primary">Find Properties</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="#" class="btn btn-primary">View All Services</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="text-center section-title">How It Works</h2>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4>1. Search</h4>
                        <p>Describe your problem or search for a service you need</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-compress-alt"></i>
                        </div>
                        <h4>2. Compare</h4>
                        <p>View profiles, ratings, and prices of service providers</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h4>3. Book</h4>
                        <p>Book instantly or schedule for later with your chosen provider</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="hero-title">Are You a Service Provider?</h2>
            <p class="hero-subtitle">Join thousands of professionals already growing their business with us</p>
            <a href="register.html" class="btn btn-light btn-lg">Register Your Service</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h3 class="navbar-brand text-white"><i class="fas fa-hands-helping"></i> LocalServiceFinder</h3>
                    <p class="mt-3">Connecting people with reliable and verified local service providers in their area.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h5 class="footer-title">Quick Links</h5>
                    <div class="footer-links">
                        <a href="#">Home</a>
                        <a href="#">Services</a>
                        <a href="#">How It Works</a>
                        <a href="#">Help Center</a>
                        <a href="#">About Us</a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h5 class="footer-title">Services</h5>
                    <div class="footer-links">
                        <a href="#">Home Services</a>
                        <a href="#">Health & Wellness</a>
                        <a href="#">Transport & Rental</a>
                        <a href="#">Education</a>
                        <a href="#">View All</a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-4">
                    <h5 class="footer-title">Contact Us</h5>
                    <div class="footer-links">
                        <p><i class="fas fa-map-marker-alt me-2"></i> 123 Service Road, City</p>
                        <p><i class="fas fa-phone me-2"></i> +1 234 567 8900</p>
                        <p><i class="fas fa-envelope me-2"></i> info@localservicefinder.com</p>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2023 Local Service Finder. All Rights Reserved.</p>
                <p>SY BTECH CSE (DATA SCIENCE) Project by Pranav, Pavan, Vaishnavi & Dakshita</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>