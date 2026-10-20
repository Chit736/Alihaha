<?php
session_start();
include 'connection.php';
include 'function.php';

//  Login 

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php?error=" . urlencode("Please login to view your orders."));
    exit();
}

$user_id = intval($_SESSION['user_id']);

//  Filter

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

//  Stats 

$stats = ['all' => 0, 'pending' => 0, 'completed' => 0, 'cancelled' => 0, 'total_spent' => 0];
$stats_sql = "SELECT status, COUNT(*) as count, SUM(total) as amount 
              FROM orders WHERE user_id = ? GROUP BY status";
$stmt = $conn->prepare($stats_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats_result = $stmt->get_result();
while ($row = $stats_result->fetch_assoc()) {
    $stats[$row['status']] = $row['count'];
    $stats['all'] += $row['count'];
    $stats['total_spent'] += $row['amount'];
}
$stmt->close();

// Orders Query

$sql = "SELECT * FROM orders WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if ($filter !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $filter;
    $types .= "s";
}
$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();

// Status Config

$status_config = [
    'pending' => ['label' => 'Pending', 'icon' => 'fa-clock', 'gradient' => 'from-yellow-400 to-orange-500', 'text' => 'text-yellow-700', 'bg' => 'bg-yellow-100'],
    'processing' => ['label' => 'Processing', 'icon' => 'fa-box', 'gradient' => 'from-blue-400 to-indigo-600', 'text' => 'text-blue-700', 'bg' => 'bg-blue-100'],
    'shipped' => ['label' => 'Shipped', 'icon' => 'fa-truck', 'gradient' => 'from-purple-400 to-pink-600', 'text' => 'text-purple-700', 'bg' => 'bg-purple-100'],
    'completed' => ['label' => 'Completed', 'icon' => 'fa-check-circle', 'gradient' => 'from-green-400 to-emerald-600', 'text' => 'text-green-700', 'bg' => 'bg-green-100'],
    'cancelled' => ['label' => 'Cancelled', 'icon' => 'fa-times-circle', 'gradient' => 'from-red-400 to-pink-600', 'text' => 'text-red-700', 'bg' => 'bg-red-100'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Alihaha</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }

        /*  Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.6; }
            100% { transform: scale(0.95); opacity: 1; }
        }
        .pulse-ring { animation: pulse-ring 2s ease-in-out infinite; }

        @keyframes slide-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .slide-in { animation: slide-in 0.4s ease-out; }

        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }

        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .gradient-animate {
            background-size: 200% 200%;
            animation: gradient-shift 4s ease infinite;
        }

        /*  Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        /*  Card Hover */
        .order-card {
            transition: all 0.3s ease;
        }
        .order-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(249, 115, 22, 0.12);
            border-color: #fdba74;
        }

        /*  Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #f97316, #fb923c, #fdba74);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .gradient-text-green {
            background: linear-gradient(135deg, #22c55e, #16a34a, #15803d);
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

        /*  Status Badge */
        .status-badge {
            animation: pulse-ring 3s ease-in-out infinite;
        }

        /*  Button Shine */
        @keyframes shine {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        .shine-effect {
            position: relative;
            overflow: hidden;
        }
        .shine-effect::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shine 3s infinite;
        }

        /*  Filter Pill */
        .filter-pill {
            transition: all 0.3s ease;
        }
        .filter-pill:hover {
            transform: translateY(-2px);
        }

        /*  Table Row Hover */
        .order-row {
            transition: all 0.2s ease;
        }
        .order-row:hover {
            background: linear-gradient(90deg, #fff7ed 0%, #ffffff 100%);
        }
    </style>
</head>
<body class="bg-blue-700 font-sans pb-16">


    <!--  HEADER -->

    <header class="glass backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-gray-100">
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3 flex-1">
                <div class="flex items-center justify-center text-black font-bold text-lg">
                    Alihaha
                </div>
                <div class="flex-1 bg-blue-800 rounded-full px-3 py-2 flex items-center gap-2">
                    <i class="fas fa-search text-orange-500 text-sm"></i>
                    <span class="text-sm text-orange-500">Search products...</span>
                </div>
            </div>
            <div class="flex items-center gap-3 ml-3">
                <i class="fas fa-camera text-black"></i>
                <a href="messenger.php" class="relative">
                    <i class="fas fa-bell text-black"></i>
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full pulse-ring"></span>
                </a>
            </div>
        </div>

        <nav class="border-t border-gray-100">
            <div class="flex justify-around px-2">
                <a href="index.php" class="flex-1 text-center py-2.5 text-sm font-semibold text-black border-b-2 border-orange-500">Home</a>
                <a href="products.php" class="flex-1 text-center py-2.5 text-sm text-black hover:text-orange-500">Products</a>
                <a href="manufacturers.php" class="flex-1 text-center py-2.5 text-sm text-black hover:text-orange-500">Manufacturers</a>
                <a href="worldwide.php" class="flex-1 text-center py-2.5 text-sm text-black hover:text-orange-500">Worldwide</a>
            </div>
        </nav>
    </header>

    <!--  HERO STATS -->
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-orange-900 text-white mx-4 mt-4 rounded-3xl">
        <div class="blob w-32 h-32 bg-orange-500 top-0 -right-10"></div>
        <div class="blob w-24 h-24 bg-green-500 bottom-0 -left-10"></div>

        <div class="relative p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold">Order History</h2>
                    <p class="text-xs text-gray-300 mt-0.5">Track your purchases</p>
                </div>
                <div class="w-12 h-12 glass rounded-2xl flex items-center justify-center animate-float">
                    <i class="fas fa-receipt text-orange-400 text-xl"></i>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-3 gap-2">
                <div class="glass rounded-xl p-2.5 text-center">
                    <p class="text-orange-400 font-bold text-lg"><?php echo $stats['all']; ?></p>
                    <p class="text-xs text-gray-300">Total</p>
                </div>
                <div class="glass rounded-xl p-2.5 text-center">
                    <p class="text-yellow-400 font-bold text-lg"><?php echo $stats['pending']; ?></p>
                    <p class="text-xs text-gray-300">Pending</p>
                </div>
                <div class="glass rounded-xl p-2.5 text-center">
                    <p class="text-green-400 font-bold text-lg"><?php echo $stats['completed']; ?></p>
                    <p class="text-xs text-gray-300">Completed</p>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="mt-3 glass rounded-xl p-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-wallet text-orange-400"></i>
                    <span class="text-xs text-gray-300">Total Spent</span>
                </div>
                <span class="text-base font-bold gradient-text">
                    $<?php echo number_format($stats['total_spent'], 2); ?>
                </span>
            </div>
        </div>

        <!-- Wave Bottom -->
        <svg class="absolute bottom-0 left-0 right-0" viewBox="0 0 1440 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 40L60 36C120 32 240 24 360 22C480 20 600 24 720 26C840 28 960 28 1080 26C1200 24 1320 20 1380 18L1440 16V40H0Z" fill="#41b91f"/>
        </svg>
    </section>

    <!--  FILTER PILLS -->
    
    <section class="px-4 pt-5">
        <div class="flex gap-2 overflow-x-auto hide-scroll pb-2">
            <?php
            $filters = [
                'all' => ['label' => 'All', 'icon' => 'fa-th-large', 'count' => $stats['all']],
                'pending' => ['label' => 'Pending', 'icon' => 'fa-clock', 'count' => $stats['pending']],
                'completed' => ['label' => 'Completed', 'icon' => 'fa-check-circle', 'count' => $stats['completed']],
                'cancelled' => ['label' => 'Cancelled', 'icon' => 'fa-times-circle', 'count' => $stats['cancelled']],
            ];
            foreach ($filters as $key => $f):
                $is_active = ($filter === $key);
                $active_class = $is_active 
                    ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-200' 
                    : 'bg-white text-gray-700 border border-gray-200';
            ?>
                <a href="my-order.php?filter=<?php echo urlencode($key); ?>" 
                   class="filter-pill <?php echo $active_class; ?> text-xs px-4 py-2 rounded-full whitespace-nowrap font-semibold flex items-center gap-2">
                    <i class="fas <?php echo $f['icon']; ?> text-xs"></i>
                    <?php echo $f['label']; ?>
                    <?php if ($f['count'] > 0): ?>
                        <span class="<?php echo $is_active ? 'bg-white/30' : 'bg-orange-100 text-orange-600'; ?> text-xs px-1.5 rounded-full">
                            <?php echo $f['count']; ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!--  ORDERS LIST -->
    <section class="px-4 pt-3 pb-6">
        <?php if (count($orders) > 0): ?>
            <div class="space-y-3 max-w-5xl mx-auto px-4 mt-4">
                <?php foreach ($orders as $order): 
                    $si = $status_config[$order['status']] ?? $status_config['pending'];
                    $order_date = strtotime($order['created_at']);
                    $is_recent = (time() - $order_date) < 86400; // 24h
                ?>
                    <div class="order-card slide-in glass rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Top Gradient Bar -->
                        <div class="h-1.5 bg-gradient-to-r <?php echo $si['gradient']; ?>"></div>

                        <div class="p-4">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <!-- Icon -->
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?php echo $si['gradient']; ?> flex items-center justify-center text-white shadow-lg flex-shrink-0">
                                        <i class="fas <?php echo $si['icon']; ?>"></i>
                                    </div>

                                    <div>
                                        <div class="flex items-center gap-1.5 mb-0.5">
                                            <h3 class="text-sm font-bold text-gray-800">Order #<?php echo $order['id']; ?></h3>
                                            <?php if ($is_recent): ?>
                                                <span class="bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full font-bold pulse-ring">NEW</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-xs text-white flex items-center gap-1">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('d M Y, h:i A', $order_date); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <span class="<?php echo $si['bg'] . ' ' . $si['text']; ?> text-xs px-2.5 py-1 rounded-full font-bold flex items-center gap-1">
                                    <i class="fas <?php echo $si['icon']; ?> text-xs"></i>
                                    <?php echo $si['label']; ?>
                                </span>
                            </div>

                            <!-- Info Grid -->
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <!-- Total -->
                                <div class="bg-blue-800 rounded-2xl p-3 border border-orange-100">
                                    <p class="text-xs text-white mb-0.5 flex items-center gap-1">
                                        <i class="fas fa-dollar-sign text-orange-500"></i> Total
                                    </p>
                                    <p class="text-base font-black gradient-text">
                                        $<?php echo number_format($order['total'], 2); ?>
                                    </p>
                                </div>

                                <!-- Address -->
                                <div class="bg-gray-50 rounded-2xl p-3 border border-gray-100">
                                    <p class="text-xs text-gray-500 mb-0.5 flex items-center gap-1">
                                        <i class="fas fa-map-marker-alt text-blue-500"></i> Ship to
                                    </p>
                                    <p class="text-xs font-semibold text-gray-700 truncate">
                                        <?php echo htmlspecialchars(substr($order['address'], 0, 30)); ?>
                                        <?php if (strlen($order['address']) > 30) echo '...'; ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <?php 
                            $progress = 25;
                            if ($order['status'] === 'processing') $progress = 50;
                            elseif ($order['status'] === 'shipped') $progress = 75;
                            elseif ($order['status'] === 'completed') $progress = 100;
                            elseif ($order['status'] === 'cancelled') $progress = 0;
                            ?>
                            <?php if ($order['status'] !== 'cancelled'): ?>
                                <div class="mb-3">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-xs text-white">Progress</span>
                                        <span class="text-xs font-bold text-orange-500"><?php echo $progress; ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full bg-gradient-to-r <?php echo $si['gradient']; ?>" 
                                             style="width: <?php echo $progress; ?>%"></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="order_detail.php?id=<?php echo $order['id']; ?>" 
                                   class="shine-effect flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-xs font-bold py-2.5 rounded-2xl shadow-lg shadow-orange-200 hover:from-orange-600 hover:to-orange-700 transition flex items-center justify-center gap-1.5">
                                    <i class="fas fa-eye"></i> View Details
                                </a>

                                <?php if ($order['status'] === 'completed'): ?>
                                    <a href="products.php" 
                                       class="flex-1 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-600 text-xs font-bold py-2.5 rounded-2xl hover:from-green-100 hover:to-emerald-100 transition flex items-center justify-center gap-1.5">
                                        <i class="fas fa-redo"></i> Buy Again
                                    </a>
                                <?php elseif ($order['status'] === 'pending'): ?>
                                    <a href="messenger.php" 
                                       class="flex-1 bg-green-500 border border-green-500 text-blue-600 text-xs font-bold py-2.5 rounded-2xl hover:from-blue-100 hover:to-indigo-100 transition flex items-center justify-center gap-1.5">
                                        <i class="fas fa-headset"></i> Support
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>

            <!--  EMPTY STATE -->
    
            <div class="text-center py-16 bg-white rounded-3xl shadow-sm">
                <div class="w-28 h-28 mx-auto mb-5 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center animate-float">
                    <i class="fas fa-box-open text-5xl text-orange-500"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-700 mb-2">
                    <?php 
                    if ($filter === 'all') echo 'No Orders Yet';
                    elseif ($filter === 'pending') echo 'No Pending Orders';
                    elseif ($filter === 'completed') echo 'No Completed Orders';
                    else echo 'No Cancelled Orders';
                    ?>
                </h2>
                <p class="text-sm text-gray-500 mb-6 max-w-xs mx-auto">
                    <?php if ($filter === 'all'): ?>
                        You haven't placed any orders yet. Start shopping to fill your order history!
                    <?php else: ?>
                        No orders in this category. Try a different filter.
                    <?php endif; ?>
                </p>

                <?php if ($filter !== 'all'): ?>
                    <a href="my-order.php" class="inline-block bg-gray-100 text-gray-700 font-semibold px-6 py-2.5 rounded-full mb-3">
                        <i class="fas fa-redo mr-2"></i> Show All Orders
                    </a>
                <?php endif; ?>

                <a href="products.php" 
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold px-8 py-3 rounded-2xl shadow-lg shadow-orange-300 hover:from-orange-600 hover:to-orange-700 transition">
                    <i class="fas fa-shopping-bag"></i> Start Shopping
                </a>
            </div>

            <!-- Suggestions -->
            <div class="mt-6">
                <h3 class="text-sm font-bold text-gray-700 mb-3 text-center">Popular Categories</h3>
                <div class="flex flex-wrap gap-2 justify-center">
                    <a href="products.php?category=General" class="bg-white border border-gray-200 text-gray-700 text-xs px-4 py-2 rounded-full hover:border-orange-300 hover:text-orange-500 transition">
                        <i class="fas fa-box mr-1"></i> General
                    </a>
                    <a href="products.php?category=Industrial%20Machinery" class="bg-white border border-gray-200 text-gray-700 text-xs px-4 py-2 rounded-full hover:border-orange-300 hover:text-orange-500 transition">
                        <i class="fas fa-industry mr-1"></i> Machinery
                    </a>
                    <a href="products.php?category=Chemicals" class="bg-white border border-gray-200 text-gray-700 text-xs px-4 py-2 rounded-full hover:border-orange-300 hover:text-orange-500 transition">
                        <i class="fas fa-flask mr-1"></i> Chemicals
                    </a>
                    <a href="products.php?category=Testing%20Instrument" class="bg-white border border-gray-200 text-gray-700 text-xs px-4 py-2 rounded-full hover:border-orange-300 hover:text-orange-500 transition">
                        <i class="fas fa-microscope mr-1"></i> Instruments
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!--  HELP CTA -->
   
    <?php if (count($orders) > 0): ?>
        <section class="px-4 pb-6">
            <div class=" max-w-5xl mx-auto px-4 mt-4relative overflow-hidden glass rounded-3xl p-5 text-white gradient-animate">
                <div class="blob w-32 h-32 bg-yellow-300 top-0 -right-10"></div>
                <div class="blob w-24 h-24 bg-purple-400 bottom-0 -left-10"></div>

                <div class="relative flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center flex-shrink-0 animate-float">
                        <i class="fas fa-headset text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-sm mb-1">Need Help?</h3>
                        <p class="text-xs text-orange-100 mb-2">Our support team is online 24/7</p>
                        <a href="messenger.php" class="inline-block bg-white text-orange-600 text-xs font-bold px-4 py-1.5 rounded-full shadow-lg">
                            <i class="fas fa-comment mr-1"></i> Chat with Us
                        </a>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

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

    <nav class="fixed bottom-0 left-0 right-0 glass backdrop-blur-md border-t border-gray-200 flex justify-around py-2 z-50">
        <a href="index.php" class="bottom-nav-item active flex flex-col items-center text-black text-xs">
            <i class="fas fa-home text-xl mb-1"></i><span>Home</span>
        </a>
        <a href="products.php" class="bottom-nav-item flex flex-col items-center text-black text-xs">
            <i class="fas fa-box text-xl mb-1"></i><span>Products</span>
        </a>
        <a href="messenger.php" class="bottom-nav-item flex flex-col items-center text-black text-xs">
            <i class="fas fa-comment-dots text-xl mb-1"></i><span>Messenger</span>
        </a>
        <a href="cart.php" class="bottom-nav-item flex flex-col items-center text-black text-xs">
            <i class="fas fa-shopping-cart text-xl mb-1"></i><span>Cart</span>
        </a>
        <a href="my-account.php" class="bottom-nav-item flex flex-col items-center text-black text-xs">
            <i class="fas fa-user text-xl mb-1"></i><span>My Alihaha</span>
        </a>
    </nav>

</body>

</html>