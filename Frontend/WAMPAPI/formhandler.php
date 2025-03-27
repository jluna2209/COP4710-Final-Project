<?php

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $userLevel = $_POST['userLevel'];

    try {
        require_once 'dbh.inc.php';

        // Check if the user_name already exists
        $query_check = "SELECT user_name FROM users WHERE user_name = ?";
        $stmt_check = $pdo->prepare($query_check);
        $stmt_check->execute([$user_name]);

        if ($stmt_check->rowCount() > 0) {
            // Username already exists
            $response = array('success' => false, 'error_message' => "Username already exists. Please choose a different user_name.");
            echo json_encode($response);
        } else {
            // Username is available, proceed with registration
            $query_insert = "INSERT INTO users (user_name, password, first_name, last_name, user_level) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $pdo->prepare($query_insert);
            $stmt_insert->execute([$user_name, $password, $first_name, $last_name, $userLevel]);

            $response = array('success' => true);
            echo json_encode($response);
        }
    } catch (PDOException $e) {
        $response = array('success' => false, 'error_message' => "Query failed: " . $e->getMessage());
        echo json_encode($response);
    }
} else {
    $response = array('success' => false, 'error_message' => "Invalid request method");
    echo json_encode($response);
}