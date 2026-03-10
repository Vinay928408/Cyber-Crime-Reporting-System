<?php
/**
 * Database Connection File
 * Cyber Crime Reporting System
 */

// Prevent multiple session starts with error suppression
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

// Try MySQL first, fallback to simple file-based database
try {
    // Database configuration
    $host = 'localhost';
    $dbname = 'cybercrime_db';
    $username = 'root';
    $password = '';

    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // MySQL helper functions
    function executeQuery($sql, $params = []) {
        global $pdo;
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
    }

    function fetchAll($sql, $params = []) {
        $stmt = executeQuery($sql, $params);
        return $stmt->fetchAll();
    }

    function fetchOne($sql, $params = []) {
        $stmt = executeQuery($sql, $params);
        return $stmt->fetch();
    }

    function insert($table, $data) {
        global $pdo;
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $values = array_values($data);
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        executeQuery($sql, $values);
        return $pdo->lastInsertId();
    }

    function update($table, $data, $where, $whereParams = []) {
        global $pdo;
        $setClause = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setClause[] = "$column = ?";
            $params[] = $value;
        }
        
        $params = array_merge($params, $whereParams);
        $sql = "UPDATE $table SET " . implode(', ', $setClause) . " WHERE $where";
        
        executeQuery($sql, $params);
        return true;
    }

    function delete($table, $where, $params = []) {
        global $pdo;
        $sql = "DELETE FROM $table WHERE $where";
        executeQuery($sql, $params);
        return true;
    }

} catch(PDOException $e) {
    // Fallback to simple file-based database
    require_once 'db_simple.php';
}

// Security functions
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Check if user is logged in
function isLoggedIn($role = null) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    if ($role && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role)) {
        return false;
    }
    
    return true;
}

// Redirect if not logged in
function requireLogin($role = null, $redirectUrl = null) {
    if (!isLoggedIn($role)) {
        $redirectUrl = $redirectUrl ?: '../index.php';
        header("Location: $redirectUrl");
        exit();
    }
}

// Get current user info
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'] ?? 'user'
        ];
    }
    return null;
}

// Logout function
function logout() {
    session_destroy();
    header('Location: ../index.php');
    exit();
}

// Date formatting function
function formatDate($date, $format = 'd M Y, h:i A') {
    return date($format, strtotime($date));
}

// Generate random string
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
?>
