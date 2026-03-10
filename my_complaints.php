<?php
require_once '../db.php';
requireLogin('user');

// Get user information
$user = getCurrentUser();

// Handle status filter
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : 'all';
$where_clause = "WHERE c.user_id = ?";
$params = [$user['id']];

if ($status_filter !== 'all') {
    $where_clause .= " AND c.status = ?";
    $params[] = $status_filter;
}

// Get user's complaints with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get total complaints count
$total_query = "SELECT COUNT(*) as total FROM complaints c $where_clause";
$total_result = fetchOne($total_query, $params);
$total_complaints = $total_result['total'];
$total_pages = ceil($total_complaints / $per_page);

// Get complaints for current page
$complaints = fetchAll("SELECT c.*, o.name as officer_name, o.badge_number 
                        FROM complaints c 
                        LEFT JOIN officers o ON c.officer_id = o.id 
                        $where_clause 
                        ORDER BY c.created_at DESC 
                        LIMIT $per_page OFFSET $offset", $params);

// Get complaint statistics
$all_complaints = fetchAll("SELECT status, COUNT(*) as count FROM complaints WHERE user_id = ? GROUP BY status", [$user['id']]);
$stats = [
    'total' => $total_complaints,
    'pending' => 0,
    'assigned' => 0,
    'in_progress' => 0,
    'resolved' => 0,
    'closed' => 0
];

foreach ($all_complaints as $stat) {
    $stats[$stat['status']] = $stat['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Complaints - Cyber Crime Reporting System</title>
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
                                <a class="nav-link active" href="my_complaints.php">
                                    <i class="fas fa-list me-1"></i>My Complaints
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($user['name']); ?>
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
                        <i class="fas fa-list me-2"></i>My Complaints
                        <small class="text-muted">(Total: <?php echo $total_complaints; ?>)</small>
                    </h2>
                    <a href="file_complaint.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>File New Complaint
                    </a>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $stats['total']; ?></h3>
                            <p>Total</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $stats['pending']; ?></h3>
                            <p>Pending</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $stats['assigned']; ?></h3>
                            <p>Assigned</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $stats['in_progress']; ?></h3>
                            <p>In Progress</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $stats['resolved']; ?></h3>
                            <p>Resolved</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $stats['closed']; ?></h3>
                            <p>Closed</p>
                        </div>
                    </div>
                </div>

                <!-- Filter and Search -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="status_filter" class="form-label">Filter by Status</label>
                                <select class="form-select" id="status_filter" onchange="filterByStatus(this.value)">
                                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="assigned" <?php echo $status_filter === 'assigned' ? 'selected' : ''; ?>>Assigned</option>
                                    <option value="in_progress" <?php echo $status_filter === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="resolved" <?php echo $status_filter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                    <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </div>
                            <div class="col-lg-8">
                                <label for="search" class="form-label">Search Complaints</label>
                                <input type="text" class="form-control" id="search" placeholder="Search by title, type, or description..." onkeyup="searchTable('complaintsTable', this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complaints Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Complaint List</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($complaints)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                <h5>No complaints found</h5>
                                <p class="text-muted">
                                    <?php if ($status_filter !== 'all'): ?>
                                        No complaints with status "<?php echo ucfirst(str_replace('_', ' ', $status_filter)); ?>" found.
                                        <a href="my_complaints.php?status=all" class="btn btn-sm btn-outline-primary mt-2">View All Complaints</a>
                                    <?php else: ?>
                                        You haven't filed any complaints yet.
                                        <a href="file_complaint.php" class="btn btn-primary mt-2">File Your First Complaint</a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="complaintsTable">
                                    <thead>
                                        <tr>
                                            <th>Complaint ID</th>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Officer</th>
                                            <th>Created</th>
                                            <th>Updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($complaints as $complaint): ?>
                                            <tr class="complaint-card <?php echo $complaint['status']; ?>">
                                                <td>
                                                    <strong>#<?php echo str_pad($complaint['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($complaint['title']); ?></strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <?php echo substr(htmlspecialchars($complaint['description']), 0, 80); ?>...
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($complaint['type']); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?php echo $complaint['status']; ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge priority-<?php echo $complaint['priority']; ?>">
                                                        <?php echo ucfirst($complaint['priority']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($complaint['officer_name']): ?>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($complaint['officer_name']); ?></strong>
                                                            <br>
                                                            <small class="text-muted"><?php echo htmlspecialchars($complaint['badge_number']); ?></small>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not Assigned</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div>
                                                        <?php echo formatDate($complaint['created_at'], 'd M Y'); ?>
                                                        <br>
                                                        <small class="text-muted"><?php echo formatDate($complaint['created_at'], 'h:i A'); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <?php echo formatDate($complaint['updated_at'], 'd M Y'); ?>
                                                        <br>
                                                        <small class="text-muted"><?php echo formatDate($complaint['updated_at'], 'h:i A'); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="view_complaint.php?id=<?php echo $complaint['id']; ?>" 
                                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if ($complaint['status'] === 'resolved' || $complaint['status'] === 'closed'): ?>
                                                            <button class="btn btn-sm btn-outline-success" onclick="printComplaint(<?php echo $complaint['id']; ?>)" title="Print">
                                                                <i class="fas fa-print"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Complaint pagination">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>">Previous</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>">Next</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
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
        function filterByStatus(status) {
            window.location.href = `my_complaints.php?status=${status}`;
        }
        
        function printComplaint(complaintId) {
            window.open(`view_complaint.php?id=${complaintId}&print=1`, '_blank');
        }
    </script>
</body>
</html>
