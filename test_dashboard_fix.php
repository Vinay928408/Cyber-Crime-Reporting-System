<?php
require_once 'db.php';

echo "<h2>🔧 Testing Dashboard Data Issues</h2>";

try {
    // Test active officers query
    echo "<h3>✅ Testing Active Officers Query:</h3>";
    $active_officers = fetchAll("SELECT o.*, COUNT(c.id) as active_cases 
                                 FROM officers o 
                                 LEFT JOIN complaints c ON o.id = c.officer_id AND c.status IN ('assigned', 'in_progress')
                                 WHERE o.status = 'active' 
                                 GROUP BY o.id 
                                 ORDER BY active_cases DESC LIMIT 5");
    
    echo "<p style='color: green;'>✅ Active Officers Retrieved: " . count($active_officers) . "</p>";
    
    // Test unassigned complaints query
    echo "<h3>✅ Testing Unassigned Complaints Query:</h3>";
    $unassigned_complaints = fetchAll("SELECT c.*, u.name as user_name 
                                       FROM complaints c 
                                       LEFT JOIN users u ON c.user_id = u.id 
                                       WHERE c.status = 'pending' 
                                       ORDER BY c.created_at DESC LIMIT 5");
    
    echo "<p style='color: green;'>✅ Unassigned Complaints: " . count($unassigned_complaints) . "</p>";
    
    // Show sample data structure
    if (!empty($unassigned_complaints)) {
        echo "<h4>Sample Complaint Data:</h4>";
        echo "<pre>" . htmlspecialchars(print_r($unassigned_complaints[0], true)) . "</pre>";
    }
    
    if (!empty($active_officers)) {
        echo "<h4>Sample Officer Data:</h4>";
        echo "<pre>" . htmlspecialchars(print_r($active_officers[0], true)) . "</pre>";
    }
    
    echo "<h3 style='color: green;'>🎉 Data Retrieved Successfully!</h3>";
    echo "<p><strong>Solution:</strong> The dashboard needs null checks for empty data.</p>";
    
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
