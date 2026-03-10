<?php
require_once '../db.php';
requireLogin('admin');

// Get admin information
$admin = getCurrentUser();

// Get dashboard statistics
$total_complaints_result = fetchOne("SELECT COUNT(*) as count FROM complaints");
$total_complaints = $total_complaints_result['count'] ?? 0;

$total_users_result = fetchOne("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
$total_users = $total_users_result['count'] ?? 0;

$total_officers_result = fetchOne("SELECT COUNT(*) as count FROM officers WHERE status = 'active'");
$total_officers = $total_officers_result['count'] ?? 0;

// Get complaints by status
$complaint_stats = fetchAll("SELECT status, COUNT(*) as count FROM complaints GROUP BY status");
$status_counts = [
    'pending' => 0,
    'assigned' => 0,
    'in_progress' => 0,
    'resolved' => 0,
    'closed' => 0
];
foreach ($complaint_stats as $stat) {
    if (isset($stat['status']) && isset($stat['count'])) {
        $status_counts[$stat['status']] = $stat['count'];
    }
}

// Get recent complaints
$recent_complaints = fetchAll("SELECT c.*, u.name as user_name, o.name as officer_name 
                               FROM complaints c 
                               LEFT JOIN users u ON c.user_id = u.id 
                               LEFT JOIN officers o ON c.officer_id = o.id 
                               ORDER BY c.created_at DESC LIMIT 5");

// Get unassigned complaints
$unassigned_complaints = fetchAll("SELECT c.*, u.name as user_name 
                                   FROM complaints c 
                                   LEFT JOIN users u ON c.user_id = u.id 
                                   WHERE c.status = 'pending' 
                                   ORDER BY c.created_at DESC LIMIT 5");

// Get active officers
$active_officers = fetchAll("SELECT o.*, COUNT(c.id) as active_cases 
                             FROM officers o 
                             LEFT JOIN complaints c ON o.id = c.officer_id AND c.status IN ('assigned', 'in_progress')
                             WHERE o.status = 'active' 
                             GROUP BY o.id 
                             ORDER BY active_cases DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Cyber Crime Reporting System</title>
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
                                <a class="nav-link active" href="dashboard.php">
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
                                <a class="nav-link" href="officers.php">
                                    <i class="fas fa-user-shield me-1"></i>Officers
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-shield me-1"></i><?php echo htmlspecialchars($admin['name']); ?>
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
                <h2 class="mb-4">
                    <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                    <small class="text-muted">Welcome, <?php echo htmlspecialchars($admin['name']); ?></small>
                </h2>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <h3><?php echo $total_complaints; ?></h3>
                    <p>Total Complaints</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Active Users</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <h3><?php echo $total_officers; ?></h3>
                    <p>Active Officers</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <h3><?php echo $status_counts['pending']; ?></h3>
                    <p>Pending Assignment</p>
                </div>
            </div>
        </div>

        <!-- Complaint Status Overview -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Complaint Status Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="mb-3">
                                    <h4 class="text-warning"><?php echo $status_counts['pending']; ?></h4>
                                    <small>Pending</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <h4 class="text-info"><?php echo $status_counts['assigned']; ?></h4>
                                    <small>Assigned</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <h4 class="text-primary"><?php echo $status_counts['in_progress']; ?></h4>
                                    <small>In Progress</small>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="text-success"><?php echo $status_counts['resolved']; ?></h4>
                                    <small>Resolved</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="text-secondary"><?php echo $status_counts['closed']; ?></h4>
                                    <small>Closed</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 mb-2">
                                <a href="complaints.php?status=pending" class="btn btn-warning btn-lg w-100">
                                    <i class="fas fa-clipboard-list me-2"></i>Assign Complaints
                                </a>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <a href="officers.php" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-user-shield me-2"></i>Manage Officers
                                </a>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <a href="users.php" class="btn btn-info btn-lg w-100">
                                    <i class="fas fa-users me-2"></i>Manage Users
                                </a>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <a href="complaints.php" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-list me-2"></i>All Complaints
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Unassigned Complaints -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Unassigned Complaints
                            <span class="badge bg-warning"><?php echo count($unassigned_complaints); ?></span>
                        </h5>
                        <a href="complaints.php?status=pending" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($unassigned_complaints)): ?>
                            <div class="text-center py-3">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <p class="mb-0 text-muted">No pending complaints to assign</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($unassigned_complaints as $complaint): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($complaint['title'] ?? 'Untitled Complaint'); ?></h6>
                                            <small class="text-muted">
                                                By: <?php echo htmlspecialchars($complaint['user_name'] ?? 'Unknown User'); ?> | 
                                                <?php echo formatDate($complaint['created_at'] ?? date('Y-m-d H:i:s')); ?>
                                            </small>
                                        </div>
                                        <a href="assign_complaint.php?id=<?php echo $complaint['id'] ?? 0; ?>" 
                                           class="btn btn-sm btn-primary">Assign</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Complaints -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Recent Complaints
                        </h5>
                        <a href="complaints.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_complaints)): ?>
                            <div class="text-center py-3">
                                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                <p class="mb-0 text-muted">No complaints filed yet</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recent_complaints as $complaint): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($complaint['title'] ?? 'Untitled Complaint'); ?></h6>
                                                <small class="text-muted">
                                                    By: <?php echo htmlspecialchars($complaint['user_name'] ?? 'Unknown User'); ?> | 
                                                    <?php echo formatDate($complaint['created_at'] ?? date('Y-m-d H:i:s')); ?>
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge badge-<?php echo $complaint['status'] ?? 'pending'; ?> mb-1">
                                                    <?php echo ucfirst(str_replace('_', ' ', $complaint['status'] ?? 'pending')); ?>
                                                </span>
                                                <?php if (!empty($complaint['officer_name'])): ?>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($complaint['officer_name']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Officers -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-user-shield me-2"></i>Active Officers
                        </h5>
                        <a href="officers.php" class="btn btn-sm btn-outline-primary">Manage Officers</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($active_officers)): ?>
                            <div class="text-center py-3">
                                <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                                <p class="mb-0 text-muted">No active officers found</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Officer Name</th>
                                            <th>Badge Number</th>
                                            <th>Department</th>
                                            <th>Active Cases</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($active_officers as $officer): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($officer['name']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($officer['email']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($officer['badge_number']); ?></td>
                                                <td><?php echo htmlspecialchars($officer['department'] ?: 'Not specified'); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $officer['active_cases']; ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Active</span>
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
</body>
</html>
