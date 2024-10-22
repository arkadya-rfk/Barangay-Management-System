<?php
session_start();
require 'db.php'; // Database connection

// Ensure the user is logged in and has the 'admin' role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blotter_id'], $_POST['status'])) {
    $blotter_id = (int) $_POST['blotter_id'];
    $status = $_POST['status'];

    // Validate status
    $valid_statuses = ['Pending', 'Resolved', 'Escalated'];
    if (!in_array($status, $valid_statuses)) {
        echo "Invalid status.";
        exit;
    }

    // Update the blotter status in the database
    $sql = "UPDATE blotters SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $blotter_id);

    if ($stmt->execute()) {
        // Redirect back to the blotter reports page
        header("Location: blotterReports.php");
        exit;
    } else {
        echo "Error updating status: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
?>
