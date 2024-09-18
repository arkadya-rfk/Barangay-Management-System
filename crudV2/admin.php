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
    if (isset($_GET['search'])) {
        $search_term = $conn->real_escape_string($_GET['search']);
        $search_query = $conn->query("
            SELECT first_name, middle_name, last_name, email 
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
    <style>
        .center-content {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 80vh;
            margin-top: 40px;
        }

        #sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 250px;
            background-color: #1E40AF;
            transition: transform 0.3s ease;
            transform: translateX(-250px);
        }

        #sidebar.active {
            transform: translateX(0);
        }

        #avatar-only
        .sidebar-avatar {
            position: fixed;
            top: 40px;
            left: 40px;
            transition: opacity 0.3s ease;
            z-index: 10;
        }

        #avatar-only.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .widget {
            width: 400px;
        }

        /* Hide results div initially */
        #results {
            display: none;
        }

        /* Hide back button initially */
        #back-btn {
            display: none;
        }
    </style>
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
            <li class="mb-4"><a href="#" class="text-white hover:text-black hover:font-bold">Pending Approvals</a></li>
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
        <!-- Error Message -->
        <div id="error-message" class="bg-red-500 text-white font-bold px-6 py-4 rounded shadow-lg mb-8" style="display: none;">Please enter in the search box.</div>

        <!-- Back button (hidden initially) -->
        <button id="back-btn" class="mb-4 p-2 bg-gray-500 text-white rounded">Back to Dashboard</button>

        <!-- Search box -->
        <div class="mb-8 flex items-center">
            <input type="text" id="search-box" placeholder="Search by First name" class="border rounded-lg p-2 w-96">
            <button id="search-btn" class="ml-4 p-2 bg-blue-500 text-white rounded">Search</button>
        </div>

        <!-- Dashboard widgets -->
        <div id="dashboard" class="grid grid-cols-3 gap-6">
            <!-- Dashboard content here -->
            <div class="bg-white p-6 rounded-lg shadow flex items-center widget">
                <img src="1.png" alt="Residents Icon" class="w-12 h-12 mr-6">
                <div>
                    <h2 class="text-xl font-bold">Residents</h2>
                    <p class="mt-4 text-2xl"><?php echo htmlspecialchars($residents_count); ?></p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow flex items-center widget">
                <img src="3.png" alt="Male Icon" class="w-16 h-16 mr-6">
                <div>
                    <h2 class="text-xl font-bold">Male</h2>
                    <p class="mt-4 text-2xl"><?php echo htmlspecialchars($males_count); ?></p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow flex items-center widget">
                <img src="4.png" alt="Female Icon" class="w-16 h-16 mr-6">
                <div>
                    <h2 class="text-xl font-bold">Female</h2>
                    <p class="mt-4 text-2xl"><?php echo htmlspecialchars($females_count); ?></p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow flex items-center widget">
                <img src="2.png" alt="Students Icon" class="w-14 h-14 mr-4">
                <div>
                    <h2 class="text-xl font-bold">Students</h2>
                    <p class="mt-4 text-2xl"><?php echo htmlspecialchars($students_count); ?></p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow flex items-center widget">
                <img src="5.png" alt="Employed Icon" class="w-14 h-14 mr-6 ml-2">
                <div>
                    <h2 class="text-xl font-bold">Employed</h2>
                    <p class="mt-4 text-2xl"><?php echo htmlspecialchars($employed_count); ?></p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow flex items-center widget">
                <img src="6.png" alt="Unemployed Icon" class="w-14 h-14 mr-6 ml-2">
                <div>
                    <h2 class="text-xl font-bold">Unemployed</h2>
                    <p class="mt-4 text-2xl"><?php echo htmlspecialchars($unemployed_count); ?></p>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div id="results">
            <h2 class="text-2xl font-bold mb-4">Search Results:</h2>
            <ul>
                <?php if (isset($_GET['search'])): ?>
                    <?php if (empty($search_results)): ?>
                        <p>No results found for "<?php echo htmlspecialchars($search_term); ?>"</p>
                    <?php else: ?>
                        <?php foreach ($search_results as $result): ?>
                            <li>
                                <p class="font-bold">
                                    <?php 
                                        echo htmlspecialchars($result['first_name']) . ' ' . htmlspecialchars($result['middle_name']) . ' ' . htmlspecialchars($result['last_name']); 
                                    ?>
                                </p>
                                <p>Username: <?php echo htmlspecialchars($result['email']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
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
            avatar.classList.add('hidden');
        });

        sidebarAvatar.addEventListener('click', () => {
            sidebar.classList.remove('active');
            avatar.classList.remove('hidden');
        });

        // Function to perform search
        function performSearch() {
    const searchTerm = searchBox.value.trim();

    if (!searchTerm) {
        errorMessage.style.display = 'block'; // Show the error message
        return;
    }

    errorMessage.style.display = 'none'; // Hide the error message
    window.location.href = `?search=${encodeURIComponent(searchTerm)}`;
}


        // Search on button click
        searchBtn.addEventListener('click', performSearch);

        // Search on Enter key press
        searchBox.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                performSearch();
            }
        });

        // Show search results and hide dashboard when a search is performed
        <?php if (isset($_GET['search'])): ?>
            dashboard.style.display = 'none';
            results.style.display = 'block';
            backBtn.style.display = 'block';
        <?php endif; ?>

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
