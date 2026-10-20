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
    <title>Alihaha - My Coupons</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-blue-800 font-sans pb-16">
    <header class="glass backdrop-blur-md sticky top-0 z-50 p-4 flex items-center gap-3">
        <a href="my-account.php" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-base font-bold text-orange-500">My Coupons</h1>
    </header>
    
    <div class="text-center py-20 px-4">
        <div class="w-24 h-24 mx-auto mb-4 bg-yellow-500/20 rounded-full flex items-center justify-center animate-float">
            <i class="fas fa-ticket-alt text-4xl text-yellow-400"></i>
        </div>
        <h2 class="text-lg font-bold text-white mb-2">No Coupons Available</h2>
        <p class="text-sm text-gray-300 mb-6">You don't have any discount coupons right now.</p>
        <a href="products.php" class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-bold px-6 py-2.5 rounded-full shadow-lg">Shop Now</a>
    </div>
</body>
</html>