<?php
session_start(); // Start the session

// Include your database connection file
include('db.php'); // Ensure this file contains the $conn variable

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $targetDir = "uploads/";  // Directory to save the uploaded files
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Try to upload the file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // File is successfully uploaded
            // Now, set the image path and update the database
            $imagePath = $targetFile;  // Path to the uploaded image

            // Check if user ID is set in session
            if (!isset($_SESSION['user']['id'])) {
                echo "User ID not set in session.";
                exit; // Stop execution if user ID is not set
            }

            $userId = $_SESSION['user']['id']; // Get user ID from session

            // Prepare SQL statement to update the profile_image in the USERS table
            $sql = "UPDATE USERS SET image = '$imagePath' WHERE id = $userId";

            // Execute the query and check for success
            if ($conn->query($sql) === TRUE) {
                echo "Image uploaded and database updated successfully";
                header("Location: user_dashboard.php"); // Redirect after upload
                exit; // Ensure no further code is executed
            } else {
                echo "Error updating database: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
