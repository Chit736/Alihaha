<?php
session_start();
include 'connection.php';
include 'function.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alihaha - Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-blue-800 font-sans pb-16">
    <header class="glass backdrop-blur-md sticky top-0 z-50 p-4 flex items-center gap-3 border-b border-gray-100">
        <a href="my-account.php" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-orange-100"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-base font-bold text-orange-500">Settings</h1>
    </header>
    
    <div class="max-w-md mx-auto pt-10 px-4">
        <div class="glass rounded-3xl p-6">
            <h2 class="text-base font-bold text-white mb-4">App Preferences</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-200">Country</span>
                    <span class="text-sm font-bold text-orange-400">🇲🇲 Myanmar</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-200">Currency</span>
                    <span class="text-sm font-bold text-orange-400">MMK (K)</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-200">Language</span>
                    <span class="text-sm font-bold text-orange-400">English</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>