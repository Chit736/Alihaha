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
    <title>Alihaha - App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-blue-800 font-sans pb-16">
    <header class="glass backdrop-blur-md sticky top-0 z-50 p-4 flex items-center gap-3 border-b border-gray-100">
        <a href="my-account.php" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-orange-100"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-base font-bold text-orange-500">Alihaha App</h1>
    </header>
    
    <div class="max-w-md mx-auto pt-10 px-4 text-center">
        <div class="glass rounded-3xl p-6">
            <div class="w-20 h-20 mx-auto mb-4 bg-orange-500/20 rounded-full flex items-center justify-center animate-float">
                <i class="fas fa-mobile-alt text-4xl text-orange-400"></i>
            </div>
            <h2 class="text-xl font-bold text-white mb-2">Get the Alihaha App</h2>
            <p class="text-sm text-gray-300 mb-6">Shop on the go with our mobile app. Available for iOS and Android.</p>
            <div class="flex flex-col gap-3">
                <a href="#" class="bg-black text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2"><i class="fab fa-apple text-xl"></i> App Store</a>
                <a href="#" class="bg-black text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2"><i class="fab fa-google-play text-xl text-green-400"></i> Google Play</a>
            </div>
        </div>
    </div>
</body>
</html>