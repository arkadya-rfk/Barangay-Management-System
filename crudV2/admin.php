<?php
session_start();

// Redirect if the user is not logged in or not an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$admin = $_SESSION['user'];

require 'db.php'; // Include database connection

// Enable detailed error reporting for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Dashboard data queries
    $residents_count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'user'")->fetch_assoc()['total'];
    $males_count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE gender = 'male' AND role = 'user'")->fetch_assoc()['total'];
    $females_count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE gender = 'female' AND role = 'user'")->fetch_assoc()['total'];
    $students_count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE achieved_status = 'student' AND role = 'user'")->fetch_assoc()['total'];
    $employed_count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE achieved_status = 'employed' AND role = 'user'")->fetch_assoc()['total'];
    $unemployed_count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE achieved_status = 'unemployed' AND role = 'user'")->fetch_assoc()['total'];

    // Handle search functionality
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
    die("Error: " . $e->getMessage());
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

    <!-- Sidebar -->
    <aside id="sidebar1" class="bg-blue-800 text-white p-5 fixed top-0 left-0 h-full w-64">
        <div class="flex items-center mb-8">
            <img src="icons/8.png" alt="Admin Avatar" class="w-20 h-20 rounded-full">
            <h2 class="text-xl font-bold ml-4">ADMIN</h2>
        </div>
        <nav>
            <ul>
                <li class="mb-4"><a href="admin.php" class="text-white hover:text-black font-bold">Home</a></li>
                <li class="mb-4"><a href="residents.php" class="text-white hover:text-black hover:font-bold">List of Residents</a></li>
                <li class="mb-4"><a href="blotterReports.php" class="text-white hover:text-black hover:font-bold">Blotter Reports</a></li>
                <li class="mb-4"><a href="logout.php" class="text-white hover:text-black hover:font-bold">Log Out</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <h1 class="text-3xl font-bold mb-10 mt-48 text-center">Barangay Management System</h1>

        <div class="flex items-center justify-center mb-8">
            <input type="text" id="search-box" placeholder="Search by First name" class="border rounded-lg p-2 w-96">
            <button id="search-btn" class="ml-4 bg-blue-500 text-white p-2 rounded">Search</button>
        </div>



        <!-- Search Results -->
        <section id="results" class="hidden">
            <h2 class="text-2xl font-bold mb-4">Search Results:</h2>
            <?php if (!empty($search_term)): ?>
                <?php if (empty($search_results)): ?>
                    <p>No results found for "<?php echo htmlspecialchars($search_term); ?>"</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($search_results as $result): ?>
                            <div 
                                class="bg-white shadow p-4 rounded cursor-pointer"
                                onclick="showUserDetails(<?php echo htmlspecialchars(json_encode($result)); ?>)">
                                <p class="font-bold text-lg">
                                    <?php echo htmlspecialchars($result['first_name'] . ' ' . $result['middle_name'] . ' ' . $result['last_name']); ?>
                                </p>
                                <p>Username: <?php echo htmlspecialchars($result['email']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>

        <!-- Dashboard Widgets -->
        <section id="dashboard" class="grid grid-cols-3 gap-6">
            <!-- Widget Template -->
            <?php
            $widgets = [
                ["Residents", $residents_count, "residents.php", "1.png"],
                ["Male", $males_count, "residents.php?filter=male", "3.png"],
                ["Female", $females_count, "residents.php?filter=female", "4.png"],
                ["Students", $students_count, "residents.php?filter=students", "2.png"],
                ["Employed", $employed_count, "residents.php?filter=employed", "5.png"],
                ["Unemployed", $unemployed_count, "residents.php?filter=unemployed", "6.png"]
            ];

            foreach ($widgets as [$label, $count, $link, $icon]): ?>
                <div class="bg-white p-6 rounded-lg shadow flex items-center">
                    <img src="icons/<?php echo $icon; ?>" alt="<?php echo $label; ?> Icon" class="w-14 h-14 mr-4">
                    <div>
                        <a href="<?php echo $link; ?>" class="text-xl font-bold text-blue-600"><?php echo $label; ?></a>
                        <p class="text-2xl mt-2"><?php echo htmlspecialchars($count); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </main>

    <!-- User Details Modal -->
    <div id="userDetailsModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-8 rounded-lg w-1/3">
            <button class="bg-red-500 text-white px-4 py-2 rounded mb-4" onclick="closeModal()">Close</button>
            <h3 class="text-2xl font-bold mb-4" id="userFullName"></h3>
            <p><strong>Email:</strong> <span id="userEmail"></span></p>
            <p><strong>First Name:</strong> <span id="userFirstName"></span></p>
            <p><strong>Middle Name:</strong> <span id="userMiddleName"></span></p>
            <p><strong>Last Name:</strong> <span id="userLastName"></span></p>
            <p><strong>Address:</strong> <span id="userAddress"></span></p>
        </div>
    </div>

    <script src="admin.js"></script>
</body>
</html>
