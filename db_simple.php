<?php
/**
 * Simple File-Based Database for Demo Purposes
 * This simulates database operations using JSON files
 */

// Data files directory
$DATA_DIR = __DIR__ . '/data';

// Create data directory if it doesn't exist
if (!file_exists($DATA_DIR)) {
    mkdir($DATA_DIR, 0755, true);
}

// Initialize data files with default data if they don't exist
function initializeData() {
    global $DATA_DIR;
    
    // Default users
    if (!file_exists($DATA_DIR . '/users.json')) {
        $users = [
            1 => [
                'id' => 1,
                'name' => 'Ram Kumar',
                'email' => 'ram@gmail.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'phone' => '9876543210',
                'address' => 'Delhi, India',
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'active'
            ]
        ];
        file_put_contents($DATA_DIR . '/users.json', json_encode($users, JSON_PRETTY_PRINT));
    }
    
    // Default officers
    if (!file_exists($DATA_DIR . '/officers.json')) {
        $officers = [
            1 => [
                'id' => 1,
                'name' => 'Officer One',
                'email' => 'officer1@gmail.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'phone' => '9876543211',
                'badge_number' => 'OFF001',
                'department' => 'Cyber Crime Division',
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'active'
            ]
        ];
        file_put_contents($DATA_DIR . '/officers.json', json_encode($officers, JSON_PRETTY_PRINT));
    }
    
    // Default admin
    if (!file_exists($DATA_DIR . '/admin.json')) {
        $admin = [
            1 => [
                'id' => 1,
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'active'
            ]
        ];
        file_put_contents($DATA_DIR . '/admin.json', json_encode($admin, JSON_PRETTY_PRINT));
    }
    
    // Default complaints
    if (!file_exists($DATA_DIR . '/complaints.json')) {
        $complaints = [];
        file_put_contents($DATA_DIR . '/complaints.json', json_encode($complaints, JSON_PRETTY_PRINT));
    }
    
    // Default updates
    if (!file_exists($DATA_DIR . '/updates.json')) {
        $updates = [];
        file_put_contents($DATA_DIR . '/updates.json', json_encode($updates, JSON_PRETTY_PRINT));
    }
}

// Initialize data on first run
initializeData();

// Helper functions
function readData($file) {
    global $DATA_DIR;
    $data = file_get_contents($DATA_DIR . '/' . $file . '.json');
    return json_decode($data, true);
}

function writeData($file, $data) {
    global $DATA_DIR;
    file_put_contents($DATA_DIR . '/' . $file . '.json', json_encode($data, JSON_PRETTY_PRINT));
}

function getNextId($file) {
    $data = readData($file);
    if (empty($data)) {
        return 1;
    }
    return max(array_keys($data)) + 1;
}

// Database simulation functions
function executeQuery($sql, $params = []) {
    // This is a simplified simulation - in real app, you'd parse SQL
    return true;
}

function fetchAll($sql, $params = []) {
    // Parse simple queries
    if (strpos($sql, 'SELECT') !== false) {
        if (strpos($sql, 'users') !== false) {
            $users = readData('users');
            return array_values($users);
        } elseif (strpos($sql, 'officers') !== false) {
            $officers = readData('officers');
            return array_values($officers);
        } elseif (strpos($sql, 'admin') !== false) {
            $admin = readData('admin');
            return array_values($admin);
        } elseif (strpos($sql, 'complaints') !== false) {
            $complaints = readData('complaints');
            $users = readData('users');
            $officers = readData('officers');
            
            // Handle JOIN queries
            if (strpos($sql, 'LEFT JOIN') !== false) {
                $result = [];
                foreach ($complaints as $complaint) {
                    $complaint_data = $complaint;
                    
                    // Add user info
                    if (isset($complaint['user_id']) && isset($users[$complaint['user_id']])) {
                        $complaint_data['user_name'] = $users[$complaint['user_id']]['name'];
                    }
                    
                    // Add officer info
                    if (isset($complaint['officer_id']) && isset($officers[$complaint['officer_id']])) {
                        $complaint_data['officer_name'] = $officers[$complaint['officer_id']]['name'];
                    }
                    
                    $result[] = $complaint_data;
                }
                
                // Handle ORDER BY and LIMIT
                if (strpos($sql, 'ORDER BY c.created_at DESC') !== false) {
                    usort($result, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
                }
                
                if (strpos($sql, 'LIMIT 5') !== false) {
                    $result = array_slice($result, 0, 5);
                }
                
                return $result;
            }
            
            return array_values($complaints);
        } elseif (strpos($sql, 'complaint_updates') !== false) {
            $updates = readData('updates');
            return array_values($updates);
        }
    }
    return [];
}

function fetchOne($sql, $params = []) {
    // Parse simple queries
    if (strpos($sql, 'SELECT') !== false) {
        if (strpos($sql, 'COUNT(*) as count') !== false) {
            if (strpos($sql, 'users') !== false) {
                $users = readData('users');
                $count = count($users);
                if (strpos($sql, "status = 'active'") !== false) {
                    $count = count(array_filter($users, fn($u) => $u['status'] === 'active'));
                }
                return ['count' => $count];
            } elseif (strpos($sql, 'officers') !== false) {
                $officers = readData('officers');
                $count = count($officers);
                if (strpos($sql, "status = 'active'") !== false) {
                    $count = count(array_filter($officers, fn($o) => $o['status'] === 'active'));
                }
                return ['count' => $count];
            } elseif (strpos($sql, 'complaints') !== false) {
                $complaints = readData('complaints');
                return ['count' => count($complaints)];
            }
        } elseif (strpos($sql, 'GROUP BY status') !== false && strpos($sql, 'complaints') !== false) {
            $complaints = readData('complaints');
            $status_counts = [];
            foreach ($complaints as $complaint) {
                $status = $complaint['status'];
                if (!isset($status_counts[$status])) {
                    $status_counts[$status] = ['status' => $status, 'count' => 0];
                }
                $status_counts[$status]['count']++;
            }
            return array_values($status_counts);
        } elseif (strpos($sql, 'users') !== false) {
            $users = readData('users');
            return array_values($users);
        } elseif (strpos($sql, 'officers') !== false) {
            $officers = readData('officers');
            return array_values($officers);
        } elseif (strpos($sql, 'admin') !== false) {
            $admin = readData('admin');
            return array_values($admin);
        } elseif (strpos($sql, 'complaints') !== false) {
            $complaints = readData('complaints');
            return array_values($complaints);
        } elseif (strpos($sql, 'complaint_updates') !== false) {
            $updates = readData('updates');
            return array_values($updates);
        }
    }
    return null;
}

function insert($table, $data) {
    $id = getNextId($table);
    $data['id'] = $id;
    $existingData = readData($table);
    $existingData[$id] = $data;
    writeData($table, $existingData);
    return $id;
}

function update($table, $data, $where, $whereParams = []) {
    $existingData = readData($table);
    
    // Simple where clause simulation (assuming WHERE id = ?)
    if (strpos($where, 'id') !== false && isset($whereParams[0])) {
        $id = $whereParams[0];
        if (isset($existingData[$id])) {
            $existingData[$id] = array_merge($existingData[$id], $data);
            writeData($table, $existingData);
            return true;
        }
    }
    return false;
}

function delete($table, $where, $params = []) {
    $existingData = readData($table);
    
    // Simple where clause simulation
    if (strpos($where, 'id') !== false && isset($params[0])) {
        $id = $params[0];
        if (isset($existingData[$id])) {
            unset($existingData[$id]);
            writeData($table, $existingData);
            return true;
        }
    }
    return false;
}

?>
