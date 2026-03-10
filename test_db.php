<?php
require_once 'db.php';

// Test database connection
echo "<h2>Database Connection Test</h2>";

try {
    // Test user login
    $user = fetchOne("SELECT * FROM users WHERE email = ?", ['ram@gmail.com']);
    if ($user) {
        echo "<p style='color: green;'>✅ User database working: Found user " . htmlspecialchars($user['name']) . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ User not found, but database is accessible</p>";
    }

    // Test officer login
    $officer = fetchOne("SELECT * FROM officers WHERE email = ?", ['officer1@gmail.com']);
    if ($officer) {
        echo "<p style='color: green;'>✅ Officer database working: Found officer " . htmlspecialchars($officer['name']) . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Officer not found, but database is accessible</p>";
    }

    // Test admin login
    $admin = fetchOne("SELECT * FROM admin WHERE email = ?", ['admin@gmail.com']);
    if ($admin) {
        echo "<p style='color: green;'>✅ Admin database working: Found admin " . htmlspecialchars($admin['name']) . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Admin not found, but database is accessible</p>";
    }

    // Test complaints
    $complaints = fetchAll("SELECT * FROM complaints");
    echo "<p style='color: blue;'>📊 Found " . count($complaints) . " complaints in database</p>";

    echo "<h3 style='color: green;'>🎉 Database connection successful!</h3>";
    echo "<p><a href='index.php'>Go to Home Page</a></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
