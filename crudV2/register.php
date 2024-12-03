<?php
require 'db.php'; // Database connection

$successMessage = ""; // Initialize empty success message

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $civil_status = $_POST['civil_status'];
    $citizenship = $_POST['citizenship'];
    $achieved_status = $_POST['achieved_status'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (first_name, middle_name, last_name, age, gender, civil_status, citizenship, achieved_status, email, password, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'user')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $first_name, $middle_name, $last_name, $age, $gender, $civil_status, $citizenship, $achieved_status, $email, $password);
    
    if ($stmt->execute()) {
        $successMessage = "User registered successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .success-message {
            font-weight: bold;
            color: green;
            opacity: 0;
            animation: fadeIn 1s forwards;
            margin-top: 20px;
        }
    </style>
</head>
<body class="bg-blue-50">

<!-- Main content -->
<div class="flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-6 text-center">SIGN UP</h2>
        
        <?php if (!empty($successMessage)): ?>
            <p class="success-message text-center"><?php echo $successMessage; ?></p>
        <?php endif; ?>
        
        <form id="registerForm" action="" method="POST" class="space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1 mt-8">
                    <label for="first_name" class="block text-sm font-medium">First Name</label>
                    <input type="text" name="first_name" id="first_name" placeholder="First Name" class="p-2 border rounded w-full" required>
                </div>
                <div class="col-span-1 mt-8">
                    <label for="middle_name" class="block text-sm font-medium">Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" placeholder="Middle Name" class="p-2 border rounded w-full" required>
                </div>
                <div class="col-span-1 mt-8">
                    <label for="last_name" class="block text-sm font-medium">Last Name</label>
                    <input type="text" name="last_name" id="last_name" placeholder="Last Name" class="p-2 border rounded w-full" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-1">
                    <label for="age" class="block text-sm font-medium">Age</label>
                    <input type="text" name="age" id="age" placeholder="Age" class="p-2 border rounded w-full" required>
                </div>
                <div class="col-span-1">
                    <label for="gender" class="block text-sm font-medium">Gender</label>
                    <select name="gender" id="gender" class="p-2 border rounded w-full" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Prefer not to say</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-1">
                    <label for="achieved_status" class="block text-sm font-medium">Achieved Status</label>
                    <select name="achieved_status" id="achieved_status" class="p-2 border rounded w-full" required>
                        <option value="Student">Student</option>
                        <option value="Employed">Employed</option>
                        <option value="Unemployed">Unemployed</option>
                    </select>
                </div>
                <div class="col-span-1">
                    <label for="civil_status" class="block text-sm font-medium">Civil Status</label>
                    <select name="civil_status" id="civil_status" class="p-2 border rounded w-full" required>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Divorced">Divorced</option>
                        <option value="Not to say">Prefer not to say</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-1">
                    <label for="citizenship" class="block text-sm font-medium">Citizenship</label>
                    <input type="text" name="citizenship" id="citizenship" placeholder="Citizenship" class="p-2 border rounded w-full" required>
                </div>
                <div class="col-span-1">
                    <label for="email" class="block text-sm font-medium">Username</label>
                    <input type="text" name="email" id="email" placeholder="Username" class="p-2 border rounded w-full" required>
                </div>
            </div>

            <div class="col-span-2">
                <label for="password" class="block text-sm font-medium">Password</label>
                <input type="password" name="password" id="password" placeholder="Password" class="p-2 border rounded w-full" required>
            </div>

            <div class="flex justify-between mt-6">
                <a href="index.php" class="text-blue-500 hover:text-blue-900 ml-6 mt-2">Cancel</a>
                <button type="submit" class="bg-blue-500 text-white hover:bg-blue-700 active:bg-blue-900 px-6 py-2 rounded">SUBMIT</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
