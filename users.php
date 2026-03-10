<?php
require_once '../db.php';
requireLogin('admin');

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitizeInput($_POST['action']);
    
    switch ($action) {
        case 'toggle_status':
            $user_id = sanitizeInput($_POST['user_id']);
            $user = fetchOne("SELECT status FROM users WHERE id = ?", [$user_id]);
            
            if ($user) {
                $new_status = $user['status'] === 'active' ? 'inactive' : 'active';
                update('users', ['status' => $new_status], 'id = ?', [$user_id]);
                $_SESSION['success'] = "User status updated successfully!";
            }
            header('Location: users.php');
            exit();
            break;
            
        case 'delete':
            $user_id = sanitizeInput($_POST['user_id']);
            
            // Check if user has complaints
            $has_complaints = fetchOne("SELECT COUNT(*) as count FROM complaints WHERE user_id = ?", [$user_id]);
            
            if ($has_complaints['count'] > 0) {
                $_SESSION['error'] = "Cannot delete user with existing complaints. Please handle complaints first.";
            } else {
                delete('users', 'id = ?', [$user_id]);
                $_SESSION['success'] = "User deleted successfully!";
            }
            header('Location: users.php');
            exit();
            break;
    }
}

// Get users with statistics
$users = fetchAll("SELECT u.*, COUNT(c.id) as total_complaints,
                           SUM(CASE WHEN c.status = 'resolved' THEN 1 ELSE 0 END) as resolved_complaints
                           FROM users u 
                           LEFT JOIN complaints c ON u.id = c.user_id 
                           GROUP BY u.id 
                           ORDER BY u.created_at DESC");

// Get statistics
$total_users = count($users);
$active_users = count(array_filter($users, fn($u) => $u['status'] === 'active'));
$total_complaints = array_sum(array_column($users, 'total_complaints'));
$resolved_complaints = array_sum(array_column($users, 'resolved_complaints'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Cyber Crime Reporting System</title>
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
                                <a class="nav-link active" href="users.php">
                                    <i class="fas fa-users me-1"></i>Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="officers.php">
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
                        <i class="fas fa-users me-2"></i>Manage Users
                        <small class="text-muted">(Total: <?php echo $total_users; ?>)</small>
                    </h2>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
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
                            <h3><?php echo $total_users; ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $active_users; ?></h3>
                            <p>Active Users</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $total_complaints; ?></h3>
                            <p>Total Complaints</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $resolved_complaints; ?></h3>
                            <p>Resolved Complaints</p>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="search" class="form-label">Search Users</label>
                                <input type="text" class="form-control" id="search" placeholder="Search by name, email, or phone..." onkeyup="searchTable('usersTable', this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Users List</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($users)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-user-slash fa-4x text-muted mb-3"></i>
                                <h5>No users found</h5>
                                <p class="text-muted">No users have registered yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="usersTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Total Complaints</th>
                                            <th>Resolved</th>
                                            <th>Joined Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo htmlspecialchars($user['phone'] ?: 'Not provided'); ?></td>
                                                <td>
                                                    <small><?php echo htmlspecialchars(substr($user['address'] ?: 'Not provided', 0, 30)); ?>...</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $user['total_complaints']; ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success"><?php echo $user['resolved_complaints']; ?></span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <?php echo formatDate($user['created_at'], 'd M Y'); ?>
                                                        <br>
                                                        <small class="text-muted"><?php echo formatDate($user['created_at'], 'h:i A'); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $user['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?php echo ucfirst($user['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-<?php echo $user['status'] === 'active' ? 'warning' : 'success'; ?>" 
                                                                onclick="toggleStatus(<?php echo $user['id']; ?>)" 
                                                                title="<?php echo $user['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>">
                                                            <i class="fas fa-<?php echo $user['status'] === 'active' ? 'pause' : 'play'; ?>"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" 
                                                                onclick="deleteUser(<?php echo $user['id']; ?>)" 
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
        function toggleStatus(userId) {
            if (confirm('Are you sure you want to toggle this user\'s status?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
