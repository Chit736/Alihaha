<?php
session_start();
include 'connection.php';
include 'function.php';


if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: my_order.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$order_id = intval($_GET['id']);

// Order စစ်ဆေးခြင်း
$sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: my_order.php?error=Order not found.");
    exit();
}


// Order Items ဆွဲထုတ်

$item_sql = "SELECT oi.*, p.title, p.image, p.category 
             FROM `order_items` oi 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?";
$item_stmt = $conn->prepare($item_sql);
$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();
$items_result = $item_stmt->get_result();

$items = [];
$subtotal = 0;
$total_items = 0;
while ($item = $items_result->fetch_assoc()) {
    $items[] = $item;
    $line_total = $item['price'] * $item['quantity'];
    $subtotal += $line_total;
    $total_items += $item['quantity'];
}
$item_stmt->close();


//  Financial Calculations

$shipping = $subtotal >= 100 ? 0 : 5.00;
$tax = $subtotal * 0.05;
$grand_total = $subtotal + $shipping + $tax;

// Status Info

$status = $order['status'];
$status_config = [
    'pending' => [
        'label' => 'Pending', 'icon' => 'fa-clock',
        'gradient' => 'from-yellow-400 to-orange-500',
        'text' => 'text-yellow-700', 'bg' => 'bg-yellow-100',
        'desc' => 'Waiting for confirmation', 'step' => 1
    ],
    'processing' => [
        'label' => 'Processing', 'icon' => 'fa-box',
        'gradient' => 'from-blue-400 to-indigo-600',
        'text' => 'text-blue-700', 'bg' => 'bg-blue-100',
        'desc' => 'Preparing your items', 'step' => 2
    ],
    'shipped' => [
        'label' => 'Shipped', 'icon' => 'fa-truck',
        'gradient' => 'from-purple-400 to-pink-600',
        'text' => 'text-purple-700', 'bg' => 'bg-purple-100',
        'desc' => 'On the way to you', 'step' => 3
    ],
    'completed' => [
        'label' => 'Completed', 'icon' => 'fa-check-circle',
        'gradient' => 'from-green-400 to-emerald-600',
        'text' => 'text-green-700', 'bg' => 'bg-green-100',
        'desc' => 'Delivered successfully', 'step' => 4
    ],
    'cancelled' => [
        'label' => 'Cancelled', 'icon' => 'fa-times-circle',
        'gradient' => 'from-red-400 to-pink-600',
        'text' => 'text-red-700', 'bg' => 'bg-red-100',
        'desc' => 'Order cancelled', 'step' => 0
    ],
];
$si = $status_config[$status] ?? $status_config['pending'];
$current_step = $si['step'];


// Estimated Delivery

$order_date = strtotime($order['created_at']);
$est_delivery = date('d M Y', strtotime('+5 days', $order_date));
$days_remaining = max(0, ceil((strtotime('+5 days', $order_date) - time()) / 86400));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $order['id']; ?> - Alihaha</title>
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

        @keyframes spin-slow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .spin-slow { animation: spin-slow 3s linear infinite; }

        /*  Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        /*  Card Hover */
        .info-card {
            transition: all 0.3s ease;
        }
        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(249, 115, 22, 0.1);
        }

        .item-row {
            transition: all 0.3s ease;
        }
        .item-row:hover {
            transform: translateX(4px);
            background: linear-gradient(90deg, #fff7ed 0%, #ffffff 100%);
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

        /*  Timeline */
        .timeline-line {
            background: linear-gradient(180deg, #22c55e 0%, #22c55e var(--progress, 0%), #e5e7eb var(--progress, 0%), #e5e7eb 100%);
        }

        /* Button Shine */
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

        /*  Print */
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-break { page-break-inside: avoid; }
        }

        /*  Step Indicator */
        .step-circle {
            transition: all 0.3s ease;
        }
        .step-completed {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }
        .step-active {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
            animation: pulse-ring 2s ease-in-out infinite;
        }
        .step-inactive {
            background: #f3f4f6;
            color: #9ca3af;
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
                <a href="index.php" class="flex-1 text-center py-2.5 text-sm font-semibold text-black border-b-2">Home</a>
                <a href="products.php" class="flex-1 text-center py-2.5 text-sm text-black hover:text-orange-500 border-orange-500">Products</a>
                <a href="manufacturers.php" class="flex-1 text-center py-2.5 text-sm text-black hover:text-orange-500">Manufacturers</a>
                <a href="worldwide.php" class="flex-1 text-center py-2.5 text-sm text-black hover:text-orange-500">Worldwide</a>
            </div>
        </nav>
    </header>

    <!--  ORDER HERO -->

    <section class="relative overflow-hidden glass <?php echo $si['gradient']; ?> text-white max-w-6xl mx-auto px-4 mt-4 rounded-3xl">
        <div class="blob w-32 h-32 bg-white top-0 -right-10"></div>
        <div class="blob w-24 h-24 bg-yellow-300 bottom-0 -left-10"></div>
        <div class="absolute inset-0 shimmer"></div>

        <div class="relative p-5">
            <!-- Top Row -->
            <div class="flex items-center justify-between mb-4">
                <div class="inline-flex items-center gap-2 bg-orange-500 rounded-full px-3 py-1.5">
                    <i class="fas <?php echo $si['icon']; ?> text-xs"></i>
                    <span class="text-xs font-bold"><?php echo strtoupper($si['label']); ?></span>
                </div>
                <div class="text-right">
                    <p class="text-xs opacity-80">Order ID</p>
                    <p class="text-lg font-black">#<?php echo $order['id']; ?></p>
                </div>
            </div>

            <!-- Status Icon -->
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-orange-500 backdrop-blur rounded-2xl flex items-center justify-center flex-shrink-0 animate-float">
                    <i class="fas <?php echo $si['icon']; ?> text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black mb-0.5"><?php echo $si['label']; ?></h2>
                    <p class="text-sm opacity-90"><?php echo $si['desc']; ?></p>
                </div>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-3 gap-2">
                <div class="bg-blue-800 rounded-xl p-2.5 text-center">
                    <p class="text-xs opacity-80 mb-0.5">Items</p>
                    <p class="text-base font-bold"><?php echo $total_items; ?></p>
                </div>
                <div class="bg-blue-800 rounded-xl p-2.5 text-center">
                    <p class="text-xs opacity-80 mb-0.5">Total</p>
                    <p class="text-base font-bold">$<?php echo number_format($order['total'], 2); ?></p>
                </div>
                <div class="bg-blue-800 rounded-xl p-2.5 text-center">
                    <p class="text-xs opacity-80 mb-0.5">Delivery</p>
                    <p class="text-xs font-bold">
                        <?php if ($status === 'completed'): ?>
                            Delivered
                        <?php elseif ($days_remaining > 0): ?>
                            <?php echo $days_remaining; ?>d left
                        <?php else: ?>
                            Today
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Wave Bottom -->
        <svg class="absolute bottom-0 left-0 right-0" viewBox="0 0 1440 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 40L60 36C120 32 240 24 360 22C480 20 600 24 720 26C840 28 960 28 1080 26C1200 24 1320 20 1380 18L1440 16V40H0Z" fill="#15de15"/>
        </svg>
    </section>

   
    <!--  ORDER TIMELINE -->
   
    <?php if ($status !== 'cancelled'): ?>
    <section class=" max-w-6xl mx-auto px-4 mt-4 px-4 pt-5">
        <div class="glass rounded-3xl p-5 shadow-sm border border-gray-100 slide-in">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-sm font-bold text-gray-800">
                        <i class="fas fa-route text-orange-500 mr-1"></i> Order Progress
                    </h2>
                    <p class="text-xs text-white mt-0.5">Track your order status</p>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-shipping-fast text-sm"></i>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-white">Progress</span>
                    <span class="text-xs font-bold text-orange-500"><?php echo ($current_step / 4) * 100; ?>%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full bg-gradient-to-r <?php echo $si['gradient']; ?>" 
                         style="width: <?php echo ($current_step / 4) * 100; ?>%"></div>
                </div>
            </div>

            <!-- Timeline Steps -->
            <div class="space-y-4">
                <?php
                $steps = [
                    ['label' => 'Order Placed', 'desc' => 'Order received', 'icon' => 'fa-shopping-bag', 'step' => 1],
                    ['label' => 'Processing', 'desc' => 'Preparing items', 'icon' => 'fa-box', 'step' => 2],
                    ['label' => 'Shipped', 'desc' => 'On the way', 'icon' => 'fa-truck', 'step' => 3],
                    ['label' => 'Delivered', 'desc' => 'Estimated ' . $est_delivery, 'icon' => 'fa-home', 'step' => 4],
                ];

                foreach ($steps as $step):
                    $is_completed = $current_step >= $step['step'];
                    $is_active = $current_step == $step['step'];
                    $circle_class = $is_completed ? 'step-completed' : ($is_active ? 'step-active' : 'step-inactive');
                ?>
                    <div class="flex gap-3 relative">
                        <div class="flex flex-col items-center">
                            <div class="step-circle w-10 h-10 rounded-full <?php echo $circle_class; ?> flex items-center justify-center flex-shrink-0">
                                <?php if ($is_completed): ?>
                                    <i class="fas fa-check text-sm"></i>
                                <?php else: ?>
                                    <i class="fas <?php echo $step['icon']; ?> text-sm"></i>
                                <?php endif; ?>
                            </div>
                            <?php if ($step['step'] < 4): ?>
                                <div class="w-0.5 h-8 mt-1 <?php echo $current_step > $step['step'] ? 'bg-green-500' : 'bg-gray-200'; ?>"></div>
                            <?php endif; ?>
                        </div>
                        <div class="pt-1.5 flex-1">
                            <p class="text-sm font-bold <?php echo $is_completed ? 'text-gray-800' : 'text-gray-400'; ?>">
                                <?php echo $step['label']; ?>
                            </p>
                            <p class="text-xs <?php echo $is_completed ? 'text-white' : 'text-white-100'; ?>">
                                <?php echo $step['desc']; ?>
                            </p>
                        </div>
                        <?php if ($is_active): ?>
                            <span class="text-xs bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full font-bold self-start mt-1.5">
                                Current
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php else: ?>
    <section class="max-w-6xl mx-auto px-4 mt-4 px-4 pt-5">
        <div class="bg-gradient-to-br from-red-50 to-pink-50 border border-red-200 rounded-3xl p-5 slide-in">
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 bg-gradient-to-br from-red-400 to-pink-600 rounded-2xl flex items-center justify-center text-white shadow-lg flex-shrink-0">
                    <i class="fas fa-times-circle text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-red-700">Order Cancelled</h3>
                    <p class="text-xs text-red-500">This order has been cancelled</p>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    
    <!--  QUICK INFO CARDS -->
    
    <section class=" max-w-6xl mx-auto px-4 mt-4 px-4 pt-5">
        <div class="grid grid-cols-2 gap-3">
            <!-- Order Date -->
            <div class="info-card glass rounded-2xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center text-white shadow-lg mb-2">
                    <i class="fas fa-calendar-alt text-sm"></i>
                </div>
                <p class="text-xs text-white mb-0.5">Order Date</p>
                <p class="text-sm font-bold text-gray-800">
                    <?php echo date('d M Y', $order_date); ?>
                </p>
                <p class="text-xs text-white">
                    <?php echo date('h:i A', $order_date); ?>
                </p>
            </div>

            <!-- Est. Delivery -->
            <div class="info-card glass rounded-2xl p-4 shadow-sm border border-gray-100">
                <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg mb-2">
                    <i class="fas fa-truck text-sm"></i>
                </div>
                <p class="text-xs text-white mb-0.5">Est. Delivery</p>
                <p class="text-sm font-bold text-gray-800"><?php echo $est_delivery; ?></p>
                <?php if ($status !== 'completed' && $status !== 'cancelled'): ?>
                    <p class="text-xs text-orange-500 font-semibold">
                        <?php echo $days_remaining; ?> days left
                    </p>
                <?php else: ?>
                    <p class="text-xs text-green-500 font-semibold">Delivered ✓</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    
    <!--  SHIPPING ADDRESS -->
 
    <section class=" max-w-6xl mx-auto px-4 mt-4 px-4 pt-4">
        <div class="info-card glass rounded-3xl p-5 shadow-sm border border-gray-100 slide-in">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-map-marker-alt text-sm"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-800">Shipping Address</h2>
            </div>

            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-2xl p-4 border border-orange-100">
                <p class="text-sm font-semibold text-gray-800 mb-2 flex items-start gap-2">
                    <i class="fas fa-home text-orange-500 mt-0.5"></i>
                    <?php echo htmlspecialchars($order['address']); ?>
                </p>
                <p class="text-xs text-gray-600 flex items-center gap-2">
                    <i class="fas fa-phone text-orange-500"></i>
                    <?php echo htmlspecialchars($order['phone']); ?>
                </p>
            </div>
        </div>
    </section>

    
    <!--  ORDER ITEMS -->
    
    <section class=" max-w-6xl mx-auto px-4 mt-4 px-4 pt-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold text-gray-800">
                <i class="fas fa-shopping-bag text-orange-500 mr-1"></i> Order Items
            </h2>
            <span class="text-xs bg-orange-100 text-orange-600 px-2 py-1 rounded-full font-bold">
                <?php echo $total_items; ?> items
            </span>
        </div>

        <div class="glass rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="divide-y divide-gray-100">
                <?php foreach ($items as $item): 
                    $line_total = $item['price'] * $item['quantity'];
                ?>
                    <div class="item-row p-3.5 flex gap-3">
                        <!-- Image -->
                        <div class="relative flex-shrink-0">
                            <?php if ($item['image']): ?>
                                <img src="image/<?php echo htmlspecialchars($item['image']); ?>" 
                                     class="w-40 h-30 object-cover rounded-xl">
                            <?php else: ?>
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center text-gray-400">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            <!-- Qty Badge -->
                            <span class="absolute -top-1.5 -right-1.5 bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold shadow-lg">
                                <?php echo $item['quantity']; ?>
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-gray-800 truncate mb-1">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </h3>
                            <span class="inline-block text-xs bg-orange-50 text-orange-600 px-2 py-0.5 rounded-full font-medium mb-1">
                                <?php echo htmlspecialchars($item['category']); ?>
                            </span>
                            <div class="flex items-center gap-3">
                                <p class="text-xs text-white">
                                    $<?php echo number_format($item['price'], 2); ?> × <?php echo $item['quantity']; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Line Total -->
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-white mb-0.5">Total</p>
                            <p class="text-sm font-bold gradient-text">
                                $<?php echo number_format($line_total, 2); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!--  PAYMENT SUMMARY -->
    <section class=" max-w-6xl mx-auto px-4 mt-4 px-4 pt-4">
        <div class="bg-orange-500 rounded-3xl p-5 shadow-sm border border-gray-100 slide-in">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-9 h-9 bg-green-500 rounded-xl flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-receipt text-sm"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-800">Payment Summary</h2>
            </div>

            <!-- Subtotal -->
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-black flex items-center gap-1">
                    <i class="fas fa-shopping-cart text-blue-800 text-xs"></i>
                    Subtotal (<?php echo $total_items; ?> items)
                </span>
                <span class="text-sm font-semibold text-gray-800">$<?php echo number_format($subtotal, 2); ?></span>
            </div>

            <!-- Shipping -->
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-black flex items-center gap-1">
                    <i class="fas fa-truck text-blue-800 text-xs"></i>
                    Shipping
                </span>
                <?php if ($shipping == 0): ?>
                    <span class="text-sm font-semibold text-green-800 flex items-center gap-1">
                        <i class="fas fa-check-circle"></i> FREE
                    </span>
                <?php else: ?>
                    <span class="text-sm font-semibold text-gray-800">$<?php echo number_format($shipping, 2); ?></span>
                <?php endif; ?>
            </div>

            <!-- Tax -->
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-black flex items-center gap-1">
                    <i class="fas fa-percent text-blue-800 text-xs"></i>
                    Tax (5%)
                </span>
                <span class="text-sm font-semibold text-gray-800">$<?php echo number_format($tax, 2); ?></span>
            </div>

            <!-- Grand Total -->
            <div class="flex justify-between items-center pt-3 mt-1">
                <div>
                    <span class="text-base font-bold text-gray-800">Grand Total</span>
                    <p class="text-xs text-black">Including all taxes</p>
                </div>
                <span class="text-2xl font-black gradient-text-green-800">
                    $<?php echo number_format($order['total'], 2); ?>
                </span>
            </div>
        </div>
    </section>

   
    <!--  SUPPORT CTA -->
   
    <section class="max-w-6xl mx-auto px-4 mt-4 pt-4 pb-6 no-print">
        <div class="relative overflow-hidden glass rounded-3xl p-5 text-white gradient-animate">
            <div class="blob w-32 h-32 bg-yellow-300 top-0 -right-10"></div>
            <div class="blob w-24 h-24 bg-purple-400 bottom-0 -left-10"></div>

            <div class="relative flex items-center gap-4">
                <div class="w-14 h-14 bg-white backdrop-blur rounded-2xl flex items-center justify-center flex-shrink-0 animate-float">
                    <i class="fas fa-headset text-green-500 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-sm mb-1">Need Help?</h3>
                    <p class="text-xs text-orange-100 mb-2">Contact us about this order</p>
                    <a href="messenger.php" class="inline-block bg-white text-green-500 text-xs font-bold px-4 py-1.5 rounded-full shadow-lg">
                        <i class="fas fa-comment mr-1"></i> Chat Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    
    <!--  ACTION BUTTONS -->
    
    <section class=" max-w-6xl mx-auto px-4 mt-4 pb-6 no-print">
        <div class="flex gap-3">
            <a href="my-order.php" 
               class="flex-1 bg-white text-gray-700 font-bold py-3.5 rounded-2xl shadow-sm border border-gray-200 hover:border-orange-300 hover:text-orange-500 transition flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
            <a href="products.php" 
               class="shine-effect flex-1 bg-gradient-to-r from-orange-500 to-red-600 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-orange-300 hover:from-orange-600 hover:to-red-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-shopping-bag"></i> Shop More
            </a>
        </div>
    </section>

   
    <!--  FOOTER -->
    
    <div class="text-center pb-6 no-print">
        <div class="relative inline-block w-12 h-12 mb-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-orange-400 via-orange-500 to-red-500 rotate-6 shadow-lg shadow-orange-300"></div>
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 -rotate-3 flex items-center justify-center">
                <span class="text-white font-black text-xl drop-shadow-md">A</span>
            </div>
        </div>
        <p class="text-xs text-white">Thank you for shopping with Alihaha 💛</p>
    </div>

</body>
</html>