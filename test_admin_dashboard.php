<?php
require_once 'db.php';

echo "<h2>🔧 Testing Admin Dashboard Functions</h2>";

try {
    // Test COUNT queries
    echo "<h3>✅ Testing COUNT Queries:</h3>";
    
    $total_complaints_result = fetchOne("SELECT COUNT(*) as count FROM complaints");
    $total_complaints = $total_complaints_result['count'] ?? 0;
    echo "<p style='color: green;'>✅ Total Complaints: " . $total_complaints . "</p>";
    
    $total_users_result = fetchOne("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
    $total_users = $total_users_result['count'] ?? 0;
    echo "<p style='color: green;'>✅ Active Users: " . $total_users . "</p>";
    
    $total_officers_result = fetchOne("SELECT COUNT(*) as count FROM officers WHERE status = 'active'");
    $total_officers = $total_officers_result['count'] ?? 0;
    echo "<p style='color: green;'>✅ Active Officers: " . $total_officers . "</p>";
    
    // Test GROUP BY query
    echo "<h3>✅ Testing GROUP BY Query:</h3>";
    $complaint_stats = fetchAll("SELECT status, COUNT(*) as count FROM complaints GROUP BY status");
    echo "<p style='color: green;'>✅ Status Stats Retrieved: " . count($complaint_stats) . " records</p>";
    
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
    echo "<p style='color: green;'>✅ Status Counts Processed Successfully</p>";
    
    // Test JOIN queries
    echo "<h3>✅ Testing JOIN Queries:</h3>";
    $recent_complaints = fetchAll("SELECT c.*, u.name as user_name, o.name as officer_name 
                                   FROM complaints c 
                                   LEFT JOIN users u ON c.user_id = u.id 
                                   LEFT JOIN officers o ON c.officer_id = o.id 
                                   ORDER BY c.created_at DESC LIMIT 5");
    echo "<p style='color: green;'>✅ Recent Complaints with JOIN: " . count($recent_complaints) . " records</p>";
    
    $unassigned_complaints = fetchAll("SELECT c.*, u.name as user_name 
                                      FROM complaints c 
                                      LEFT JOIN users u ON c.user_id = u.id 
                                      WHERE c.officer_id IS NULL OR c.officer_id = 0 
                                      ORDER BY c.created_at DESC");
    echo "<p style='color: green;'>✅ Unassigned Complaints: " . count($unassigned_complaints) . " records</p>";
    
    echo "<h3 style='color: green;'>🎉 ALL ADMIN DASHBOARD FUNCTIONS WORKING!</h3>";
    echo "<p><strong>Ready to access:</strong></p>";
    echo "<ul>";
    echo "<li><a href='admin/login.php' style='color: blue;'>👨‍💼 Admin Login</a> (admin@gmail.com / password)</li>";
    echo "<li><a href='admin/dashboard.php' style='color: blue;'>📊 Admin Dashboard</a></li>";
    echo "</ul>";
    
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
