<?php
// Database configuration

// Database credentials
$host = "localhost";
$username = "root";
$password = "password";
$dbname = "college_event_website";

// Global PDO connection that can be reused
$global_pdo = null;

/**
 * Create and return a PDO database connection
 * * @return PDO The database connection
 */
function getPDOConnection() {
    global $host, $username, $password, $dbname, $global_pdo;
    
    // Reuse existing connection if available
    if ($global_pdo !== null) {
        return $global_pdo;
    }
    
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Store for reuse
        $global_pdo = $pdo;
        
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

/**
 * Create and return a mysqli database connection
 * This is used by the API endpoints
 * * @return mysqli The database connection
 */
function getConnection() {
    global $host, $username, $password, $dbname;
    
    $conn = new mysqli($host, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

/**
 * Convert mysqli result to associative array
 * * @param mysqli_result $result The result from a mysqli query
 * @return array An array of rows
 */
function resultToArray($result) {
    $rows = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

/**
 * Sanitize input data
 * * @param string $data The input data to sanitize
 * @return string The sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Create a JSON response
 * * @param bool $success Whether the operation was successful
 * @param array|null $data Additional data to include in the response
 * @param string|null $error_message Error message to include if success is false
 * @return string JSON encoded response
 */
function jsonResponse($success, $data = null, $error_message = null) {
    $response = ['success' => $success];
    
    if ($data !== null) {
        $response = array_merge($response, $data);
    }
    
    if (!$success && $error_message !== null) {
        $response['error_message'] = $error_message;
    }
    
    return json_encode($response);
}
?>