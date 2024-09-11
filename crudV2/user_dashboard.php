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
            <li class="mb-4"><a href="user_dashboard.php" class="text-white hover:text-black hover:font-bold">Profile</a></li>
            <li class="mb-4"><a href="#" class="text-white hover:text-black hover:font-bold">Get Verified</a></li>
            <li class="mb-4"><a href="#" class="text-white hover:text-black hover:font-bold">Request Document</a></li>
            <li class="mb-4"><a href="logout.php" class="text-white hover:text-black hover:font-bold">Log Out</a></li>
        </ul>
    </div>


    <?php if (isset($_GET['update'])): ?>
    <div class="p-4 mb-4 text-sm <?= $_GET['update'] == 'success' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' ?> rounded-lg" role="alert">
        <?= $_GET['update'] == 'success' ? 'User information updated successfully!' : 'An error occurred while updating user information.' ?>
    </div>
<?php endif; ?>

    <!-- Main Content -->
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
                        <?= isset($user['first_name']) && isset($user['last_name']) ? $user['first_name'] . " " . $user['middle_name'] . " " . $user['last_name'] : 'Name Not Available' ?>
                    </h2>
                    <p>Username: <?= isset($user['email']) ? $user['email'] : 'Email Not Available' ?></p>
                    <p>Email: <?= isset($user['user_email']) ? $user['user_email'] : 'Email Not Available' ?></p>
                    <p>Contact Number: <?= isset($user['contact']) ? $user['contact'] : 'Not Provided' ?></p>
                </div>

                <!-- Contact Info -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    <div>
                        <h3 class="font-bold">User Information</h3>
                        <p>First Name: <?= $user['first_name'] ?></p>
                        <p>Middle Name: <?= $user['middle_name'] ?></p>
                        <p>Last Name: <?= $user['last_name'] ?></p>
                        <p>Birth Date: <?= isset($user['birth_date']) ? $user['birth_date'] : 'Not Provided' ?></p>
                    </div>
                    <div>
                        <h3 class="font-bold">Contact Information</h3>
                        <p>Address: <?= isset($user['address']) ? $user['address'] : 'Not Provided' ?></p>
                        <p>Civil Status: <?= isset($user['civil_status']) ? $user['civil_status'] : 'Not Provided' ?></p>
                        <p>Citizenship: <?= $user['citizenship'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Information Modal -->
    <!-- Edit Information Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center">
    <div class="bg-white p-6 rounded-lg w-full max-w-2xl">
        <h2 class="text-xl font-bold mb-4">Edit Information</h2>
        <form id="editForm" method="POST"> <!-- Removed action for AJAX handling -->
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-1">
                    <label for="first_name" class="block">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="p-2 border rounded w-full" value="<?= $user['first_name'] ?>" required>
                </div>
                <div class="col-span-1">
                    <label for="middle_name" class="block">Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" class="p-2 border rounded w-full" value="<?= $user['middle_name'] ?>" required>
                </div>
                <div class="col-span-1">
                    <label for="last_name" class="block">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="p-2 border rounded w-full" value="<?= $user['last_name'] ?>" required>
                </div>
                <div class="col-span-1">
                    <label for="birth_date" class="block">Birth Date</label>
                    <input type="date" name="birth_date" id="birth_date" class="p-2 border rounded w-full" value="<?= $user['birth_date'] ?? '' ?>" required>
                </div>
            </div>

            <div class="mt-4">
                <label for="email" class="block">Username</label> <!-- Username field -->
                <input type="text" name="username" id="email" class="w-full p-2 border border-gray-300 rounded" value="<?= $user['email'] ?>"> <!-- ID is 'email' for Username -->
            </div>

            <div class="mt-4">
                <label for="User" class="block">Email Address</label> <!-- New Email Address field -->
                <input type="email" name="user_email" id="User" class="w-full p-2 border border-gray-300 rounded" value="<?= isset($user['user_email']) ? $user['user_email'] : '' ?>"> <!-- Assuming there's a 'user_email' field -->
            </div>
            
            <div class="mt-4">
                <label for="contact" class="block">Contact Number</label>
                <input type="text" name="contact" id="contact" class="p-2 border rounded w-full" value="<?= $user['contact'] ?? '' ?>" required>
            </div>
            <div class="mt-4">
                <label for="address" class="block">Address</label>
                <input type="text" name="address" id="address" class="p-2 border rounded w-full" value="<?= $user['address'] ?? '' ?>" required>
            </div>
            <div class="mt-4">
                <label for="civil_status" class="block">Civil Status</label>
                <select name="civil_status" id="civil_status" class="p-2 border rounded w-full" required>
                    <option value="Single" <?= $user['civil_status'] == 'Single' ? 'selected' : '' ?>>Single</option>
                    <option value="Married" <?= $user['civil_status'] == 'Married' ? 'selected' : '' ?>>Married</option>
                    <option value="Divorced" <?= $user['civil_status'] == 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                    <option value="Not to say" <?= $user['civil_status'] == 'Not to say' ? 'selected' : '' ?>>Prefer not to say</option>
                </select>
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

        document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(this); // Gather the form data

    // Send the AJAX request to edit_user.php
    fetch('edit_user.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest' // To check if request is AJAX in PHP
        }
    })
    .then(response => response.text())  // Process the response as text
    .then(data => {
        console.log("Server Response:", data);  // Log the server's response to the console
        if (data.trim() === 'success') {
            // Hide the modal after success and reload the page
            toggleModal();
            location.reload(); // This will reload the page and fetch updated user info
        } else {
            // Handle errors and display feedback to the user
            alert('Error updating user information: ' + data);  // Display the error message
        }
    })
    .catch(error => {
        console.error('Error:', error); // Log errors to the console
        alert('An error occurred while submitting the form.');
    });
});

    </script>

</body>
</html>
