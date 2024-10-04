<?php
// Include database connection
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['username'];  // This should update the 'email' field (username)
    $email_address = $_POST['user_email'];  // This should update the 'user_email' field (email address)
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender']; // Capture gender
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $citizenship = $_POST['citizenship']; // Corrected this variable name
    $civil_status = $_POST['civil_status'];
    $achieved_status = $_POST['achieved_status'];

    // SQL to update user information (make sure column names match the database schema)
    $sql = "UPDATE users SET 
                first_name = ?, 
                middle_name = ?, 
                last_name = ?, 
                email = ?,  -- This corresponds to the username in your table
                user_email = ?,  -- This corresponds to the actual email address
                birth_date = ?,  
                gender = ?,  
                contact = ?, 
                address = ?, 
                citizenship = ?,  -- Corrected the column name
                civil_status = ?, 
                achieved_status = ?
            WHERE id = ?";

    // Prepare the statement to prevent SQL injection
    if (!$stmt = $conn->prepare($sql)) {
        echo 'error:sql_prepare - ' . $conn->error;
        exit;
    }

    // Bind parameters (make sure the user_id is the last parameter)
    if (!$stmt->bind_param("ssssssssssssi", $first_name, $middle_name, $last_name, $email, $email_address, $birth_date, $gender, $contact, $address, $citizenship, $civil_status, $achieved_status, $user_id)) {
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
?>
