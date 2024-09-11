<?php
session_start();
require 'db.php'; // Database connection

// Ensure the user is logged in and has the 'user' role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// Fetch user data from the database
$user_id = $_SESSION['user']['id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .sidebar-open { transform: translateX(0); }
        .sidebar-closed { transform: translateX(-100%); }
    </style>
</head>
<body class="bg-blue-50">

    <!-- Sidebar -->
    <div id="sidebar" class="fixed top-0 left-0 h-screen w-64 bg-blue-700 text-white p-6 sidebar-open transition-transform duration-300">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold">User Menu</h2>
            <button onclick="toggleSidebar()"></button>
        </div>
        <ul class="mt-6">
            <li class="mb-4"><a href="#" class="text-white hover:text-black hover:font-bold">Profile</a></li>
            <li class="mb-4"><a href="#" class="text-white hover:text-black hover:font-bold">Dashboard</a></li>
            <li class="mb-4"><a href="#" class="text-white hover:text-black hover:font-bold">Settings</a></li>
            <li class="mb-4"><a href="logout.php" class="text-white hover:text-black hover:font-bold">Log Out</a></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="ml-0 lg:ml-64 transition-all duration-300">
        <div class="p-6">
            <div class="bg-white p-4 rounded-lg shadow-md">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold">User Dashboard</h1>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded-lg" onclick="toggleModal()">Edit Information</button>
                </div>

                <!-- User Info -->
                <div class="mt-6 flex flex-col items-center">
    <img src="profile-image-placeholder.jpg" alt="Profile" class="w-24 h-24 rounded-full">
    <h2 class="text-lg font-bold mt-4">
        <?= isset($user['first_name']) && isset($user['last_name']) ? $user['first_name'] . " " . $user['middle_name']. " " . $user['last_name'] : 'Name Not Available' ?>
    </h2>
    <p>Email: <?= isset($user['email']) ? $user['email'] : 'Email Not Available' ?></p>
    <p>Contact: <?= isset($user['contact']) ? $user['contact'] : ' ' ?></p> <!-- Assuming there's a 'contact' field -->
</div>


                <!-- Contact Info -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    <div>
                        <h3 class="font-bold">User Information</h3>
                        <p>First Name: <?= $user['first_name'] ?></p>
                        <p>Middle Name: <?= $user['middle_name'] ?></p>
                        <p>Last Name: <?= $user['last_name'] ?></p>
                        <p>Birth Date: <?= isset($user['birth_date']) ? $user['birth_date'] : ' ' ?></p> <!-- Assuming there's a birth_date field -->
                    </div>
                    <div>
                        <h3 class="font-bold">Contact Information</h3>
                        <p>Address: <?= isset($user['address']) ? $user['address'] : ' ' ?></p> <!-- Assuming there's an 'address' field -->
                        <p>Civil Status: <?= isset($user['civil_status']) ? $user['civil_status'] : ' ' ?></p>
                        <p>Citizenship: <?= $user['citizenship'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Information Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-xl font-bold mb-4">Edit Information</h2>
            <form action="edit_user.php" method="POST"> <!-- Assuming edit_user.php handles updates -->
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="w-full p-2 border border-gray-300 rounded" value="<?= $user['first_name'] ?>">
                    </div>
                    <div>
                        <label for="last_name" class="block">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="w-full p-2 border border-gray-300 rounded" value="<?= $user['last_name'] ?>">
                    </div>
                </div>
                <div class="mt-4">
                    <label for="email" class="block">Email</label>
                    <input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded" value="<?= $user['email'] ?>">
                </div>
                <div class="mt-4">
                    <label for="contact" class="block">Contact Number</label>
                    <input type="text" name="contact" id="contact" class="w-full p-2 border border-gray-300 rounded" value="<?= $user['contact'] ?>"> <!-- Assuming there's a contact field -->
                </div>
                <div class="flex justify-between mt-6">
                    <button type="button" class="bg-gray-300 px-4 py-2 rounded" onclick="toggleModal()">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('sidebar-closed');
            document.getElementById('sidebar').classList.toggle('sidebar-open');
        }

        function toggleModal() {
            document.getElementById('editModal').classList.toggle('hidden');
        }
    </script>
</body>
</html>
