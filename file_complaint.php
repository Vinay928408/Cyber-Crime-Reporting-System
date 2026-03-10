<?php
require_once '../db.php';
requireLogin('user');

// Handle complaint submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $type = sanitizeInput($_POST['type']);
    $description = sanitizeInput($_POST['description']);
    $priority = sanitizeInput($_POST['priority']);
    $user_id = getCurrentUser()['id'];

    // Validation
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Complaint title is required";
    }
    
    if (empty($type)) {
        $errors[] = "Complaint type is required";
    }
    
    if (empty($description)) {
        $errors[] = "Complaint description is required";
    } elseif (strlen($description) < 20) {
        $errors[] = "Description must be at least 20 characters";
    }
    
    // Submit complaint if no errors
    if (empty($errors)) {
        $complaintData = [
            'user_id' => $user_id,
            'title' => $title,
            'type' => $type,
            'description' => $description,
            'priority' => $priority,
            'status' => 'pending'
        ];
        
        $complaintId = insert('complaints', $complaintData);
        
        if ($complaintId) {
            $_SESSION['success'] = "Complaint filed successfully! Your complaint ID is #" . str_pad($complaintId, 6, '0', STR_PAD_LEFT);
            header('Location: my_complaints.php');
            exit();
        } else {
            $errors[] = "Failed to file complaint. Please try again.";
        }
    }
}

// Get complaint types
$complaintTypes = [
    'Hacking' => 'Unauthorized access to computer systems',
    'Financial Fraud' => 'Online financial scams and fraud',
    'Identity Theft' => 'Theft of personal information',
    'Cyber Bullying' => 'Online harassment and bullying',
    'Phishing' => 'Fraudulent attempts to obtain sensitive information',
    'Malware Attack' => 'Malicious software attacks',
    'Data Breach' => 'Unauthorized access to personal/corporate data',
    'Online Threats' => 'Threats made through online platforms',
    'Social Media Fraud' => 'Fraudulent activities on social media',
    'Other' => 'Other cyber crimes'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Complaint - Cyber Crime Reporting System</title>
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
                                <a class="nav-link active" href="file_complaint.php">
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
                                    <i class="fas fa-user me-1"></i><?php echo htmlspecialchars(getCurrentUser()['name']); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="profile.php">
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
                            <i class="fas fa-plus-circle me-2"></i>File New Complaint
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-1"><?php echo $error; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Important:</strong> Please provide accurate and detailed information. False complaints may lead to legal action.
                        </div>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="title" class="form-label">Complaint Title *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                                       required maxlength="200">
                                <div class="form-text">Brief title describing the incident</div>
                                <div class="invalid-feedback">Please provide a complaint title</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="type" class="form-label">Complaint Type *</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Select complaint type</option>
                                    <?php foreach ($complaintTypes as $type => $description): ?>
                                        <option value="<?php echo $type; ?>" 
                                                <?php echo (isset($_POST['type']) && $_POST['type'] === $type) ? 'selected' : ''; ?>>
                                            <?php echo $type; ?> - <?php echo $description; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a complaint type</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority Level</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="low" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'low') ? 'selected' : ''; ?>>
                                        Low - Minor issue
                                    </option>
                                    <option value="medium" <?php echo (!isset($_POST['priority']) || $_POST['priority'] === 'medium') ? 'selected' : ''; ?>>
                                        Medium - Moderate issue
                                    </option>
                                    <option value="high" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'high') ? 'selected' : ''; ?>>
                                        High - Serious issue
                                    </option>
                                </select>
                                <div class="form-text">Select the severity of the incident</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Detailed Description *</label>
                                <textarea class="form-control" id="description" name="description" rows="6" 
                                          required maxlength="2000"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                                <div class="form-text">
                                    Provide detailed information about the incident including:
                                    <ul class="mb-0 mt-2">
                                        <li>When and where it happened</li>
                                        <li>How it happened</li>
                                        <li>Who was involved (if known)</li>
                                        <li>Any evidence or witnesses</li>
                                        <li>Impact or damage caused</li>
                                    </ul>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <div class="invalid-feedback">Please provide a detailed description (minimum 20 characters)</div>
                                    <small class="text-muted" id="description-counter">0 / 2000</small>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Declaration:</strong> I hereby declare that the information provided is true to the best of my knowledge. I understand that filing a false complaint is a punishable offense.
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="declaration" required>
                                    <label class="form-check-label" for="declaration">
                                        I agree to the above declaration
                                    </label>
                                    <div class="invalid-feedback">You must agree to the declaration to proceed</div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Complaint
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
        // Character counter for description
        document.getElementById('description').addEventListener('input', updateCharacterCounter);
        
        // Initialize character counter
        updateCharacterCounter.call(document.getElementById('description'));
    </script>
</body>
</html>
