<?php

// index.php

require 'credentials.php';
require 'keyauth.php';

// Check if a session already exists to avoid issues
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to dashboard if user is already logged in
if (isset($_SESSION['un'])) {
    header("Location: dashboard/index.php"); // Updated path
    exit();
}

// Initialize the KeyAuth API
$KeyAuthApp = new KeyAuth\api($name, $OwnerId);

if (!isset($_SESSION['sessionid'])) {
    $KeyAuthApp->init();
}

// Check if the URL parameter for the login page is set
$showLoginPage = isset($_GET['page']) && $_GET['page'] === 'login';

?>

<!DOCTYPE html>
<html lang="en" class="bg-gray-950 text-white font-sans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IrisFN</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: radial-gradient(at 10% 20%, rgb(18, 18, 18) 0%, rgb(10, 10, 10) 50%, rgb(5, 5, 5) 100%);
        }
        .glow-button, .glow-input {
            transition: all 0.3s ease;
            box-shadow: 0 0 5px #a855f7, 0 0 10px #a855f7, 0 0 15px #a855f7;
        }
        .glow-button:hover, .glow-input:focus {
            box-shadow: 0 0 8px #c084fc, 0 0 15px #c084fc, 0 0 20px #c084fc;
        }
        /* New animations */
        .feature-box {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .feature-box:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 6px -1px rgba(168, 85, 247, 0.3), 0 2px 4px -1px rgba(168, 85, 247, 0.2);
        }
        .login-button-animate {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .login-button-animate:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 8px #c084fc, 0 0 15px #c084fc, 0 0 20px #c084fc, 0 4px 6px -1px rgba(192, 132, 252, 0.3), 0 2px 4px -1px rgba(192, 132, 252, 0.2);
        }
        .input-animate {
            transition: box-shadow 0.3s ease-in-out;
        }
        .input-animate:focus {
            box-shadow: 0 0 8px #c084fc, 0 0 15px #c084fc, 0 0 20px #c084fc;
        }
    </style>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</head>
<body class="flex items-center justify-center min-h-screen">

    <?php if ($showLoginPage): ?>
        <div class="w-full max-w-sm p-6 space-y-5 bg-gray-950 rounded-md shadow-2xl border border-purple-900">
            <div class="text-center mb-4">
                <h1 class="text-2xl font-bold text-white mb-1">IrisFN Panel</h1>
                <p class="text-sm text-gray-400">Enter your license key to access your panel.</p>
            </div>
            <form class="space-y-4" method="post">
                <div class="relative">
                    <input type="text" name="key" id="key"
                        class="block w-full px-4 py-3 text-white bg-black border border-gray-900 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500 placeholder-gray-500 transition-colors glow-input input-animate"
                        placeholder="License Key" required>
                </div>
                <div>
                    <button type="submit" name="login"
                        class="w-full px-5 py-3 text-lg font-medium text-center text-white bg-purple-700 rounded-md hover:bg-purple-800 glow-button login-button-animate transition ease-in-out duration-150">
                        Login
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="flex flex-col items-center justify-center text-center p-8 max-w-4xl mx-auto">
            <h1 class="text-5xl font-bold mb-4 text-purple-400">Welcome to IrisFN</h1>
            <p class="text-lg text-gray-300 mb-8 max-w-2xl">Discover why thousands of users choose IrisFN for a seamless and secure experience. Our cutting-edge technology and user-friendly design make us the top choice.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="p-6 bg-gray-900 rounded-lg border border-purple-800 shadow-md feature-box">
                    <h2 class="text-xl font-semibold mb-2 text-purple-300">Fast & Reliable</h2>
                    <p class="text-gray-400">Experience lightning-fast performance with our optimized infrastructure.</p>
                </div>
                <div class="p-6 bg-gray-900 rounded-lg border border-purple-800 shadow-md feature-box">
                    <h2 class="text-xl font-semibold mb-2 text-purple-300">Secure Panel</h2>
                    <p class="text-gray-400">Your data is safe with our top-tier security measures and encryption.</p>
                </div>
                <div class="p-6 bg-gray-900 rounded-lg border border-purple-800 shadow-md feature-box">
                    <h2 class="text-xl font-semibold mb-2 text-purple-300">24/7 Support</h2>
                    <p class="text-gray-400">Our dedicated team is always ready to assist you with any questions.</p>
                </div>
            </div>

            <a href="?page=login"
               class="px-8 py-4 text-lg font-bold text-center text-white bg-purple-700 rounded-full hover:bg-purple-800 glow-button login-button-animate transition ease-in-out duration-150">
               Login to Panel
            </a>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <?php
    if (isset($_POST['login'])) {
        $key = $_POST['key'];
        if ($KeyAuthApp->license($key)) {
            $_SESSION['un'] = $key; // Using key as username for simplicity
            echo "<meta http-equiv='Refresh' Content='2; url=dashboard/index.php'>"; // Updated path
            echo '
            <script type="text/javascript">
                const notyf = new Notyf();
                notyf.success({
                    message: "Login successful!",
                    duration: 3500,
                    dismissible: true
                });
            </script>';
        } else {
            echo '
            <script type="text/javascript">
                const notyf = new Notyf();
                notyf.error({
                    message: "Invalid license key.",
                    duration: 3500,
                    dismissible: true
                });
            </script>';
        }
    }
    ?>
</body>
</html>