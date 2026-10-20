<?php
session_start();
include 'connection.php';
include 'function.php';

$current_page = basename($_SERVER['PHP_SELF']);

$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$user_name = $is_logged_in ? ($_SESSION['user_name'] ?? 'User') : 'Guest';
$user_id = $is_logged_in ? intval($_SESSION['user_id']) : 0;
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';


// User Stats (Login ဝင်ထားရင်)

$total_orders = 0;
$pending_orders = 0;
$completed_orders = 0;
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

if ($is_logged_in) {
    // Total orders
    $sql = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $total_orders = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Pending orders
    $sql = "SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $pending_orders = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Completed orders
    $sql = "SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND status = 'completed'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $completed_orders = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
}


//  Menu Items

$main_menu = [
    [
        'href' => 'my_order.php',
        'icon' => 'fa-clipboard-list',
        'label' => 'Manage Orders',
        'subtitle' => $total_orders . ' orders',
        'gradient' => 'from-orange-400 to-red-500',
        'badge' => $pending_orders > 0 ? $pending_orders : null,
        'badge_color' => 'bg-red-500'
    ],
    [
        'href' => 'messenger.php',
        'icon' => 'fa-comment-dots',
        'label' => 'Messenger',
        'subtitle' => 'Chat with suppliers',
        'gradient' => 'from-blue-400 to-indigo-600',
        'badge' => null,
        'badge_color' => ''
    ],
    [
        'href' => 'cart.php',
        'icon' => 'fa-shopping-cart',
        'label' => 'Shopping Cart',
        'subtitle' => $cart_count . ' items',
        'gradient' => 'from-green-400 to-emerald-600',
        'badge' => $cart_count > 0 ? $cart_count : null,
        'badge_color' => 'bg-green-500'
    ],
    [
        'href' => 'favorites.php',
        'icon' => 'fa-heart',
        'label' => 'My Favorites',
        'subtitle' => 'Saved items',
        'gradient' => 'from-pink-400 to-rose-600',
        'badge' => null,
        'badge_color' => ''
    ],
    [
        'href' => 'coupons.php',
        'icon' => 'fa-ticket-alt',
        'label' => 'My Coupons',
        'subtitle' => 'Discounts & offers',
        'gradient' => 'from-yellow-400 to-orange-500',
        'badge' => null,
        'badge_color' => ''
    ],
];

$seller_menu = [
    [
        'href' => 'how-to-sell.php',
        'icon' => 'fa-user-tie',
        'label' => 'How to sell on Alihaha',
        'subtitle' => 'Start your business',
        'gradient' => 'from-purple-400 to-indigo-600',
    ],
    [
        'href' => 'help-center.php',
        'icon' => 'fa-question-circle',
        'label' => 'Help Center',
        'subtitle' => 'Get support',
        'gradient' => 'from-cyan-400 to-blue-600',
    ],
];

$settings_menu = [
    [
        'href' => 'settings.php',
        'icon' => 'fa-globe',
        'label' => 'Country & Currency',
        'subtitle' => '🇲🇲 Myanmar • MMK',
        'gradient' => 'from-teal-400 to-green-600',
    ],
    [
        'href' => 'settings.php',
        'icon' => 'fa-cog',
        'label' => 'Settings',
        'subtitle' => 'App preferences',
        'gradient' => 'from-gray-400 to-gray-600',
    ],
    [
        'href' => 'app.php',
        'icon' => 'fa-mobile-alt',
        'label' => 'Alihaha App',
        'subtitle' => 'Download now',
        'gradient' => 'from-orange-400 to-red-500',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alihaha - My Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hide-scroll::-webkit-scrollbar {
            display: none;
        }

        .hide-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .bottom-nav-item.active {
            color: #f97316;
        }

        .bottom-nav-item.active i {
            color: #f97316;
        }

        /*  Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.6;
            }

            100% {
                transform: scale(0.95);
                opacity: 1;
            }
        }

        .pulse-ring {
            animation: pulse-ring 2s ease-in-out infinite;
        }

        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in {
            animation: slide-in 0.4s ease-out;
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }

            100% {
                background-position: 1000px 0;
            }
        }

        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }

        /*  Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        /*  Menu Item Hover */
        .menu-item {
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            transform: translateX(4px);
            background: linear-gradient(90deg, #fff7ed 0%, #ffffff 100%);
            border-color: #fdba74;
        }

        /*  Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #f97316, #fb923c, #fdba74);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /*  Decorative Blob */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.35;
            pointer-events: none;
        }

        /*  Icon Box Hover */
        .icon-box {
            transition: transform 0.3s ease;
        }

        .menu-item:hover .icon-box {
            transform: scale(1.1) rotate(-5deg);
        }

        /*  Profile Avatar Ring */
        .profile-ring {
            position: relative;
        }

        .profile-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f97316, #fb923c, #fdba74);
            z-index: -1;
            animation: pulse-ring 3s ease-in-out infinite;
        }

        /*  Arrow Animation */
        .menu-item .arrow {
            transition: transform 0.3s ease;
        }

        .menu-item:hover .arrow {
            transform: translateX(4px);
        }

        /*  Logout Hover */
        .logout-btn {
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: linear-gradient(90deg, #fef2f2 0%, #ffffff 100%);
            transform: translateX(4px);
        }
    </style>
</head>

<body class="bg-blue-800 font-sans pb-20">

    
    <!--  HEADER -->
  
    <header class="glass backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-gray-100">
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="index.php" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-orange-100 hover:text-orange-500 transition">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h1 class="text-base font-bold text-orange-500">My Alihaha</h1>
                    <p class="text-xs text-white">Manage your account</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="settings.php" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-orange-100 hover:text-orange-500 transition">
                    <i class="fas fa-cog text-sm"></i>
                </a>
                <a href="help-center.php" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-orange-100 hover:text-orange-500 transition">
                    <i class="fas fa-question text-sm"></i>
                </a>
            </div>
        </div>
    </header>

    
    <!--  PROFILE HERO -->
    
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-orange-900 text-white mx-4 mt-4 rounded-3xl">
        <div class="blob w-32 h-32 bg-orange-500 top-0 -right-10"></div>
        <div class="blob w-24 h-24 bg-pink-500 bottom-0 -left-10"></div>

        <div class="relative p-5">
            <?php if ($is_logged_in): ?>
                <!-- Logged In Profile -->
                <div class="flex items-center gap-4 mb-5">
                    <!-- Avatar -->
                    <div class="profile-ring relative flex-shrink-0">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white text-3xl font-bold shadow-xl">
                            <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                        </div>
                        <span class="absolute bottom-0 right-0 w-6 h-6 bg-green-500 border-4 border-slate-900 rounded-full"></span>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-300 mb-0.5">Welcome back,</p>
                        <h2 class="text-xl font-bold truncate">
                            <?php echo htmlspecialchars($user_name); ?>
                        </h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs bg-green-500/20 text-green-300 px-2 py-0.5 rounded-full font-medium flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full pulse-ring"></span>
                                Active
                            </span>
                            <span class="text-xs bg-orange-500/20 text-orange-300 px-2 py-0.5 rounded-full font-medium">
                                ⭐ Gold Member
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-3 gap-2">
                    <div class="glass rounded-xl p-3 text-center">
                        <p class="text-orange-400 font-bold text-lg"><?php echo $total_orders; ?></p>
                        <p class="text-xs text-gray-300">Orders</p>
                    </div>
                    <div class="glass rounded-xl p-3 text-center">
                        <p class="text-yellow-400 font-bold text-lg"><?php echo $pending_orders; ?></p>
                        <p class="text-xs text-gray-300">Pending</p>
                    </div>
                    <div class="glass rounded-xl p-3 text-center">
                        <p class="text-green-400 font-bold text-lg"><?php echo $completed_orders; ?></p>
                        <p class="text-xs text-gray-300">Completed</p>
                    </div>
                </div>

            <?php else: ?>
                <!-- Guest -->
                <div class="text-center py-4">
                    <div class="w-20 h-20 mx-auto mb-4 bg-white/10 backdrop-blur rounded-full flex items-center justify-center animate-float">
                        <i class="fas fa-user text-4xl text-orange-400"></i>
                    </div>
                    <h2 class="text-xl font-bold mb-2">Welcome to Alihaha</h2>
                    <p class="text-sm text-gray-300 mb-5 max-w-xs mx-auto">
                        Sign in to access your orders, favorites, and more
                    </p>
                    <div class="flex gap-3 justify-center">
                        <a href="login.php" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold px-6 py-2.5 rounded-2xl shadow-lg shadow-orange-500/30">
                            <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                        </a>
                        <a href="register.php" class="glass text-white font-bold px-6 py-2.5 rounded-2xl">
                            Register
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    
    <!--  QUICK ACTIONS (Logged In) -->
   
    <?php if ($is_logged_in): ?>
        <section class="px-4 pt-4">
            <div class="grid grid-cols-4 gap-2">
                <a href="my-order.php" class="glass rounded-2xl p-3 shadow-sm border border-gray-100 text-center hover:border-orange-200 transition">
                    <div class="w-10 h-10 mx-auto mb-1.5 bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-orange-500"></i>
                    </div>
                    <p class="text-xs font-semibold text-white">Orders</p>
                </a>
                <a href="favorites.php" class="glass rounded-2xl p-3 shadow-sm border border-gray-100 text-center hover:border-orange-200 transition">
                    <div class="w-10 h-10 mx-auto mb-1.5 bg-gradient-to-br from-pink-100 to-pink-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-heart text-pink-500"></i>
                    </div>
                    <p class="text-xs font-semibold text-white">Favorites</p>
                </a>
                <a href="coupons.php" class="glass rounded-2xl p-3 shadow-sm border border-gray-100 text-center hover:border-orange-200 transition">
                    <div class="w-10 h-10 mx-auto mb-1.5 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-ticket-alt text-yellow-500"></i>
                    </div>
                    <p class="text-xs font-semibold text-white">Coupons</p>
                </a>
                <a href="cart.php" class="glass rounded-2xl p-3 shadow-sm border border-gray-100 text-center hover:border-orange-200 transition relative">
                    <div class="w-10 h-10 mx-auto mb-1.5 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-green-500"></i>
                    </div>
                    <p class="text-xs font-semibold text-white">Cart</p>
                    <?php if ($cart_count > 0): ?>
                        <span class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold pulse-ring">
                            <?php echo $cart_count; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </section>
    <?php endif; ?>

    
    <!--  MAIN MENU -->
    
    <section class="px-4 pt-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold text-gray-800">
                <i class="fas fa-th-large text-orange-500 mr-1"></i> My Account
            </h2>
            <span class="text-xs text-white"><?php echo count($main_menu); ?> items</span>
        </div>

        <div class="space-y-2">
            <?php foreach ($main_menu as $item): ?>
                <a href="<?php echo $item['href']; ?>"
                    class="menu-item slide-in flex items-center gap-3 glass rounded-2xl p-3.5 shadow-sm border border-gray-100">
                    <!-- Icon -->
                    <div class="icon-box w-11 h-11 rounded-2xl bg-gradient-to-br <?php echo $item['gradient']; ?> flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas <?php echo $item['icon']; ?> text-base"></i>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-800">
                            <?php echo $item['label']; ?>
                        </h3>
                        <p class="text-xs text-gray-800">
                            <?php echo $item['subtitle']; ?>
                        </p>
                    </div>

                    <!-- Badge -->
                    <?php if ($item['badge']): ?>
                        <span class="<?php echo $item['badge_color']; ?> text-white text-xs w-6 h-6 rounded-full flex items-center justify-center font-bold flex-shrink-0">
                            <?php echo $item['badge']; ?>
                        </span>
                    <?php endif; ?>

                    <!-- Arrow -->
                    <i class="fas fa-chevron-right text-gray-300 text-xs arrow flex-shrink-0"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    
    <!--  SELLER MENU -->
  
    <section class="px-4 pt-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold text-gray-800">
                <i class="fas fa-store text-orange-500 mr-1"></i> Sell on Alihaha
            </h2>
        </div>

        <div class="space-y-2">
            <?php foreach ($seller_menu as $item): ?>
                <a href="<?php echo $item['href']; ?>"
                    class="menu-item slide-in flex items-center gap-3 glass rounded-2xl p-3.5 shadow-sm border border-gray-100">
                    <div class="icon-box w-11 h-11 rounded-2xl bg-gradient-to-br <?php echo $item['gradient']; ?> flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas <?php echo $item['icon']; ?> text-base"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-800">
                            <?php echo $item['label']; ?>
                        </h3>
                        <p class="text-xs text-gray-800">
                            <?php echo $item['subtitle']; ?>
                        </p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 text-xs arrow flex-shrink-0"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    
    <!--  SETTINGS MENU -->
   
    <section class="px-4 pt-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold text-gray-800">
                <i class="fas fa-sliders-h text-orange-500 mr-1"></i> Settings
            </h2>
        </div>

        <div class="space-y-2">
            <?php foreach ($settings_menu as $item): ?>
                <a href="<?php echo $item['href']; ?>"
                    class="menu-item slide-in flex items-center gap-3 glass rounded-2xl p-3.5 shadow-sm border border-gray-100">
                    <div class="icon-box w-11 h-11 rounded-2xl bg-gradient-to-br <?php echo $item['gradient']; ?> flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas <?php echo $item['icon']; ?> text-base"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-gray-800">
                            <?php echo $item['label']; ?>
                        </h3>
                        <p class="text-xs text-gray-800">
                            <?php echo $item['subtitle']; ?>
                        </p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 text-xs arrow flex-shrink-0"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

 
    <!--  CTA BANNER (Guest only) -->
    
    <?php if (!$is_logged_in): ?>
        <section class="px-4 pt-5">
            <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-600 to-red-600 rounded-3xl p-6 text-white">
                <div class="blob w-32 h-32 bg-yellow-300 top-0 -right-10"></div>
                <div class="blob w-24 h-24 bg-pink-400 bottom-0 -left-10"></div>

                <div class="relative text-center">
                    <div class="w-14 h-14 mx-auto mb-3 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center animate-float">
                        <i class="fas fa-gift text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-1">Join Alihaha Today!</h3>
                    <p class="text-xs text-orange-100 mb-4">
                        Get exclusive discounts, track orders, and more
                    </p>
                    <a href="register.php" class="inline-block bg-white text-orange-600 font-bold px-6 py-2.5 rounded-full shadow-lg">
                        <i class="fas fa-user-plus mr-2"></i> Create Account
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!--  ADMIN DASHBOARD BUTTON -->
   
    <?php if ($is_admin): ?>
        <section class="px-4 pt-5 pb-2">
            <a href="admin_dashboard.php"
                class="relative overflow-hidden bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl p-4 flex items-center gap-3 shadow-lg shadow-orange-500/30 hover:scale-[1.02] transition">
                <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-user-shield text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-bold text-white">Admin Dashboard</h3>
                    <p class="text-xs text-orange-100">Manage system & view reports</p>
                </div>
                <i class="fas fa-arrow-right text-white"></i>
            </a>
        </section>
    <?php endif; ?>

    
    <!--  LOGOUT -->
    
    <?php if ($is_logged_in): ?>
        <section class="px-4 pt-5 pb-6">
            <a href="logout.php"
                class="logout-btn flex items-center gap-3 bg-white rounded-2xl p-3.5 shadow-sm border border-red-100">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white shadow-lg flex-shrink-0">
                    <i class="fas fa-sign-out-alt text-base"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-red-500">Logout</h3>
                    <p class="text-xs text-gray-500">See you soon!</p>
                </div>
                <i class="fas fa-chevron-right text-red-300 text-xs"></i>
            </a>
        </section>
    <?php endif; ?>

    
    <!--  APP INFO -->
    
    <section class="px-4 pb-6">
        <div class="text-center">
            <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl flex items-center justify-center text-white font-bold shadow-lg shadow-orange-200">
                A
            </div>
            <p class="text-xs font-bold text-orange-500">Alihaha</p>
            <p class="text-xs text-white">Version 1.0.0</p>
            <p class="text-xs text-white mt-2">
                Made with <i class="fas fa-heart text-red-500"></i> in Myanmar
            </p>
        </div>
    </section>

    <footer class="glass border-t border-gray-200 mt-8 pb-20">
        <!--  Links Grid -->
        <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">

                <!-- Column 1 -->
                <div>
                    <h4 class="text-sm font-bold text-gray-800 mb-3">About Alihaha</h4>
                    <ul class="space-y-2 text-xs text-white">
                        <li><a href="#" class="hover:text-orange-500">Why choose Alihaha</a></li>
                        <li><a href="#" class="hover:text-orange-500">Co-Create Pitch</a></li>
                        <li><a href="#" class="hover:text-orange-500">Corporate responsibility</a></li>
                        <li><a href="#" class="hover:text-orange-500">Careers</a></li>
                    </ul>
                </div>

                <!-- Column 2 -->
                <div>
                    <h4 class="text-sm font-bold text-gray-800 mb-3">Order protection</h4>
                    <ul class="space-y-2 text-xs text-white">
                        <li><a href="#" class="hover:text-orange-500">Secure payments</a></li>
                        <li><a href="#" class="hover:text-orange-500">Money-back guarantee</a></li>
                        <li><a href="#" class="hover:text-orange-500">Guaranteed delivery</a></li>
                        <li><a href="#" class="hover:text-orange-500">After-sales protections</a></li>
                        <li><a href="#" class="hover:text-orange-500">Production monitoring</a></li>
                        <li><a href="#" class="hover:text-orange-500">Policies and rules</a></li>
                    </ul>
                </div>

                <!-- Column 3 -->
                <div>
                    <h4 class="text-sm font-bold text-gray-800 mb-3">Source on Alihaha</h4>
                    <ul class="space-y-2 text-xs text-white">
                        <li><a href="#" class="hover:text-orange-500">Verified manufacturers</a></li>
                        <li><a href="#" class="hover:text-orange-500">Request for Quotation</a></li>
                    </ul>
                </div>

                <!-- Column 4 -->
                <div>
                    <h4 class="text-sm font-bold text-gray-800 mb-3">Help Center</h4>
                    <ul class="space-y-2 text-xs text-white">
                        <li><a href="#" class="hover:text-orange-500">Buyer Help Center</a></li>
                        <li><a href="#" class="hover:text-orange-500">Live chat</a></li>
                        <li><a href="#" class="hover:text-orange-500">File a trade dispute</a></li>
                        <li><a href="#" class="hover:text-orange-500">Refunds</a></li>
                        <li><a href="#" class="hover:text-orange-500">Report IP infringement</a></li>
                        <li><a href="#" class="hover:text-orange-500">Report a violation</a></li>
                    </ul>
                </div>

                <!-- Column 5 -->
                <div>
                    <h4 class="text-sm font-bold text-gray-800 mb-3">Sell on Alihaha</h4>
                    <ul class="space-y-2 text-xs text-white">
                        <li><a href="#" class="hover:text-orange-500">Sell on Alihaha</a></li>
                        <li><a href="#" class="hover:text-orange-500">Start selling</a></li>
                        <li><a href="#" class="hover:text-orange-500">Check order status</a></li>
                        <li><a href="#" class="hover:text-orange-500">Become a Verified Supplier</a></li>
                        <li><a href="#" class="hover:text-orange-500">Partnerships</a></li>
                    </ul>

                    <!-- Stay Connected -->
                    <h4 class="text-sm font-bold text-gray-800 mb-2 mt-4">Stay Connected</h4>
                    <div class="flex gap-3 text-white">
                        <a href="#" class="hover:text-orange-500"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-orange-500"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="hover:text-orange-500"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-orange-500"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="hover:text-orange-500"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="hover:text-orange-500"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>

            <!--  Payment + App -->
            <div class="border-t border-gray-100 pt-6 mt-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <!-- Payment -->
                    <!-- Payment Methods -->
                    <div class="flex flex-wrap justify-center items-center gap-3 py-4 border-t border-gray-100">

                        <!-- Visa -->
                        <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition">
                            <i class="fab fa-cc-visa text-blue-700 text-2xl"></i>
                        </a>

                        <!-- Mastercard -->
                        <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition">
                            <i class="fab fa-cc-mastercard text-red-600 text-2xl"></i>
                        </a>

                        <!-- PayPal -->
                        <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition">
                            <i class="fab fa-cc-paypal text-blue-500 text-2xl"></i>
                        </a>

                        <!-- Amex -->
                        <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition">
                            <i class="fab fa-cc-amex text-blue-800 text-2xl"></i>
                        </a>

                        <!-- Apple Pay -->
                        <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition">
                            <i class="fab fa-apple-pay text-black text-2xl"></i>
                        </a>

                        <!-- Google Pay -->
                        <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition">
                            <i class="fab fa-google-pay text-gray-700 text-2xl"></i>
                        </a>

                        <!-- Discover -->
                        <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition">
                            <i class="fab fa-cc-discover text-orange-500 text-2xl"></i>
                        </a>
                    </div>

                    <!-- App -->
                    <!-- App Download -->
                    <div class="flex items-center gap-3 justify-center md:justify-end">

                        <!-- App Store -->
                        <a href="#" class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                            <i class="fab fa-apple text-2xl"></i>
                            <div class="leading-tight">
                                <p class="text-[10px] text-gray-300">Download on the</p>
                                <p class="text-sm font-semibold">App Store</p>
                            </div>
                        </a>

                        <!-- Google Play -->
                        <a href="#" class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                            <i class="fab fa-google-play text-xl text-green-400"></i>
                            <div class="leading-tight">
                                <p class="text-[10px] text-gray-300">GET IT ON</p>
                                <p class="text-sm font-semibold">Google Play</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!--  Subsidiaries -->
            <div class="border-t border-gray-100 pt-4 mt-6 text-center">
                <div class="flex flex-wrap justify-center gap-2 text-xs text-white mb-3">
                    <a href="#" class="hover:text-orange-500">AliExpress</a> |
                    <a href="#" class="hover:text-orange-500">1688.com</a> |
                    <a href="#" class="hover:text-orange-500">Tmall Taobao World</a> |
                    <a href="#" class="hover:text-orange-500">Lazada</a> |
                    <a href="#" class="hover:text-orange-500">Taobao Global</a> |
                    <a href="#" class="hover:text-orange-500">TAO</a> |
                    <a href="#" class="hover:text-orange-500">Trendyol</a> |
                    <a href="#" class="hover:text-orange-500">Europages</a>
                </div>
            </div>

            <!--  Legal -->
            <div class="border-t border-gray-100 pt-4 text-center">
                <div class="flex flex-wrap justify-center gap-2 text-xs text-white mb-3">
                    <a href="#" class="hover:text-orange-500">Legal Notice</a> ·
                    <a href="#" class="hover:text-orange-500">Product Listing Policy</a> ·
                    <a href="#" class="hover:text-orange-500">Intellectual Property Protection</a> ·
                    <a href="#" class="hover:text-orange-500">Privacy Policy</a> ·
                    <a href="#" class="hover:text-orange-500">Your Privacy Choices</a> ·
                    <a href="#" class="hover:text-orange-500">Terms of Use</a> ·
                    <a href="#" class="hover:text-orange-500">Integrity Compliance</a>
                </div>
                <p class="text-xs text-white">
                    © 1999-2026 Alihaha.com. All rights reserved.
                </p>
            </div>
        </div>
    </footer>


    <!-- BOTTOM NAVIGATION -->

    <!--  BOTTOM NAVIGATION -->
    <!-- BOTTOM NAVIGATION -->
    <nav class="fixed bottom-0 left-0 right-0 glass backdrop-blur-md border-t border-gray-200 flex justify-around py-2 z-50">
        <a href="index.php" class="bottom-nav-item flex flex-col items-center text-xs <?php echo ($current_page == 'index.php') ? 'text-orange-500 active' : 'text-black hover:text-orange-500'; ?>">
            <i class="fas fa-home text-xl mb-1"></i><span>Home</span>
        </a>
        <a href="tips.php" class="bottom-nav-item flex flex-col items-center text-xs <?php echo ($current_page == 'tips.php') ? 'text-orange-500 active' : 'text-black hover:text-orange-500'; ?>">
            <i class="fas fa-eye text-xl mb-1"></i><span>Tips</span>
        </a>
        <a href="messenger.php" class="bottom-nav-item flex flex-col items-center text-xs <?php echo ($current_page == 'messenger.php' || $current_page == 'chat.php') ? 'text-orange-500 active' : 'text-black hover:text-orange-500'; ?>">
            <i class="fas fa-comment-dots text-xl mb-1"></i><span>Messenger</span>
        </a>
        <a href="cart.php" class="bottom-nav-item flex flex-col items-center text-xs <?php echo ($current_page == 'cart.php' || $current_page == 'checkout.php') ? 'text-orange-500 active' : 'text-black hover:text-orange-500'; ?>">
            <i class="fas fa-shopping-cart text-xl mb-1"></i><span>Cart</span>
        </a>
        <a href="my-account.php" class="bottom-nav-item flex flex-col items-center text-xs <?php echo ($current_page == 'my-account.php' || $current_page == 'my-order.php' || $current_page == 'order-detail.php') ? 'text-orange-500 active' : 'text-black hover:text-orange-500'; ?>">
            <i class="fas fa-user text-xl mb-1"></i><span>My Alihaha</span>
        </a>
    </nav>

</body>

</html>