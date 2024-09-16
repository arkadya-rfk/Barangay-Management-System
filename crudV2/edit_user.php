<?php
// Include database connection
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email_address = $_POST['user_email'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender']; // Capture gender
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $civil_status = $_POST['civil_status'];

    // SQL to update user information
    $sql = "UPDATE users SET 
                first_name = ?, 
                middle_name = ?, 
                last_name = ?, 
                email = ?, 
                user_email = ?, 
                birth_date = ?,  
                gender = ?,  -- Include gender in update
                contact = ?, 
                address = ?, 
                civil_status = ?
            WHERE id = ?";

    // Prepare the statement to prevent SQL injection
    if (!$stmt = $conn->prepare($sql)) {
        echo 'error:sql_prepare - ' . $conn->error; 
        exit;
    }

    // Bind parameters (note that gender is added)
    if (!$stmt->bind_param("ssssssssssi", $first_name, $middle_name, $last_name, $username, $email_address, $birth_date, $gender, $contact, $address, $civil_status, $user_id)) {
        echo 'error:sql_bind - ' . $stmt->error; 
        exit;
    }

    // Execute the query
    if ($stmt->execute()) {
        echo 'success';  // Send success response to AJAX
    } else {
        echo 'error:sql_execute - ' . $stmt->error;  // Output the execute error
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
