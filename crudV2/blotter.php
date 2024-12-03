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
    <title>Blotter Request</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-center">File a Blotter</h2>
        
        <form action="submit_blotter.php" method="POST" class="space-y-4">
            <!-- Incident Date and Time -->
            <div>
                <label for="incident_date_time" class="block text-sm font-medium text-gray-700">Date and Time of Incident</label>
                <input type="datetime-local" id="incident_date_time" name="incident_date_time" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <!-- Incident Location -->
            <div>
                <label for="incident_location" class="block text-sm font-medium text-gray-700">Location of Incident</label>
                <input type="text" id="incident_location" name="incident_location" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <!-- Incident Description -->
            <div>
                <label for="incident_description" class="block text-sm font-medium text-gray-700">Description of Incident</label>
                <textarea id="incident_description" name="incident_description" rows="4" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
            </div>

            <!-- Type of Incident -->
            <div>
                <label for="type_of_incident" class="block text-sm font-medium text-gray-700">Type of Incident</label>
                <select id="type_of_incident" name="type_of_incident" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Select an incident type</option>
                    <option value="Dispute">Dispute</option>
                    <option value="Theft">Theft</option>
                    <option value="Harassment">Harassment</option>
                    <option value="Property Damage">Property Damage</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- Involved Parties -->
            <div>
                <label for="involved_parties" class="block text-sm font-medium text-gray-700">Involved Parties</label>
                <input type="text" id="involved_parties" name="involved_parties"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <!-- Witnesses -->
            <div>
                <label for="witnesses" class="block text-sm font-medium text-gray-700">Witnesses (if any)</label>
                <input type="text" id="witnesses" name="witnesses"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <!-- Desired Resolution -->
            <div>
                <label for="desired_resolution" class="block text-sm font-medium text-gray-700">Desired Resolution</label>
                <textarea id="desired_resolution" name="desired_resolution" rows="3"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
            </div>

            <!-- Button Container -->
            <div class="flex justify-between items-center mb-4">
                
            <!-- Back Button -->
                <a href="user_dashboard.php" class="bg-gray-500 text-white font-bold py-2 px-4 rounded hover:bg-gray-700">Back</a>

            <!-- Submit Button -->
                <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Submit Blotter</button>
            </div>

        </form>
    </div>

</body>
</html>
