<?php
require_once '../db.php';
requireLogin('admin');

// Handle officer actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitizeInput($_POST['action']);
    
    switch ($action) {
        case 'add':
            $name = sanitizeInput($_POST['name']);
            $email = sanitizeInput($_POST['email']);
            $password = $_POST['password'];
            $phone = sanitizeInput($_POST['phone']);
            $badge_number = sanitizeInput($_POST['badge_number']);
            $department = sanitizeInput($_POST['department']);

            // Validation
            $errors = [];
            
            if (empty($name)) $errors[] = "Name is required";
            if (empty($email) || !validateEmail($email)) $errors[] = "Valid email is required";
            if (empty($password)) $errors[] = "Password is required";
            if (empty($badge_number)) $errors[] = "Badge number is required";
            
            // Check if email already exists
            if (empty($errors)) {
                $existing = fetchOne("SELECT id FROM officers WHERE email = ?", [$email]);
                if ($existing) $errors[] = "Email already exists";
            }
            
            if (empty($errors)) {
                $officerData = [
                    'name' => $name,
                    'email' => $email,
                    'password' => hashPassword($password),
                    'phone' => $phone,
                    'badge_number' => $badge_number,
                    'department' => $department
                ];
                
                insert('officers', $officerData);
                $_SESSION['success'] = "Officer added successfully!";
                header('Location: officers.php');
                exit();
            }
            break;
            
        case 'toggle_status':
            $officer_id = sanitizeInput($_POST['officer_id']);
            $officer = fetchOne("SELECT status FROM officers WHERE id = ?", [$officer_id]);
            
            if ($officer) {
                $new_status = $officer['status'] === 'active' ? 'inactive' : 'active';
                update('officers', ['status' => $new_status], 'id = ?', [$officer_id]);
                $_SESSION['success'] = "Officer status updated successfully!";
            }
            header('Location: officers.php');
            exit();
            break;
            
        case 'delete':
            $officer_id = sanitizeInput($_POST['officer_id']);
            
            // Check if officer has assigned complaints
            $has_complaints = fetchOne("SELECT COUNT(*) as count FROM complaints WHERE officer_id = ?", [$officer_id]);
            
            if ($has_complaints['count'] > 0) {
                $_SESSION['error'] = "Cannot delete officer with assigned complaints. Please reassign complaints first.";
            } else {
                delete('officers', 'id = ?', [$officer_id]);
                $_SESSION['success'] = "Officer deleted successfully!";
            }
            header('Location: officers.php');
            exit();
            break;
    }
}

// Get officers with statistics
$officers = fetchAll("SELECT o.*, COUNT(c.id) as total_cases,
                             SUM(CASE WHEN c.status IN ('assigned', 'in_progress') THEN 1 ELSE 0 END) as active_cases
                             FROM officers o 
                             LEFT JOIN complaints c ON o.id = c.officer_id 
                             GROUP BY o.id 
                             ORDER BY o.name");

// Get statistics
$total_officers = count($officers);
$active_officers = count(array_filter($officers, fn($o) => $o['status'] === 'active'));
$total_cases = array_sum(array_column($officers, 'total_cases'));
$active_cases = array_sum(array_column($officers, 'active_cases'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Officers - Cyber Crime Reporting System</title>
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
                                <a class="nav-link" href="complaints.php">
                                    <i class="fas fa-list me-1"></i>Complaints
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="users.php">
                                    <i class="fas fa-users me-1"></i>Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="officers.php">
                                    <i class="fas fa-user-shield me-1"></i>Officers
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-shield me-1"></i><?php echo htmlspecialchars(getCurrentUser()['name']); ?>
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
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="fas fa-user-shield me-2"></i>Manage Officers
                        <small class="text-muted">(Total: <?php echo $total_officers; ?>)</small>
                    </h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addOfficerModal">
                        <i class="fas fa-plus me-2"></i>Add Officer
                    </button>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $total_officers; ?></h3>
                            <p>Total Officers</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $active_officers; ?></h3>
                            <p>Active Officers</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $total_cases; ?></h3>
                            <p>Total Cases</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $active_cases; ?></h3>
                            <p>Active Cases</p>
                        </div>
                    </div>
                </div>

                <!-- Officers Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Officers List</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($officers)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-user-slash fa-4x text-muted mb-3"></i>
                                <h5>No officers found</h5>
                                <p class="text-muted">No officers have been added yet.</p>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addOfficerModal">
                                    <i class="fas fa-plus me-2"></i>Add First Officer
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Badge Number</th>
                                            <th>Department</th>
                                            <th>Total Cases</th>
                                            <th>Active Cases</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($officers as $officer): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($officer['name']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($officer['email']); ?></td>
                                                <td><?php echo htmlspecialchars($officer['phone'] ?: 'Not provided'); ?></td>
                                                <td><?php echo htmlspecialchars($officer['badge_number']); ?></td>
                                                <td><?php echo htmlspecialchars($officer['department'] ?: 'Not specified'); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $officer['total_cases']; ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning"><?php echo $officer['active_cases']; ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $officer['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?php echo ucfirst($officer['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-<?php echo $officer['status'] === 'active' ? 'warning' : 'success'; ?>" 
                                                                onclick="toggleStatus(<?php echo $officer['id']; ?>)" 
                                                                title="<?php echo $officer['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>">
                                                            <i class="fas fa-<?php echo $officer['status'] === 'active' ? 'pause' : 'play'; ?>"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" 
                                                                onclick="deleteOfficer(<?php echo $officer['id']; ?>)" 
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Officer Modal -->
    <div class="modal fade" id="addOfficerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>Add New Officer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-1"><?php echo $error; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback">Please provide officer name</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">Please provide valid email</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <div class="invalid-feedback">Password must be at least 6 characters</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="badge_number" class="form-label">Badge Number *</label>
                                <input type="text" class="form-control" id="badge_number" name="badge_number" required>
                                <div class="invalid-feedback">Please provide badge number</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="department" name="department" 
                                       placeholder="e.g., Cyber Crime Division">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Add Officer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
        function toggleStatus(officerId) {
            if (confirm('Are you sure you want to toggle this officer\'s status?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="officer_id" value="${officerId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteOfficer(officerId) {
            if (confirm('Are you sure you want to delete this officer? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="officer_id" value="${officerId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
