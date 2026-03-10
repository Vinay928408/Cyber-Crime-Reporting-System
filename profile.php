<?php
require_once '../db.php';
requireLogin('user');

// Get user information
$user = getCurrentUser();
$userDetails = fetchOne("SELECT * FROM users WHERE id = ?", [$user['id']]);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    // Password change validation
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to change password";
        }
        
        if (empty($new_password)) {
            $errors[] = "New password is required";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters";
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        }
        
        // Verify current password
        if (empty($errors) && !verifyPassword($current_password, $userDetails['password'])) {
            $errors[] = "Current password is incorrect";
        }
    }
    
    // Update profile if no errors
    if (empty($errors)) {
        $updateData = [
            'name' => $name,
            'phone' => $phone,
            'address' => $address
        ];
        
        // Update password if provided
        if (!empty($new_password)) {
            $updateData['password'] = hashPassword($new_password);
        }
        
        $updated = update('users', $updateData, 'id = ?', [$user['id']]);
        
        if ($updated) {
            // Update session name
            $_SESSION['user_name'] = $name;
            
            $_SESSION['success'] = "Profile updated successfully!";
            
            // Refresh user details
            $userDetails = fetchOne("SELECT * FROM users WHERE id = ?", [$user['id']]);
        } else {
            $errors[] = "Profile update failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Cyber Crime Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="../index.php">
                        <i class="fas fa-shield-alt me-2"></i>Cyber Crime Reporting System
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="dashboard.php">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="file_complaint.php">
                                    <i class="fas fa-plus-circle me-1"></i>File Complaint
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="my_complaints.php">
                                    <i class="fas fa-list me-1"></i>My Complaints
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($user['name']); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item active" href="profile.php">
                                        <i class="fas fa-user-edit me-1"></i>Edit Profile
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                                    </a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container my-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-user-edit me-2"></i>My Profile
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-1"><?php echo $error; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <!-- Personal Information -->
                            <h5 class="mb-3">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </h5>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : htmlspecialchars($userDetails['name']); ?>" 
                                           required>
                                </div>
                                <div class="invalid-feedback">Please provide your full name</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($userDetails['email']); ?>" 
                                           readonly>
                                    <div class="form-text">Email cannot be changed. Contact admin if needed.</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : htmlspecialchars($userDetails['phone'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="address" class="form-label">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : htmlspecialchars($userDetails['address'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Password Change -->
                            <h5 class="mb-3">
                                <i class="fas fa-lock me-2"></i>Change Password
                                <small class="text-muted">(Leave blank to keep current password)</small>
                            </h5>
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('current_password', 'current-password-toggle')">
                                        <i class="fas fa-eye" id="current-password-toggle"></i>
                                    </button>
                                </div>
                                <div class="form-text">Enter current password to change password</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('new_password', 'new-password-toggle')">
                                        <i class="fas fa-eye" id="new-password-toggle"></i>
                                    </button>
                                </div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar" id="password-strength" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted" id="password-strength-text"></small>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('confirm_password', 'confirm-password-toggle')">
                                        <i class="fas fa-eye" id="confirm-password-toggle"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <p>&copy; 2024 Cyber Crime Reporting System. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword && confirmPassword && newPassword !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Password strength checker
        document.getElementById('new_password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
    </script>
</body>
</html>
