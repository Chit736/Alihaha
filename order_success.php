<?php
session_start();
include 'connection.php';



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("location: cart.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$address = htmlspecialchars(trim($_POST['address'] ?? ''));
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
$city = htmlspecialchars(trim($_POST['city'] ?? ''));
$postal = htmlspecialchars(trim($_POST['postal'] ?? ''));
$notes = htmlspecialchars(trim($_POST['notes'] ?? 'notes' ?? ''));
$payment = htmlspecialchars(trim($_POST['payment'] ?? 'cod'));

// Validation 
if (empty($address) || empty($phone)) {
    header("location: checkout.php?error=Please fill in all required fields.");
    exit();
}

// Cart 
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("location: cart.php?error=Your cart is empty.");
    exit();
}

// Total ကို server ဘက်မှာတွက် 
$total = 0;
$total_items = 0;
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $p_sql = "SELECT price FROM products WHERE id = ?";
    $p_stmt = $conn->prepare($p_sql);
    $p_stmt->bind_param("i", $product_id);
    $p_stmt->execute();
    $product = $p_stmt->get_result()->fetch_assoc();
    $p_stmt->close();

    if ($product) {
        $total += $product['price'] * $quantity;
        $total_items += $quantity;
    }
}

$shipping = $total >= 100 ? 0 : 5.00;
$tax = $total * 0.05;
$grand_total = $total + $shipping + $tax;

// Orders Table ထဲ Insertထည့် 
$status = 'pending';
$full_address = $address;
if ($city) $full_address .= ', ' . $city;
if ($postal) $full_address .= ' ' . $postal;

$sql = "INSERT INTO orders(user_id, total, status, address, phone) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("idsss", $user_id, $grand_total, $status, $full_address, $phone);

if (!$stmt->execute()) {
    die("Order failed:" . $stmt->error);
}
$order_id = $conn->insert_id;
$stmt->close();

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $p_sql = "SELECT price FROM products WHERE id = ?";
    $p_stmt = $conn->prepare($p_sql);
    $p_stmt->bind_param("i", $product_id);
    $p_stmt->execute();
    $product = $p_stmt->get_result()->fetch_assoc();
    $p_stmt->close();

    if ($product) {
        $item_sql = "INSERT INTO `order_items` (order_id, product_id, quantity, price) VAlUES (?, ?, ?, ?)";
        $item_stmt = $conn->prepare($item_sql);
        $item_stmt->bind_param("iiid", $order_id, $product_id, $quantity, $product['price']);
        $item_stmt->execute();
        $item_stmt->close();
    }
}

// Cart Clear 
unset($_SESSION['cart']);

// Order Info ဆွဲထုတ် 
$o_sql = "SELECT * FROM orders WHERE id = ?";
$o_stmt = $conn->prepare($o_sql);
$o_stmt->bind_param("i", $order_id);
$o_stmt->execute();
$order = $o_stmt->get_result()->fetch_assoc();
$o_stmt->close();

// Estimated Delivery (3-7 days) 
$order_date = strtotime($order['created_at']);
$est_delivery = date('d M Y', strtotime('+5 days', $order_date));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Order Placed Successfully - Alihaha </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
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
                transform: scale(1.15);
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
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in {
            animation: slide-in 0.6s ease-out;
        }

        @keyframes slide-in-delay {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            50% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in-delay {
            animation: slide-in-delay 1s ease-out;
        }

        @keyframes gradient-shift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .gradient-animate {
            background-size: 200% 200%;
            animation: gradient-shift 4s ease infinite;
        }

        @keyframes scale-bounce {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.15);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .scale-bounce {
            animation: scale-bounce 0.7s ease-out;
        }

        @keyframes check-draw {
            0% {
                stroke-dashoffset: 100;
            }

            100% {
                stroke-dashoffset: 0;
            }
        }

        .check-path {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: check-draw 1s ease-out 0.3s forwards;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }

        .confetti {
            position: fixed;
            top: 0;
            width: 10px;
            height: 10px;
            animation: confetti-fall 3s linear forwards;
            pointer-events: none;
            z-index: 100;
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
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

        /*  Button Shine */
        @keyframes shine {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shine 3s infinite;
        }

        /*  Success Ring */
        .success-ring {
            position: relative;
        }

        .success-ring::before {
            content: '';
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            border: 3px solid;
            border-color: #22c55e transparent #22c55e transparent;
            animation: spin 3s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /*  Info Card Hover */
        .info-card {
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(249, 115, 22, 0.1);
        }
    </style>
</head>

<body class="bg-blue-800 font-sans min-h-screen overflow-x-hidden">

    <!--  CONFETTI (CSS Only) -->

    <div class="confetti" style="left: 10%; background: #f97316; animation-delay: 0s;"></div>
    <div class="confetti" style="left: 20%; background: #22c55e; animation-delay: 0.2s;"></div>
    <div class="confetti" style="left: 30%; background: #8b5cf6; animation-delay: 0.4s;"></div>
    <div class="confetti" style="left: 40%; background: #ec4899; animation-delay: 0.1s;"></div>
    <div class="confetti" style="left: 50%; background: #eab308; animation-delay: 0.5s;"></div>
    <div class="confetti" style="left: 60%; background: #06b6d4; animation-delay: 0.3s;"></div>
    <div class="confetti" style="left: 70%; background: #f97316; animation-delay: 0.6s;"></div>
    <div class="confetti" style="left: 80%; background: #22c55e; animation-delay: 0.15s;"></div>
    <div class="confetti" style="left: 90%; background: #8b5cf6; animation-delay: 0.45s;"></div>

   
    <!--  MAIN CONTAINER -->
  
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-lg w-full">

            
            <!--  SUCCESS HERO CARD -->
           
            <div class="relative overflow-hidden glass rounded-3xl p-8 text-white text-center scale-bounce shadow-2xl shadow-green-200">
                <div class="blob w-40 h-40 bg-yellow-300 top-0 -right-10"></div>
                <div class="blob w-32 h-32 bg-white bottom-0 -left-10"></div>
                <div class="absolute inset-0 shimmer"></div>

                <!-- Success Icon with Ring -->
                <div class="relative inline-block mb-4">
                    <div class="success-ring relative w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-xl">
                        <svg class="w-14 h-14 text-green-500" viewBox="0 0 52 52" fill="none">
                            <path class="check-path" d="M14 27L22 35L38 19" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <!-- Sparkles -->
                    <div class="absolute -top-2 -right-2 text-yellow-300 text-2xl animate-float">✨</div>
                    <div class="absolute -bottom-2 -left-2 text-yellow-300 text-xl animate-float" style="animation-delay: 0.5s;">⭐</div>
                </div>

                <h1 class="text-2xl font-black mb-2">
                    Order Placed! 🎉
                </h1>
                <p class="text-sm text-green-50 mb-4">
                    Thank you for your order. We'll process it shortly.
                </p>

                <!-- Order ID Badge -->
                <div class="inline-flex items-center gap-2 bg-blue-800 backdrop-blur rounded-full px-4 py-2 border border-white/30">
                    <i class="fas fa-receipt"></i>
                    <span class="text-sm font-bold">Order #<?php echo $order['id']; ?></span>
                </div>
            </div>

            
            <!--  ORDER INFO CARDS -->
          
            <div class="mt-4 grid grid-cols-2 gap-3 slide-in-delay">
                <!-- Status -->
                <div class="info-card glass rounded-2xl p-4 shadow-sm border border-gray-100">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg mb-2">
                        <i class="fas fa-clock text-sm"></i>
                    </div>
                    <p class="text-xs text-white mb-0.5">Status</p>
                    <p class="text-sm font-bold text-gray-800">
                        <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full font-bold">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </p>
                </div>

                <!-- Total -->
                <div class="info-card glass rounded-2xl p-4 shadow-sm border border-gray-100">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg mb-2">
                        <i class="fas fa-dollar-sign text-sm"></i>
                    </div>
                    <p class="text-xs text-white mb-0.5">Total Paid</p>
                    <p class="text-base font-black gradient-text-green">
                        $<?php echo number_format($order['total'], 2); ?>
                    </p>
                </div>

                <!-- Date -->
                <div class="info-card glass rounded-2xl p-4 shadow-sm border border-gray-100">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg mb-2">
                        <i class="fas fa-calendar-alt text-sm"></i>
                    </div>
                    <p class="text-xs text-white mb-0.5">Order Date</p>
                    <p class="text-sm font-bold text-gray-800">
                        <?php echo date('d M Y', strtotime($order['created_at'])); ?>
                    </p>
                    <p class="text-xs text-white">
                        <?php echo date('h:i A', strtotime($order['created_at'])); ?>
                    </p>
                </div>

                <!-- Est. Delivery -->
                <div class="info-card glass rounded-2xl p-4 shadow-sm border border-gray-100">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg mb-2">
                        <i class="fas fa-truck text-sm"></i>
                    </div>
                    <p class="text-xs text-white mb-0.5">Est. Delivery</p>
                    <p class="text-sm font-bold text-gray-800">
                        <?php echo $est_delivery; ?>
                    </p>
                    <p class="text-xs text-green-500 font-semibold">3-5 days</p>
                </div>
            </div>

            
            <!--  SHIPPING INFO -->
          
            <div class="mt-4 glass rounded-3xl p-5 shadow-sm border border-gray-100 slide-in-delay">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-map-marker-alt text-xs"></i>
                    </div>
                    <h2 class="text-sm font-bold text-gray-800">Shipping To</h2>
                </div>
                <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-2xl p-3 border border-orange-100">
                    <p class="text-sm font-semibold text-gray-800 mb-1">
                        <?php echo htmlspecialchars($order['address']); ?>
                    </p>
                    <p class="text-xs text-gray-600 flex items-center gap-1">
                        <i class="fas fa-phone text-orange-500"></i>
                        <?php echo htmlspecialchars($order['phone']); ?>
                    </p>
                </div>
            </div>

           
            <!-- ORDER TIMELINE -->
            
            <div class="mt-4 glass rounded-3xl p-5 shadow-sm border border-gray-100 slide-in-delay">
                <h2 class="text-sm font-bold text-gray-800 mb-4">
                    <i class="fas fa-route text-orange-500 mr-1"></i> Order Progress
                </h2>

                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-4 top-3 bottom-3 w-0.5 bg-gradient-to-b from-green-500 via-orange-400 to-gray-200"></div>

                    <!-- Step 1: Confirmed -->
                    <div class="flex gap-3 mb-4 relative">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center text-white shadow-lg flex-shrink-0 relative z-10">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-800">Order Confirmed</p>
                            <p class="text-xs text-white">Just now</p>
                        </div>
                    </div>

                    <!-- Step 2: Processing -->
                    <div class="flex gap-3 mb-4 relative">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white shadow-lg flex-shrink-0 relative z-10 pulse-ring">
                            <i class="fas fa-box text-xs"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-orange-500">Processing</p>
                            <p class="text-xs text-white">Preparing your items</p>
                        </div>
                    </div>

                    <!-- Step 3: Shipped -->
                    <div class="flex gap-3 mb-4 relative">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 shadow-sm flex-shrink-0 relative z-10">
                            <i class="fas fa-truck text-xs"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-semibold text-white">Shipped</p>
                            <p class="text-xs text-white">Coming soon</p>
                        </div>
                    </div>

                    <!-- Step 4: Delivered -->
                    <div class="flex gap-3 relative">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 shadow-sm flex-shrink-0 relative z-10">
                            <i class="fas fa-home text-xs"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-semibold text-white">Delivered</p>
                            <p class="text-xs text-white">Est. <?php echo $est_delivery; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- CTA BUTTONS -->
            
            <div class="mt-4 flex gap-3 slide-in-delay">
                <a href="my_order.php"
                    class="shine-effect flex-1 bg-gradient-to-r from-orange-500 to-red-600 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-orange-300 hover:from-orange-600 hover:to-red-700 transition flex items-center justify-center gap-2">
                    <i class="fas fa-list"></i>
                    View My Orders
                </a>
                <a href="products.php"
                    class="flex-1 glass text-white font-bold py-3.5 rounded-2xl shadow-sm border border-gray-200 hover:border-orange-300 hover:text-orange-500 transition flex items-center justify-center gap-2">
                    <i class="fas fa-shopping-bag"></i>
                    Continue
                </a>
            </div>

           
            <!--  SUPPORT INFO -->
       
            <div class="mt-4 glass rounded-2xl p-4 border border-blue-100 slide-in-delay">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-800">Need help with your order?</p>
                        <p class="text-xs text-white">Contact our 24/7 support team</p>
                    </div>
                    <a href="messenger.php" class="bg-white text-blue-600 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                        Chat →
                    </a>
                </div>
            </div>

          
            <!-- FOOTER -->
          
            <div class="text-center mt-6 pb-6 slide-in-delay">
                <div class="relative inline-block w-12 h-12 mb-2">
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-orange-400 via-orange-500 to-red-500 rotate-6 shadow-lg shadow-orange-300"></div>
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 -rotate-3 flex items-center justify-center">
                        <span class="text-white font-black text-xl drop-shadow-md">A</span>
                    </div>
                </div>
                <p class="text-xs text-white">Thank you for shopping with Alihaha 💛</p>
            </div>

        </div>
    </div>

</body>

</html>