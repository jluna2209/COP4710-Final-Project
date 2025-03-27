<?php
session_start(); // Start the session

$servername = "localhost"; // Replace with your actual server name
$username = "root";     // Replace with your actual database username
$password = "password";     // Replace with your actual database password
$dbname = "college_event_website";         // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get username and password from the form
$username = $_POST['username'];
$password = $_POST['password'];

// Prepare and execute the query to fetch user data
$stmt = $conn->prepare("SELECT user_id, username, password, user_level FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id, $db_username, $hashed_password, $user_level);
    $stmt->fetch();

    // Verify the password
    if (password_verify($password, $hashed_password)) {
        // Password is correct!
        $_SESSION['userID'] = $user_id;
        $_SESSION['userRole'] = $user_level;

        $response = array('success' => true);
    } else {
        // Invalid password
        $response = array('success' => false, 'error_message' => "Invalid password");
    }
} else {
    // Username not found
    $response = array('success' => false, 'error_message' => "Username not found");
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);

$stmt->close();
$conn->close();
?>