<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require 'db.php'; // Database connection

// Error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$filter = '';
$search_term = '';
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
} elseif (isset($_POST['filter'])) {
    $filter = $_POST['filter'];
}

if (isset($_POST['search_term'])) {
    $search_term = $_POST['search_term'];
}

try {
    // Fetch residents data with filter and search
    $query = "SELECT first_name, middle_name, last_name, gender, achieved_status FROM users WHERE role = 'user'";
    if ($filter) {
        switch ($filter) {
            case 'male':
                $query .= " AND gender = 'male'";
                break;
            case 'female':
                $query .= " AND gender = 'female'";
                break;
            case 'students':
                $query .= " AND achieved_status = 'student'";
                break;
            case 'employed':
                $query .= " AND achieved_status = 'employed'";
                break;
            case 'unemployed':
                $query .= " AND achieved_status = 'unemployed'";
                break;
        }
    }

    if ($search_term) {
        $query .= " AND first_name LIKE '%" . $conn->real_escape_string($search_term) . "%'";
    }

    $residents_query = $conn->query($query);
    $residents = $residents_query->fetch_all(MYSQLI_ASSOC);

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
    <title>Residents</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

    <div id="sidebar" class="bg-blue-800 text-white p-5 transition-transform rounded-r-lg">
        <div class="flex items-center">
            <img id="sidebar-avatar" src="8.png" alt="Admin Avatar" class="sidebar-avatar w-20 h-20 rounded-full cursor-pointer">
            <h2 class="text-xl font-bold ml- mt-2">ADMIN</h2>
        </div>
        <ul class="mt-16">
            <li class="mb-4"><a href="admin.php" class="text-white hover:text-black hover:font-bold">Home</a></li>
            <li class="mb-4"><a href="residents.php" class="text-white hover:text-black font-bold">List of Residents</a></li>
            <li class="mb-4"><a href="logout.php" class="text-white hover:text-black hover:font-bold">Log Out</a></li>
        </ul>
    </div>

    <!-- Avatar for toggling the sidebar -->
        <div id="avatar-only" class="p-5 cursor-pointer">
            <img src="7.png" alt="Admin Avatar" class="w-20 h-20 rounded-full">
        </div>
         

    <!-- Main content -->
    <div class="ml-64 p-6">
        <h1 class="text-3xl font-bold mb-10">List of Residents</h1>

        <!-- Search Box -->
        <form method="POST" class="mb-6">
            <input type="text" name="search_term" id="search-box" placeholder="Search by First Name" value="<?= htmlspecialchars($search_term); ?>" class="w-full p-2 border rounded mb-4">
            <button type="submit" class="bg-blue-500 text-white p-2 rounded">Search</button>
        </form>

        <!-- Sorting options -->
        <form method="POST" class="mb-6">
            <label for="filter" class="block text-lg font-semibold mb-2">Sort by:</label>
            <select name="filter" id="filter" class="w-full p-2 border rounded">
                <option value="">All Residents</option>
                <option value="male" <?= $filter === 'male' ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= $filter === 'female' ? 'selected' : '' ?>>Female</option>
                <option value="students" <?= $filter === 'students' ? 'selected' : '' ?>>Students</option>
                <option value="employed" <?= $filter === 'employed' ? 'selected' : '' ?>>Employed</option>
                <option value="unemployed" <?= $filter === 'unemployed' ? 'selected' : '' ?>>Unemployed</option>
            </select>
            <button type="submit" class="mt-4 bg-blue-500 text-white p-2 rounded">Filter</button>
        </form>

        <!-- Residents Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-blue-800 text-white">
                    <tr>
                        <th class="p-4 text-left">First Name</th>
                        <th class="p-4 text-left">Middle Name</th>
                        <th class="p-4 text-left">Last Name</th>
                        <th class="p-4 text-left">Gender</th>
                        <th class="p-4 text-left">Achieved Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($residents)): ?>
                        <tr>
                            <td colspan="5" class="p-4 text-center">No residents found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($residents as $resident): ?>
                            <tr class="border-b">
                                <td class="p-4"><?= htmlspecialchars($resident['first_name']); ?></td>
                                <td class="p-4"><?= htmlspecialchars($resident['middle_name']); ?></td>
                                <td class="p-4"><?= htmlspecialchars($resident['last_name']); ?></td>
                                <td class="p-4"><?= htmlspecialchars(ucfirst($resident['gender'])); ?></td>
                                <td class="p-4"><?= htmlspecialchars(ucfirst($resident['achieved_status'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        const avatar = document.getElementById('avatar-only');
        const sidebar = document.getElementById('sidebar');
        const sidebarAvatar = document.getElementById('sidebar-avatar');

        avatar.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            avatar.classList.add('active');
        });

        sidebarAvatar.addEventListener('click', () => {
            sidebar.classList.remove('active');
            avatar.classList.remove('hidden');
        });
    </script>
</body>
</html>
