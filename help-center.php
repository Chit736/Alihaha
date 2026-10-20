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
    <title>Alihaha - Help Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-blue-800 font-sans pb-16">
    <header class="glass backdrop-blur-md sticky top-0 z-50 p-4 flex items-center gap-3 border-b border-gray-100">
        <a href="my-account.php" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-orange-100"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-base font-bold text-orange-500">Help Center</h1>
    </header>
    
    <div class="max-w-md mx-auto pt-10 px-4">
        <div class="glass rounded-3xl p-6 text-center">
            <div class="w-20 h-20 mx-auto mb-4 bg-cyan-500/20 rounded-full flex items-center justify-center">
                <i class="fas fa-question-circle text-4xl text-cyan-400"></i>
            </div>
            <h2 class="text-xl font-bold text-white mb-2">How can we help?</h2>
            <p class="text-sm text-gray-300 mb-6">Contact our 24/7 support team for any issues.</p>
            <a href="messenger.php" class="inline-block bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold px-6 py-2.5 rounded-full shadow-lg"><i class="fas fa-comment mr-2"></i>Chat with Support</a>
        </div>
    </div>
</body>
</html>