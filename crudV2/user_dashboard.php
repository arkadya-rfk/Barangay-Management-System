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

function capitalizeStatus($status) {
    return ucfirst(strtolower($status)); // Convert to lowercase first, then capitalize
}

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
    <link rel="stylesheet" href="user.css">
</head>
<body class="bg-blue-50">

    <!-- Sidebar -->
    <div id="sidebar" class="bg-blue-800 text-white p-5 transition-transform rounded-r-lg">
        <div class="flex items-center">
            <img id="sidebar-avatar" src="8.png" alt="User Avatar" class="sidebar-avatar w-20 h-20 rounded-full cursor-pointer">
            <h2 class="ml-4 text-xl font-bold"><?= $user['first_name'] ?? 'User' ?></h2>
        </div>
        <ul class="mt-10">
            <li class="mb-4"><a href="user_dashboard.php" class="text-white hover:text-black font-bold">Profile</a></li>
            <li class="mb-4"><a href="javascript:void(0)" class="text-white hover:text-black hover:font-bold" onclick="toggleModal()">Edit Profile</a></li>
            <li class="mb-4"><a href="logout.php" class="text-white hover:text-black hover:font-bold">Log Out</a></li>
        </ul>
    </div>

    <!-- Avatar for toggling the sidebar -->
    <div id="avatar-only" class="p-5 cursor-pointer">
        <img src="7.png" alt="User Avatar" class="w-20 h-20 rounded-full">
    </div>

    
    <?php if (isset($_GET['update'])): ?>
    <div class="p-4 mb-4 text-sm <?= $_GET['update'] == 'success' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' ?> rounded-lg" role="alert">
       <p class ="font-bold text-l" ><?= $_GET['update'] == 'success' ? 'User information updated successfully!' : 'An error occurred while updating user information.' ?></p>
    </div>
    <?php endif; ?>


        <div class="flex items-center justify-between lg:ml-72 mt-12 transition-all duration-300">
                <h1 class="text-2xl font-bold">Profile Dashboard</h1>
            </div>
        
            <div id="content" class="content p-6 lg:ml-64">
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
            <img src="ambot.jpeg" alt="Profile" class="w-36 h-36 rounded-full mx-auto">
            <h2 class="text-2xl font-bold mt-4">
                <?= isset($user['first_name']) && isset($user['last_name']) ? $user['first_name'] . " " . $user['middle_name'] . " " . $user['last_name'] : 'Name Not Available' ?>
            </h2>
        </div>

        <!-- Nav Menus -->
        <div class="bg-blue-100 p-4 rounded-lg shadow-md mt-6">
            <ul class="flex justify-center space-x-6">
                <li id="account-link"><a href="javascript:void(0)" class="nav-link text-blue-600 font-bold hover:text-black" onclick="showSection('account')">Account Center</a></li>
                <li id="user-info-link"><a href="javascript:void(0)" class="nav-link text-blue-600 font-bold hover:text-black" onclick="showSection('user-info')">User Information</a></li>
                <li id="contact-info-link"><a href="javascript:void(0)" class="nav-link text-blue-600 font-bold hover:text-black" onclick="showSection('contact-info')">Contact Information</a></li>
            </ul>
         </div>

        <!-- Account Center Section -->
        <div id="account" class="section-content bg-white p-6 rounded-lg shadow-md mt-6">
            <h3 class="font-bold mb-4 text-2xl">Account Center</h3>
            <p>Username: <?= $user['email'] ?? 'Username Not Available' ?></p>
            <p>Email: <?= $user['user_email'] ?? 'Email Not Available' ?></p>
        </div>

        <div id="user-info" class="section-content bg-white p-6 rounded-lg shadow-md mt-6">
            <h3 class="font-bold mb-4 text-2xl">User Information</h3>
            <p>First Name: <?= $user['first_name'] ?></p>
            <p>Middle Name: <?= $user['middle_name'] ?></p>
            <p>Last Name: <?= $user['last_name'] ?></p>
            <p>Birth Date: <?= isset($user['birth_date']) ? (new DateTime($user['birth_date']))->format('M d, Y') : 'Not Provided' ?></p>
            <p>Gender: <?= isset($user['gender']) ? ucfirst($user['gender']) : 'Not Provided' ?></p> <!-- Added gender -->
        </div>

        <!-- Contact Information Section -->
        <div id="contact-info" class="section-content bg-white p-6 rounded-lg shadow-md mt-6">
            <h3 class="font-bold mb-4 text-2xl">Contact Information</h3>
            <p>Contact Number: <?= $user['contact'] ?? 'Not Provided' ?></p>
            <p>Address: <?= $user['address'] ?? 'Not Provided' ?></p>
            <p>Achieved Status: <?= ($user['achieved_status']) ? capitalizeStatus($user['achieved_status']) : 'Not Provided' ?></p>
            <p>Civil Status: <?= ($user['civil_status']) ? capitalizeStatus($user['civil_status']) : 'Not Provided' ?></p>
            <p>Citizenship: <?= $user['citizenship'] ?></p>
        </div>
    </div>
</div>



    <!-- Edit Information Modal -->
    <div id="editModal" class="fixed inset-0 z-50 flex justify-center items-center bg-black bg-opacity-50 hidden">
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
                    <input type="date" name="birth_date" id="birth_date" class="p-2 border rounded w-full" 
                    value="<?= isset($user['birth_date']) ? (new DateTime($user['birth_date']))->format('Y-m-d') : '' ?>" required>
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
                <input type="text" name="contact" id="contact" class="p-2 border rounded w-full" value="<?= $user['contact'] ?>">
            </div>

            <div class="mt-4">
                <label for="address" class="block">Address</label>
                <textarea name="address" id="address" rows="3" class="p-2 border rounded w-full"><?= $user['address'] ?></textarea>
            </div>

             <!-- Add Gender Field -->
             <div class="mt-4">
                <label for="gender" class="block">Gender</label>
                <select name="gender" id="gender" class="w-full p-2 border rounded" required>
                    <option value="Male" <?= $user['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $user['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $user['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <!-- Add Achieved Status Field -->
            <div class="mt-4">
                <label for="achieved_status" class="block">Civil Status</label>
                <select name="achieved_status" id="achieved_status" class="w-full p-2 border rounded" required>
                    <option value="Student" <?= $user['achieved_status'] === 'Student' ? 'selected' : '' ?>>Student</option>
                    <option value="Employed" <?= $user['achieved_status'] === 'Employed' ? 'selected' : '' ?>>Employed</option>
                    <option value="Unemployed" <?= $user['achieved_status'] === 'Unemployed' ? 'selected' : '' ?>>Unemployed</option>
                </select>
            </div>

            <!-- Add Civil Status Field -->
            <div class="mt-4">
                <label for="civil_status" class="block">Civil Status</label>
                <select name="civil_status" id="civil_status" class="w-full p-2 border rounded" required>
                    <option value="Single" <?= $user['civil_status'] === 'Single' ? 'selected' : '' ?>>Single</option>
                    <option value="Married" <?= $user['civil_status'] === 'Married' ? 'selected' : '' ?>>Married</option>
                    <option value="Divorced" <?= $user['civil_status'] === 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                    <option value="Not to say" <?= $user['civil_status'] === 'Not to say' ? 'selected' : '' ?>>Not to Say</option>
                </select>
            </div>

            <div class="mt-6 text-right">
                <button type="button" onclick="toggleModal()" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
        const avatarOnly = document.getElementById('avatar-only');
        avatarOnly.classList.toggle('hidden');
    }

    document.getElementById('sidebar-avatar').addEventListener('click', toggleSidebar);
    document.getElementById('avatar-only').addEventListener('click', toggleSidebar);

    function toggleModal() {
        document.getElementById('editModal').classList.toggle('hidden');
    }

    // Handle form submission via AJAX
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

    fetch('edit_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data === 'success') {
            window.location.href = 'user_dashboard.php?update=success';
        } else {
            alert('An error occurred: ' + data);  // Output the specific error
            window.location.href = 'user_dashboard.php?update=error';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.location.href = 'user_dashboard.php?update=error';
    });
});

    function showSection(sectionId) {
            const sections = document.querySelectorAll('.section-content');
            sections.forEach(section => section.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
        }

        // Initially show Account Center
        showSection('account');


        function showSection(section) {
    // Hide all sections
    document.getElementById('account').style.display = 'none';
    document.getElementById('user-info').style.display = 'none';
    document.getElementById('contact-info').style.display = 'none';

    // Show the selected section
    document.getElementById(section).style.display = 'block';

    // Remove active class from all nav links
    let navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.classList.remove('active-link'); // Custom active class
        link.style.color = ''; // Reset text color
    });

    // Add active class to the clicked link
    if (section === 'account') {
        document.querySelector('#account-link a').classList.add('active-link');
        document.querySelector('#account-link a').style.color = 'black'; // Set active text color
    } else if (section === 'user-info') {
        document.querySelector('#user-info-link a').classList.add('active-link');
        document.querySelector('#user-info-link a').style.color = 'black';
    } else if (section === 'contact-info') {
        document.querySelector('#contact-info-link a').classList.add('active-link');
        document.querySelector('#contact-info-link a').style.color = 'black';
    }
}
</script>

</body>
</html>
