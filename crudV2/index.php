<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <script>
        function redirectWithDelay(url) {
            // Show loader and hide buttons
            document.getElementById('content').style.display = 'none';
            document.getElementById('loader').style.display = 'flex'; // Make loader visible and centered

            // Delay the redirect
            setTimeout(function() {
                window.location.href = url;
            }, 500); // 1.5 seconds delay
        }
    </script>
</head>
<body class="bg-blue-50 relative">

<div class="absolute top-0 left-0 w-32 h-32 bg-blue-200 rounded-full opacity-50"></div>
<div class="absolute bottom-0 right-0 w-48 h-48 bg-blue-300 rounded-full opacity-50"></div>

<!-- Loader (hidden by default) -->
<div id="loader" class="hidden justify-center items-center h-screen">
    <div class="border-t-4 border-blue-500 rounded-full w-12 h-12 animate-spin"></div>
</div>

<!-- Main content -->
<div id="content" class="flex justify-center items-center h-screen">
    <div class="bg-gray-100 p-10 rounded-lg shadow-lg text-center">
        <h1 class="text-2xl font-bold mb-4">MANAGEMENT SYSTEM</h1>
        <div class="space-x-4">
            <a href="javascript:void(0)" onclick="redirectWithDelay('login.php')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 active:bg-blue-900">LOGIN</a>
            <a href="javascript:void(0)" onclick="redirectWithDelay('register.php')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 active:bg-blue-900">SIGN UP</a>
        </div>
    </div>
</div>

</body>
</html>
