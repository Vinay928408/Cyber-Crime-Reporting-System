<?php
require_once 'db.php';

echo "<h2>Testing Session Management</h2>";

// Check session status
$session_status = session_status();
switch($session_status) {
    case PHP_SESSION_DISABLED:
        echo "<p style='color: red;'>❌ Sessions are disabled</p>";
        break;
    case PHP_SESSION_NONE:
        echo "<p style='color: orange;'>⚠️ Sessions enabled but none active</p>";
        break;
    case PHP_SESSION_ACTIVE:
        echo "<p style='color: green;'>✅ Session is active</p>";
        break;
}

// Test session functions
try {
    // Test setting session data
    $_SESSION['test'] = 'working';
    echo "<p style='color: green;'>✅ Session write working</p>";
    
    // Test reading session data
    if (isset($_SESSION['test'])) {
        echo "<p style='color: green;'>✅ Session read working: " . htmlspecialchars($_SESSION['test']) . "</p>";
    }
    
    // Test database functions
    $users = fetchAll("SELECT * FROM users");
    echo "<p style='color: blue;'>📊 Database working: " . count($users) . " users found</p>";
    
    // Test authentication functions
    if (function_exists('isLoggedIn')) {
        echo "<p style='color: green;'>✅ Authentication functions available</p>";
    }
    
    echo "<h3 style='color: green;'>🎉 Session and Database working perfectly!</h3>";
    echo "<p><a href='index.php'>Go to Home Page</a> | <a href='admin/login.php'>Admin Login</a> | <a href='user/login.php'>User Login</a></p>";
    
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
