<?php
require_once '../db.php';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    // Validation
    $errors = [];
    
    if (empty($email) || !validateEmail($email)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    // Authenticate user if no errors
    if (empty($errors)) {
        $user = fetchOne("SELECT * FROM users WHERE email = ? AND status = 'active'", [$email]);
        
        if ($user && verifyPassword($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = 'user';
            
            header('Location: dashboard.php');
            exit();
        } else {
            $errors[] = "Invalid email or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - Cyber Crime Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="auth-card fade-in">
                    <div class="auth-header">
                        <h3><i class="fas fa-sign-in-alt me-2"></i>User Login</h3>
                        <p class="mb-0">Access your account to manage complaints</p>
                    </div>
                    <div class="auth-body">
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-1"><?php echo $error; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                           required autofocus>
                                </div>
                                <div class="invalid-feedback">Please provide your email address</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password', 'password-toggle')">
                                        <i class="fas fa-eye" id="password-toggle"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Please provide your password</div>
                            </div>
                            
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>
                                <a href="#" class="text-muted">Forgot Password?</a>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p class="mb-0">Don't have an account? <a href="register.php">Register here</a></p>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="../index.php" class="text-muted">
                                <i class="fas fa-arrow-left me-1"></i>Back to Home
                            </a>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <p class="text-muted mb-2">Login as:</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="../admin/login.php" class="btn btn-warning btn-sm">
                                    <i class="fas fa-user-shield me-1"></i>Admin
                                </a>
                                <a href="../officer/login.php" class="btn btn-success btn-sm">
                                    <i class="fas fa-badge me-1"></i>Officer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
