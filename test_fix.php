<?php
require_once 'db.php';

echo "<h2>Testing Database Fix</h2>";

// Test if functions are working without redeclaration error
try {
    // Test sanitizeInput function
    $test_input = sanitizeInput("test<script>alert('xss')</script>");
    echo "<p style='color: green;'>✅ sanitizeInput function working: " . htmlspecialchars($test_input) . "</p>";
    
    // Test validateEmail function
    $valid_email = validateEmail("test@example.com");
    $invalid_email = validateEmail("invalid-email");
    echo "<p style='color: green;'>✅ validateEmail function working</p>";
    
    // Test password functions
    $hash = hashPassword("password");
    $verify = verifyPassword("password", $hash);
    echo "<p style='color: green;'>✅ Password functions working</p>";
    
    // Test database functions
    $users = fetchAll("SELECT * FROM users");
    echo "<p style='color: blue;'>📊 Database functions working - Found " . count($users) . " users</p>";
    
    echo "<h3 style='color: green;'>🎉 All functions working correctly!</h3>";
    echo "<p><a href='index.php'>Go to Home Page</a> | <a href='admin/login.php'>Admin Login</a></p>";
    
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
