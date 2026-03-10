<?php
require_once '../db.php';
requireLogin('user');

// Get complaint ID
$complaint_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$complaint_id) {
    $_SESSION['error'] = "Invalid complaint ID";
    header('Location: my_complaints.php');
    exit();
}

// Get complaint details
$complaint = fetchOne("SELECT c.*, o.name as officer_name, o.badge_number, o.department, 
                               u.name as user_name, u.email as user_email, u.phone as user_phone
                        FROM complaints c 
                        LEFT JOIN officers o ON c.officer_id = o.id 
                        LEFT JOIN users u ON c.user_id = u.id 
                        WHERE c.id = ? AND c.user_id = ?", [$complaint_id, getCurrentUser()['id']]);

if (!$complaint) {
    $_SESSION['error'] = "Complaint not found or you don't have permission to view it";
    header('Location: my_complaints.php');
    exit();
}

// Get complaint updates
$updates = fetchAll("SELECT cu.*, o.name as officer_name, o.badge_number 
                     FROM complaint_updates cu 
                     LEFT JOIN officers o ON cu.officer_id = o.id 
                     WHERE cu.complaint_id = ? 
                     ORDER BY cu.created_at DESC", [$complaint_id]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaint - Cyber Crime Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { padding: 20px; }
            .card { border: 1px solid #000 !important; }
        }
        .print-only { display: none; }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header no-print">
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
            <div class="col-lg-12">
                <!-- Print Header -->
                <div class="print-only text-center mb-4">
                    <h1>Cyber Crime Reporting System</h1>
                    <h3>Complaint Details</h3>
                </div>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="no-print">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="my_complaints.php">My Complaints</a></li>
                        <li class="breadcrumb-item active">View Complaint</li>
                    </ol>
                </nav>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                    <h2>
                        <i class="fas fa-eye me-2"></i>Complaint Details
                        <small class="text-muted">ID: #<?php echo str_pad($complaint['id'], 6, '0', STR_PAD_LEFT); ?></small>
                    </h2>
                    <div class="btn-group">
                        <a href="my_complaints.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                        <?php if ($complaint['status'] === 'resolved' || $complaint['status'] === 'closed'): ?>
                            <button class="btn btn-success" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Print
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show no-print">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Complaint Details Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>Complaint Information
                        </h5>
                        <div>
                            <span class="badge badge-<?php echo $complaint['status']; ?> me-2">
                                <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                            </span>
                            <span class="badge priority-<?php echo $complaint['priority']; ?>">
                                <?php echo ucfirst($complaint['priority']); ?> Priority
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Complaint ID:</strong></td>
                                        <td>#<?php echo str_pad($complaint['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Title:</strong></td>
                                        <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type:</strong></td>
                                        <td><?php echo htmlspecialchars($complaint['type']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge badge-<?php echo $complaint['status']; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Priority:</strong></td>
                                        <td>
                                            <span class="badge priority-<?php echo $complaint['priority']; ?>">
                                                <?php echo ucfirst($complaint['priority']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-lg-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Filed Date:</strong></td>
                                        <td><?php echo formatDate($complaint['created_at']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Updated:</strong></td>
                                        <td><?php echo formatDate($complaint['updated_at']); ?></td>
                                    </tr>
                                    <?php if ($complaint['assigned_at']): ?>
                                        <tr>
                                            <td><strong>Assigned Date:</strong></td>
                                            <td><?php echo formatDate($complaint['assigned_at']); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if ($complaint['resolved_at']): ?>
                                        <tr>
                                            <td><strong>Resolved Date:</strong></td>
                                            <td><?php echo formatDate($complaint['resolved_at']); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td><strong>Assigned Officer:</strong></td>
                                        <td>
                                            <?php if ($complaint['officer_name']): ?>
                                                <?php echo htmlspecialchars($complaint['officer_name']); ?>
                                                <br>
                                                <small class="text-muted">
                                                    Badge: <?php echo htmlspecialchars($complaint['badge_number']); ?>
                                                    <?php if ($complaint['department']): ?>
                                                        | <?php echo htmlspecialchars($complaint['department']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">Not Assigned</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-12">
                                <h6><strong>Description:</strong></h6>
                                <div class="p-3 bg-light rounded">
                                    <?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complainant Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Complainant Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <p><strong>Name:</strong><br><?php echo htmlspecialchars($complaint['user_name']); ?></p>
                            </div>
                            <div class="col-lg-4">
                                <p><strong>Email:</strong><br><?php echo htmlspecialchars($complaint['user_email']); ?></p>
                            </div>
                            <div class="col-lg-4">
                                <p><strong>Phone:</strong><br><?php echo htmlspecialchars($complaint['user_phone'] ?: 'Not provided'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complaint Updates Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Progress Updates
                            <?php if (!empty($updates)): ?>
                                <small class="text-muted">(<?php echo count($updates); ?> updates)</small>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($updates)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                <h6>No updates yet</h6>
                                <p class="text-muted">
                                    <?php if ($complaint['status'] === 'pending'): ?>
                                        Your complaint is pending assignment to an officer.
                                    <?php elseif ($complaint['status'] === 'assigned'): ?>
                                        Your complaint has been assigned to an officer. Updates will appear here soon.
                                    <?php else: ?>
                                        No progress updates have been recorded for this complaint.
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="timeline">
                                <?php foreach ($updates as $update): ?>
                                    <div class="timeline-item">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <i class="fas fa-user-shield me-2"></i>
                                                            <?php echo htmlspecialchars($update['officer_name']); ?>
                                                            <small class="text-muted">
                                                                (Badge: <?php echo htmlspecialchars($update['badge_number']); ?>)
                                                            </small>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>
                                                            <?php echo formatDate($update['created_at']); ?>
                                                        </small>
                                                    </div>
                                                    <span class="badge bg-primary">
                                                        <?php echo htmlspecialchars($update['status']); ?>
                                                    </span>
                                                </div>
                                                
                                                <div class="mb-2">
                                                    <strong>Notes:</strong>
                                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($update['notes'])); ?></p>
                                                </div>
                                                
                                                <?php if ($update['remarks']): ?>
                                                    <div>
                                                        <strong>Remarks:</strong>
                                                        <p class="mb-0 text-muted"><?php echo nl2br(htmlspecialchars($update['remarks'])); ?></p>
                                                    </div>
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
    </main>

    <!-- Footer -->
    <footer class="footer mt-5 no-print">
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
