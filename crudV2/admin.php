<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$admin = $_SESSION['user'];

require 'db.php'; // Database connection

// Error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Fetch data for the admin dashboard
    $residents_query = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'user'");
    $residents_count = $residents_query->fetch_assoc()['total'];
    
    $males_query = $conn->query("SELECT COUNT(*) AS total FROM users WHERE gender='male' AND role = 'user'");
    $males_count = $males_query->fetch_assoc()['total'];
    
    $females_query = $conn->query("SELECT COUNT(*) AS total FROM users WHERE gender='female' AND role = 'user'");
    $females_count = $females_query->fetch_assoc()['total'];
    
    $students_query = $conn->query("SELECT COUNT(*) AS total FROM users WHERE achieved_status='student' AND role = 'user'");
    $students_count = $students_query->fetch_assoc()['total'];
    
    $employed_query = $conn->query("SELECT COUNT(*) AS total FROM users WHERE achieved_status='employed' AND role = 'user'");
    $employed_count = $employed_query->fetch_assoc()['total'];
    
    $unemployed_query = $conn->query("SELECT COUNT(*) AS total FROM users WHERE achieved_status='unemployed' AND role = 'user'");
    $unemployed_count = $unemployed_query->fetch_assoc()['total'];

    // Handle search query
    $search_results = [];
    $search_term = '';
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search_term = $conn->real_escape_string($_GET['search']);
        $search_query = $conn->query("
            SELECT first_name, middle_name, last_name, email, address
            FROM users 
            WHERE first_name LIKE '%$search_term%' 
            AND role != 'admin'
        ");
        while ($row = $search_query->fetch_assoc()) {
            $search_results[] = $row;
        }
    }

} catch (mysqli_sql_exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="bg-gray-50 relative">

    <!-- Sidebar hidden initially -->
    <div id="sidebar" class="bg-blue-800 text-white p-5 transition-transform rounded-r-lg">
        <div class="flex items-center">
            <img id="sidebar-avatar" src="8.png" alt="Admin Avatar" class="sidebar-avatar w-20 h-20 rounded-full cursor-pointer">
            <h2 class="text-xl font-bold ml- mt-2">ADMIN</h2>
        </div>
        <ul class="mt-16">
            <li class="mb-4"><a href="admin.php" class="text-white hover:text-black font-bold">Home</a></li>
            <li class="mb-4"><a href="residents.php" class="text-white hover:text-black hover:font-bold">List of Residents</a></li>
            <li class="mb-4"><a href="blotterReports.php" class="text-white hover:text-black hover:font-bold">Blotter Reports</a></li>
            <li class="mb-4"><a href="logout.php" class="text-white hover:text-black hover:font-bold">Log Out</a></li>
        </ul>
    </div>

    <!-- Avatar for toggling the sidebar -->
        <div id="avatar-only" class="p-5 cursor-pointer">
            <img src="7.png" alt="Admin Avatar" class="w-20 h-20 rounded-full">
        </div>

    <!-- Main content with Search -->
        <div class="center-content">
            <h1 class="text-3xl font-bold mb-10 mt-20">Barangay Management System</h1>

        <!-- Back button (hidden initially) -->
        <button id="back-btn" class="mb-4 p-2 bg-gray-500 text-white rounded">Back to Dashboard</button>

        <!-- Search box -->
        <div class="mb-8 flex items-center">
            <input type="text" id="search-box" placeholder="Search by First name" class="border rounded-lg p-2 w-96">
            <button id="search-btn" class="ml-4 p-2 bg-blue-500 text-white rounded">Search</button>
        </div>

        <!-- Display search results -->
        <div id="results">
            <h2 class="text-2xl font-bold mb-4">Search Results:</h2>

            <?php if (!empty($search_term)): ?>
                <?php if (empty($search_results)): ?>
                    <p>No results found for "<?php echo htmlspecialchars($search_term); ?>"</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($search_results as $result): ?>
                            <div class="bg-white shadow-lg p-4 rounded-lg cursor-pointer" 
                                 onclick="showUserDetails(<?php echo htmlspecialchars(json_encode($result)); ?>)">
                                <p class="font-bold text-lg">
                                    <?php 
                                        echo htmlspecialchars($result['first_name']) . ' ' . htmlspecialchars($result['middle_name']) . ' ' . htmlspecialchars($result['last_name']); 
                                    ?>
                                </p>
                                <p>Username: <?php echo htmlspecialchars($result['email']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>


        <!-- User Details Modal (Hidden by default) -->
        <div id="userDetailsModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-8 rounded-lg w-1/3">
                <button class="bg-red-500 text-white px-4 py-2 rounded-md mb-4" onclick="closeModal()">Close</button>
                <h3 class="text-2xl font-bold mb-4" id="userFullName"></h3>
                <p><strong>Email:</strong> <span id="userEmail"></span></p>
                <p><strong>First Name:</strong> <span id="userFirstName"></span></p>
                <p><strong>Middle Name:</strong> <span id="userMiddleName"></span></p>
                <p><strong>Last Name:</strong> <span id="userLastName"></span></p>
                <p><strong>Address:</strong> <span id="userAddress"></span></p>
                <!-- Add more fields as needed -->
            </div>
        </div>


        <!-- Dashboard widgets -->
        <div id="dashboard" class="grid grid-cols-3 gap-6">
            <!-- Dashboard content here -->
            <div class="bg-white p-6 rounded-lg shadow flex items-center widget">
                <img src="1.png" alt="Residents Icon" class="w-12 h-12 mr-6">
                <div>
                    <h2 class="text-xl font-bold">Residents</h2>
                    <p class="mt-4 text-2xl mb-2"><?php echo htmlspecialchars($residents_count); ?></p>
                    <a class="mt-2 text-s font-bold" style="color:blue;" href="residents.php">View all</a>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow flex items-center widget">
                <img src="3.png" alt="Male Icon" class="w-16 h-16 mr-6">
                <div>
                    <h2 class="text-xl font-bold"><a href="residents.php">Male</a></h2>
                    <p class="mt-4 text-2xl mb-2"><?php echo htmlspecialchars($males_count); ?></p>
                    <a class="mt-2 text-s font-bold" style="color:blue;" href="residents.php?filter=male">View all</a>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow flex items-center widget">
                <img src="4.png" alt="Female Icon" class="w-16 h-16 mr-6">
                <div>
                    <h2 class="text-xl font-bold">Female</h2>
                    <p class="mt-4 text-2xl mb-2"><?php echo htmlspecialchars($females_count); ?></p>
                    <a class="mt-2 text-s font-bold" style="color:blue;" href="residents.php?filter=female">View all</a>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow flex items-center widget">
                <img src="2.png" alt="Students Icon" class="w-14 h-14 mr-4">
                <div>
                    <h2 class="text-xl font-bold">Students</h2>
                    <p class="mt-4 text-2xl mb-2"><?php echo htmlspecialchars($students_count); ?></p>
                    <a class="mt-2 text-s font-bold" style="color:blue;" href="residents.php?filter=students">View all</a>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow flex items-center widget">
                <img src="5.png" alt="Employed Icon" class="w-14 h-14 mr-6 ml-2">
                <div>
                    <h2 class="text-xl font-bold">Employed</h2>
                    <p class="mt-4 text-2xl mb-2"><?php echo htmlspecialchars($employed_count); ?></p>
                    <a class="mt-2 text-s font-bold" style="color:blue;" href="residents.php?filter=employed">View all</a>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow flex items-center widget">
                <img src="6.png" alt="Unemployed Icon" class="w-14 h-14 mr-6 ml-2">
                <div>
                    <h2 class="text-xl font-bold">Unemployed</h2>
                    <p class="mt-4 text-2xl mb-2"><?php echo htmlspecialchars($unemployed_count); ?></p>
                    <a class="mt-2 text-s font-bold" style="color:blue;" href="residents.php?filter=unemployed">View all</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const avatar = document.getElementById('avatar-only');
        const sidebar = document.getElementById('sidebar');
        const sidebarAvatar = document.getElementById('sidebar-avatar');
        const dashboard = document.getElementById('dashboard');
        const results = document.getElementById('results');
        const searchBox = document.getElementById('search-box');
        const searchBtn = document.getElementById('search-btn');
        const backBtn = document.getElementById('back-btn');
        const errorMessage = document.getElementById('error-message');


        avatar.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            avatar.classList.add('active');
        });

        sidebarAvatar.addEventListener('click', () => {
            sidebar.classList.remove('active');
            avatar.classList.remove('hidden');
        });

              // Display search results when there are any
              <?php if (!empty($search_term)): ?>
            dashboard.style.display = 'none';
            results.style.display = 'block';
            backBtn.style.display = 'block';
            searchBox.value = '<?php echo htmlspecialchars($search_term); ?>';
        <?php endif; ?>

        // Function to perform search
        function performSearch() {
            const searchTerm = searchBox.value.trim();
            if (searchTerm) {
                window.location.href = `admin.php?search=${encodeURIComponent(searchTerm)}`;
            } else {
                alert("Please enter in search box.");
            }
        }

        // Search button functionality
        searchBtn.addEventListener('click', performSearch);

        // Back button functionality
        backBtn.addEventListener('click', () => {
            dashboard.style.display = 'grid';
            results.style.display = 'none';
            backBtn.style.display = 'none';
            searchBox.value = '';
        });

    // Function to display user details in a modal
        function showUserDetails(user) {
    // Fill in user details in the modal
    document.getElementById('userFullName').innerText = user.first_name + ' ' + user.middle_name + ' ' + user.last_name;
    document.getElementById('userEmail').innerText = user.email;
    document.getElementById('userFirstName').innerText = user.first_name;
    document.getElementById('userMiddleName').innerText = user.middle_name;
    document.getElementById('userLastName').innerText = user.last_name;
    document.getElementById('userAddress').innerText = user.address;

    // Show the modal
    document.getElementById('userDetailsModal').classList.remove('hidden');
}

    // Function to close the modal
    function closeModal() {
    document.getElementById('userDetailsModal').classList.add('hidden');
}
        // Back button functionality
        backBtn.addEventListener('click', () => {
            dashboard.style.display = 'grid';
            results.style.display = 'none';
            backBtn.style.display = 'none';
            searchBox.value = ''; // Clear the search box
        });
    </script>
</body>
</html>
