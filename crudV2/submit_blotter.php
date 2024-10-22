<?php
session_start();
require 'db.php'; // Database connection

// Ensure the user is logged in and has the 'user' role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// Fetch the logged-in user ID
$user_id = $_SESSION['user']['id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize input
    $incident_date_time = $_POST['incident_date_time'] ?? '';
    $incident_location = $_POST['incident_location'] ?? '';
    $incident_description = $_POST['incident_description'] ?? '';
    $type_of_incident = $_POST['type_of_incident'] ?? '';
    $involved_parties = $_POST['involved_parties'] ?? '';
    $witnesses = $_POST['witnesses'] ?? '';
    $desired_resolution = $_POST['desired_resolution'] ?? '';

    // Validate required fields
    if (empty($incident_date_time) || empty($incident_location) || empty($incident_description) || empty($type_of_incident)) {
        echo "Please fill in all the required fields.";
        exit;
    }

    // Prepare and bind the SQL statement to insert data
    $sql = "INSERT INTO blotters (user_id, incident_date_time, incident_location, incident_description, type_of_incident, involved_parties, witnesses, desired_resolution)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $user_id, $incident_date_time, $incident_location, $incident_description, $type_of_incident, $involved_parties, $witnesses, $desired_resolution);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Redirect to a success page or display a success message
        header("Location: blotter_success.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
?>
