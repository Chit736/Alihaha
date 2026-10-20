<?php
session_start();
include 'connection.php';
include 'function.php';

// Admin ဟုတ်မဟုတ် စစ်မယ်
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=" . urlencode("Access Denied. Admins only."));
    exit();
}

// Admin အတွက် စာရင်းအင်းများ တွက်ချက်ခြင်း
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total) as sum FROM orders")->fetch_assoc()['sum'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Alihaha</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #1e40af; /* blue-800 */ }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="font-sans min-h-screen p-4 pb-20">

    <!-- Header -->
    <header class="max-w-4xl mx-auto flex items-center justify-between mb-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-shield text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">Admin Dashboard</h1>
                <p class="text-xs text-blue-200">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            </div>
        </div>
        <a href="index.php" class="glass-card px-4 py-2 rounded-xl text-white text-sm hover:bg-white/20 transition">
            <i class="fas fa-home mr-1"></i> Main Site
        </a>
    </header>

    <!-- Stats Grid -->
    <div class="max-w-4xl mx-auto grid grid-cols-2 gap-4 mb-8">
        <!-- Users -->
        <div class="glass-card rounded-2xl p-5">
            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mb-3">
                <i class="fas fa-users text-white"></i>
            </div>
            <p class="text-3xl font-bold text-white"><?php echo $total_users; ?></p>
            <p class="text-sm text-blue-200">Total Users</p>
        </div>
        
        <!-- Products -->
        <div class="glass-card rounded-2xl p-5">
            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mb-3">
                <i class="fas fa-box text-white"></i>
            </div>
            <p class="text-3xl font-bold text-white"><?php echo $total_products; ?></p>
            <p class="text-sm text-blue-200">Total Products</p>
        </div>

        <!-- Orders -->
        <div class="glass-card rounded-2xl p-5">
            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mb-3">
                <i class="fas fa-shopping-cart text-white"></i>
            </div>
            <p class="text-3xl font-bold text-white"><?php echo $total_orders; ?></p>
            <p class="text-sm text-blue-200">Total Orders</p>
        </div>

        <!-- Revenue -->
        <div class="glass-card rounded-2xl p-5">
            <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mb-3">
                <i class="fas fa-dollar-sign text-white"></i>
            </div>
            <p class="text-3xl font-bold text-white">$<?php echo number_format($total_revenue, 2); ?></p>
            <p class="text-sm text-blue-200">Total Revenue</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="max-w-4xl mx-auto">
        <h2 class="text-lg font-bold text-white mb-4">Manage System</h2>
        <div class="grid grid-cols-1 gap-3">
            <a href="my-order.php" class="glass-card rounded-xl p-4 flex items-center justify-between hover:bg-white/20 transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-clipboard-list text-orange-400"></i>
                    <span class="text-white font-medium">View All Orders</span>
                </div>
                <i class="fas fa-chevron-right text-blue-300"></i>
            </a>
            <a href="products.php" class="glass-card rounded-xl p-4 flex items-center justify-between hover:bg-white/20 transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-box text-green-400"></i>
                    <span class="text-white font-medium">Manage Products</span>
                </div>
                <i class="fas fa-chevron-right text-blue-300"></i>
            </a>
        </div>
    </div>

</body>
</html>