<?php
session_start();
require 'db.php'; // Database connection

// Ensure the user is logged in and has the 'admin' role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch all blotter reports from the database
$sql = "SELECT b.id, b.incident_date_time, b.incident_location, b.incident_description, b.type_of_incident, 
        b.involved_parties, b.witnesses, b.desired_resolution, b.status, u.first_name, u.last_name 
        FROM blotters b 
        JOIN users u ON b.user_id = u.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blotter Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h2 class="text-2xl font-bold mb-6">Blotter Reports</h2>
                <!-- Back Button -->
                <a href="admin.php" class="inline-block bg-gray-500 text-white font-bold py-2 px-4 rounded hover:bg-gray-700 mb-4">
            Back
        </a>
        <table class="min-w-full bg-white rounded-lg shadow-lg">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Reported By</th>
                    <th class="py-3 px-6 text-left">Date & Time</th>
                    <th class="py-3 px-6 text-left">Location</th>
                    <th class="py-3 px-6 text-left">Description</th>
                    <th class="py-3 px-6 text-left">Type</th>
                    <th class="py-3 px-6 text-left">Status</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm font-light">
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                    <td class="py-3 px-6 text-left"><?= htmlspecialchars($row['incident_date_time']) ?></td>
                    <td class="py-3 px-6 text-left"><?= htmlspecialchars($row['incident_location']) ?></td>
                    <td class="py-3 px-6 text-left"><?= htmlspecialchars($row['incident_description']) ?></td>
                    <td class="py-3 px-6 text-left"><?= htmlspecialchars($row['type_of_incident']) ?></td>
                    <td class="py-3 px-6 text-left"><?= htmlspecialchars($row['status']) ?></td>
                    <td class="py-3 px-6 text-center">
                        <form action="update_blotter_status.php" method="POST" class="inline-block">
                            <input type="hidden" name="blotter_id" value="<?= $row['id'] ?>">
                            <select name="status" class="bg-gray-200 border border-gray-400 rounded px-2 py-1">
                                <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Resolved" <?= $row['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                <option value="Escalated" <?= $row['status'] === 'Escalated' ? 'selected' : '' ?>>Escalated</option>
                            </select>
                            <button type="submit" class="bg-blue-600 text-white font-bold py-1 px-3 rounded hover:bg-blue-700 ml-2">
                                Update
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
