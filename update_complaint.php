<?php
require_once '../db.php';
requireLogin('officer');

// Get complaint ID
$complaint_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$complaint_id) {
    $_SESSION['error'] = "Invalid complaint ID";
    header('Location: my_complaints.php');
    exit();
}

// Get complaint details
$complaint = fetchOne("SELECT c.*, u.name as user_name, u.email as user_email, u.phone as user_phone
                        FROM complaints c 
                        LEFT JOIN users u ON c.user_id = u.id 
                        WHERE c.id = ? AND c.officer_id = ?", [$complaint_id, getCurrentUser()['id']]);

if (!$complaint) {
    $_SESSION['error'] = "Complaint not found or not assigned to you";
    header('Location: my_complaints.php');
    exit();
}

// Get previous updates
$updates = fetchAll("SELECT cu.*, o.name as officer_name, o.badge_number 
                     FROM complaint_updates cu 
                     LEFT JOIN officers o ON cu.officer_id = o.id 
                     WHERE cu.complaint_id = ? 
                     ORDER BY cu.created_at DESC", [$complaint_id]);

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = sanitizeInput($_POST['status']);
    $notes = sanitizeInput($_POST['notes']);
    $remarks = sanitizeInput($_POST['remarks']);

    // Validation
    $errors = [];
    
    if (empty($status)) {
        $errors[] = "Status is required";
    }
    
    if (empty($notes)) {
        $errors[] = "Progress notes are required";
    }
    
    // Update complaint if no errors
    if (empty($errors)) {
        try {
            // Update complaint status
            $updateData = ['status' => $status];
            
            if ($status === 'resolved') {
                $updateData['resolved_at'] = date('Y-m-d H:i:s');
            }
            
            update('complaints', $updateData, 'id = ?', [$complaint_id]);
            
            // Add new update
            $updateRecord = [
                'complaint_id' => $complaint_id,
                'officer_id' => getCurrentUser()['id'],
                'status' => $status,
                'notes' => $notes,
                'remarks' => $remarks
            ];
            
            insert('complaint_updates', $updateRecord);
            
            $_SESSION['success'] = "Complaint updated successfully!";
            header('Location: view_complaint.php?id=' . $complaint_id);
            exit();
            
        } catch (Exception $e) {
            $errors[] = "Update failed: " . $e->getMessage();
        }
    }
}

// Define status options based on current status
$status_options = [
    'assigned' => ['assigned', 'in_progress'],
    'in_progress' => ['in_progress', 'resolved'],
    'resolved' => ['resolved'],
    'closed' => ['closed']
];

$current_status_options = $status_options[$complaint['status']] ?? ['assigned'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Complaint - Cyber Crime Reporting System</title>
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
                                <a class="nav-link" href="my_complaints.php">
                                    <i class="fas fa-list me-1"></i>My Complaints
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-badge me-1"></i><?php echo htmlspecialchars(getCurrentUser()['name']); ?>
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
            <div class="col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Update Complaint Progress
                            <small class="text-muted">ID: #<?php echo str_pad($complaint['id'], 6, '0', STR_PAD_LEFT); ?></small>
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

                        <!-- Complaint Details -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Complaint Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ID:</strong> #<?php echo str_pad($complaint['id'], 6, '0', STR_PAD_LEFT); ?></p>
                                    <p><strong>Title:</strong> <?php echo htmlspecialchars($complaint['title']); ?></p>
                                    <p><strong>Type:</strong> <?php echo htmlspecialchars($complaint['type']); ?></p>
                                    <p><strong>Priority:</strong> 
                                        <span class="badge priority-<?php echo $complaint['priority']; ?>">
                                            <?php echo ucfirst($complaint['priority']); ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Current Status:</strong> 
                                        <span class="badge badge-<?php echo $complaint['status']; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                                        </span>
                                    </p>
                                    <p><strong>Filed by:</strong> <?php echo htmlspecialchars($complaint['user_name']); ?></p>
                                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($complaint['user_email']); ?></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($complaint['user_phone'] ?: 'Not provided'); ?></p>
                                </div>
                            </div>
                            <p><strong>Description:</strong></p>
                            <div class="p-3 bg-light rounded">
                                <?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
                            </div>
                        </div>

                        <!-- Previous Updates -->
                        <?php if (!empty($updates)): ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Previous Updates</h6>
                                </div>
                                <div class="card-body">
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
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Update Status *</label>
                                    <select class="form-select" id="status" name="status" required onchange="updateStatusDescription()">
                                        <?php foreach ($current_status_options as $option): ?>
                                            <option value="<?php echo $option; ?>" 
                                                    <?php echo (isset($_POST['status']) && $_POST['status'] === $option) ? 'selected' : ''; ?>>
                                                <?php 
                                                $status_labels = [
                                                    'assigned' => 'Assigned (Under Review)',
                                                    'in_progress' => 'In Progress (Investigation)',
                                                    'resolved' => 'Resolved (Case Closed)'
                                                ];
                                                echo $status_labels[$option] ?? ucfirst(str_replace('_', ' ', $option));
                                                ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a status</div>
                                    <div class="form-text" id="statusDescription"></div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="remarks" class="form-label">Internal Remarks</label>
                                    <input type="text" class="form-control" id="remarks" name="remarks" 
                                           value="<?php echo isset($_POST['remarks']) ? htmlspecialchars($_POST['remarks']) : ''; ?>"
                                           placeholder="Optional internal notes for admin">
                                    <div class="form-text">Internal remarks visible only to admin</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Progress Notes *</label>
                                <textarea class="form-control" id="notes" name="notes" rows="6" required
                                          placeholder="Provide detailed information about the current progress..."><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                                <div class="form-text">
                                    This will be visible to the complainant. Include:
                                    <ul class="mb-0 mt-2">
                                        <li>Current investigation status</li>
                                        <li>Actions taken so far</li>
                                        <li>Next steps or timeline</li>
                                        <li>Any additional information needed</li>
                                    </ul>
                                </div>
                                <div class="invalid-feedback">Please provide progress notes</div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Update Progress
                                </button>
                                <a href="view_complaint.php?id=<?php echo $complaint_id; ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <a href="my_complaints.php" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-2"></i>Back to My Complaints
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
        const statusDescriptions = {
            'assigned': 'Complaint is under initial review and assessment.',
            'in_progress': 'Active investigation is underway.',
            'resolved': 'Case has been successfully resolved and closed.'
        };

        function updateStatusDescription() {
            const status = document.getElementById('status').value;
            const descriptionElement = document.getElementById('statusDescription');
            
            if (statusDescriptions[status]) {
                descriptionElement.textContent = statusDescriptions[status];
                descriptionElement.className = 'form-text text-info';
            } else {
                descriptionElement.textContent = '';
            }
        }

        // Initialize status description on page load
        updateStatusDescription();
    </script>
</body>
</html>
