<?php
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Crime Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="index.php">
                        <i class="fas fa-shield-alt me-2"></i>Cyber Crime Reporting System
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="#home">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#about">About</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#services">Services</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#contact">Contact</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="user/login.php" class="btn btn-primary text-white">User Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/login.php" class="btn btn-warning text-white">Admin</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="officer/login.php" class="btn btn-success text-white">Officer</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="py-5 text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Report Cyber Crime Safely & Securely</h1>
                    <p class="lead mb-4">Our online platform provides a secure and confidential way to report cyber crimes. Track your complaints and get updates from dedicated officers.</p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="user/register.php" class="btn btn-light btn-lg">Register Now</a>
                        <a href="user/login.php" class="btn btn-outline-light btn-lg">Login</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <i class="fas fa-shield-alt display-1"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="display-5 fw-bold">About Our System</h2>
                    <p class="lead">A comprehensive platform for cyber crime reporting and management</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-user-shield fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Secure & Confidential</h5>
                            <p class="card-text">Your identity and information are protected with advanced security measures.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Real-time Tracking</h5>
                            <p class="card-text">Track the progress of your complaint in real-time with regular updates.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Expert Officers</h5>
                            <p class="card-text">Dedicated cyber crime experts handle your cases professionally.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="display-5 fw-bold">Our Services</h2>
                    <p class="lead">Comprehensive cyber crime reporting solutions</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-laptop fa-2x text-danger mb-3"></i>
                            <h5 class="card-title">Hacking</h5>
                            <p class="card-text">Report unauthorized access to your systems or accounts.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-credit-card fa-2x text-warning mb-3"></i>
                            <h5 class="card-title">Financial Fraud</h5>
                            <p class="card-text">Report online financial scams and fraudulent activities.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-user-secret fa-2x text-info mb-3"></i>
                            <h5 class="card-title">Identity Theft</h5>
                            <p class="card-text">Report cases of identity theft and personal data misuse.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-bullhorn fa-2x text-success mb-3"></i>
                            <h5 class="card-title">Cyber Bullying</h5>
                            <p class="card-text">Report online harassment and cyber bullying incidents.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card text-center">
                        <h2>500+</h2>
                        <p>Complaints Resolved</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card text-center">
                        <h2>50+</h2>
                        <p>Expert Officers</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card text-center">
                        <h2>24/7</h2>
                        <p>Support Available</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card text-center">
                        <h2>100%</h2>
                        <p>Confidential</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="display-5 fw-bold">Contact Us</h2>
                    <p class="lead">Get in touch with our team</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                            <h5 class="card-title">Helpline</h5>
                            <p class="card-text">+91-1234567890</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-envelope fa-2x text-success mb-3"></i>
                            <h5 class="card-title">Email</h5>
                            <p class="card-text">support@cybercrime.gov.in</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-map-marker-alt fa-2x text-warning mb-3"></i>
                            <h5 class="card-title">Address</h5>
                            <p class="card-text">Cyber Crime Division, Police Headquarters</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <p>&copy; 2024 Cyber Crime Reporting System. All rights reserved.</p>
                    <p>
                        <a href="#" class="text-white me-3">Privacy Policy</a>
                        <a href="#" class="text-white me-3">Terms of Service</a>
                        <a href="#" class="text-white">Disclaimer</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
