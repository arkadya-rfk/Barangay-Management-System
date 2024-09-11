<?php
session_start();
require 'db.php'; // Database connection

// Check if the login form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL query to check if the user exists
    $sql = "SELECT id, first_name, middle_name, last_name, email, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, now check the password
        $user = $result->fetch_assoc();
        
        // Debugging output
        // print_r($user); // Uncomment for debugging

        // For admin, no password hashing
        if ($user['role'] == 'admin' && $password === $user['password']) {
            // Set session for admin
            $_SESSION['user'] = [
                'id' => $user['id'],
                'first_name' => $user['first_name'],
                'middle_name' => $user['middle_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            // Redirect to admin panel
            header("Location: admin.php");
            exit;

        } elseif ($user['role'] == 'user' && password_verify($password, $user['password'])) {
            // Set session for user
            $_SESSION['user'] = [
                'id' => $user['id'],
                'first_name' => $user['first_name'],
                'middle_name' => $user['middle_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            // Redirect to user dashboard
            header("Location: user_dashboard.php");
            exit;

        } else {
            // Invalid password 
            $error = "Invalid username or password!";
        }
    } else {
        // User not found
        $error = "User not found!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50">
    
<div class="flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-6 text-center">LOGIN</h2>
        
        <?php if (!empty($error)) : ?>
            <p class="bg-red-500 text-white font-bold px-6 py-4 rounded shadow-lg mb-4"><?= $error; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <div class="col-span-2">
                <label for="username" class="block text-sm font-medium">Username</label>
                <input type="text" name="username" id="username" placeholder="Username" class="p-2 border rounded w-full">
            </div>
            <div class="col-span-2">
                <label for="password" class="block text-sm font-medium">Password</label>
                <input type="password" name="password" id="password" placeholder="Password" class="p-2 border rounded w-full" required>
            </div>

            <div class="flex justify-between mt-6">
                <a href="index.php" class="text-blue-500 hover:text-blue-900 ml-4 mt-2">Back</a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 active:bg-blue-900 text-white px-6 py-2 rounded">LOGIN</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
