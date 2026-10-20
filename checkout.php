<?php
session_start();
include 'connection.php';
include 'function.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("location: login.php?error=" . urlencode("Please login to checkout."));
    exit();
}

$user_id = intval($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'User';

$session_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
if (empty($session_cart)) {
    header("location: cart.php?error=Your cart is empty.");
    exit();
}

$cart_items = array();
$total = 0;
$total_items = 0;
$total_savings = 0;

foreach ($session_cart as $product_id => $quantity) {
    $product_id = intval($product_id);
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($product) {
        $line_total = $product['price'] * $quantity;
        $original_price = $product['price'] * 1.3;
        $savings = ($original_price - $product['price']) * $quantity;

        $cart_items[] = array(
            'product_id' => $product_id,
            'title' => $product['title'],
            'price' => $product['price'],
            'original_price' => $original_price,
            'image' => $product['image'],
            'category' => $product['category'] ?? 'General',
            'quantity' => $quantity,
            'line_total' => $line_total
        );
        $total += $line_total;
        $total_items += $quantity;
        $total_savings += $savings;
    }
}

$shipping = $total >= 100 ? 0 : 5.00;
$tax = $total * 0.05;
$grand_total = $total + $shipping + $tax;

$free_shipping_threshold = 100;
$remaining_for_free = max(0, $free_shipping_threshold - $total);
$free_shipping_progress = min(100, ($total / $free_shipping_threshold) * 100);

$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alihaha - Order Summary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .gradient-text {
            background: linear-gradient(135deg, #f97316, #fb923c, #fdba74);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-input:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .payment-option input:checked+.payment-content {
            border-color: #f97316;
            background: #f97316;
        }

        .step-active {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
        }

        .step-completed {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
        }

        .step-inactive {
            background: #f3f4f6;
            color: #9ca3af;
        }
    </style>
</head>

<body class="bg-blue-800 font-sans pb-16">

    <!-- HEADER -->
    <header class="glass backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="cart.php" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center text-gray-600 hover:bg-orange-100 hover:text-orange-500 transition">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h1 class="text-base font-bold text-orange-500">Order Summary</h1>
                    <p class="text-xs text-white"><?php echo $total_items; ?> item<?php echo $total_items != 1 ? 's' : ''; ?> • Review & confirm</p>
                </div>
            </div>
            <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                <i class="fas fa-lock text-sm"></i>
            </div>
        </div>

        <!-- Step Indicator -->
        <div class="max-w-6xl mx-auto px-4 pb-3">
            <div class="flex items-center gap-2">
                <div class="flex-1 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full step-completed flex items-center justify-center text-xs font-bold"><i class="fas fa-check text-xs"></i></div>
                    <span class="text-xs font-semibold text-green-600 hidden sm:block">Cart</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-500"></div>
                <div class="flex-1 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full step-active flex items-center justify-center text-xs font-bold">2</div>
                    <span class="text-xs font-semibold text-orange-500 hidden sm:block">Checkout</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200"></div>
                <div class="flex-1 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full step-inactive flex items-center justify-center text-xs font-bold">3</div>
                    <span class="text-xs font-semibold text-gray-400 hidden sm:block">Complete</span>
                </div>
            </div>
        </div>
    </header>

    <!-- ERROR -->
    <?php if ($error): ?>
        <div class="max-w-6xl mx-auto px-4 mt-4">
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-700 p-3 rounded-2xl text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- FREE SHIPPING -->
    <section class="max-w-6xl mx-auto px-4 pt-4">
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-orange-900 text-white rounded-2xl p-4">
            <div class="relative">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-truck text-orange-400"></i>
                    <?php if ($remaining_for_free > 0): ?>
                        <p class="text-xs font-medium">Add <span class="text-orange-400 font-bold">$<?php echo number_format($remaining_for_free, 2); ?></span> for FREE shipping!</p>
                    <?php else: ?>
                        <p class="text-xs font-bold text-green-400">🎉 FREE shipping unlocked!</p>
                    <?php endif; ?>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-400 h-2 rounded-full transition-all" style="width: <?php echo $free_shipping_progress; ?>%"></div>
                </div>
            </div>
        </div>
    </section>

    <!--  MAIN 2-COLUMN LAYOUT -->
  
    <form action="order_success.php" method="POST" id="checkoutForm">
        <input type="hidden" name="total" value="<?php echo $grand_total; ?>">

        <div class="max-w-6xl mx-auto px-4 pt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

            
            <!--  LEFT COLUMN: Items + Summary -->
           
            <div class="space-y-6">

                <!-- YOUR ITEMS -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-bold text-gray-800">
                            <i class="fas fa-shopping-bag text-orange-500 mr-1"></i> Your Items
                        </h2>
                        <a href="cart.php" class="text-xs text-orange-500 font-semibold">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                    </div>

                    <div class="glass rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="divide-y divide-gray-100">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="p-3 flex gap-3 hover:bg-orange-50/40 transition">
                                    <div class="relative flex-shrink-0">
                                        <?php if ($item['image']): ?>
                                            <img src="image/<?php echo htmlspecialchars($item['image']); ?>" class="w-20 h-20 object-cover rounded-xl">
                                        <?php else: ?>
                                            <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>
                                        <?php endif; ?>
                                        <span class="absolute -top-1.5 -right-1.5 bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold shadow-lg"><?php echo $item['quantity']; ?></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-bold text-gray-800 truncate mb-1"><?php echo htmlspecialchars($item['title']); ?></h3>
                                        <div class="flex items-center gap-2 text-xs text-white mb-1">
                                            <span class="bg-orange-50 text-orange-600 px-2 py-0.5 rounded-full font-medium"><?php echo htmlspecialchars($item['category']); ?></span>
                                            <span>Qty: <?php echo $item['quantity']; ?></span>
                                        </div>
                                        <div class="flex items-baseline gap-1.5">
                                            <p class="text-orange-500 font-bold text-sm">$<?php echo number_format($item['price'], 2); ?></p>
                                            <p class="text-white text-xs line-through">$<?php echo number_format($item['original_price'], 2); ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-xs text-gray-500 mb-0.5">Total</p>
                                        <p class="text-sm font-bold text-gray-800">$<?php echo number_format($item['line_total'], 2); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <!-- ORDER SUMMARY -->
                <section>
                    <h2 class="text-sm font-bold text-white mb-3">
                        <i class="fas fa-receipt text-orange-500 mr-1"></i> Order Summary
                    </h2>
                    <div class="bg-orange-500 rounded-3xl p-4 shadow-sm">
                        <div class="flex justify-between py-2 border-b border-white/20">
                            <span class="text-sm text-white">Subtotal (<?php echo $total_items; ?> items)</span>
                            <span class="text-sm font-semibold text-white">$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-white/20">
                            <span class="text-sm text-white"><i class="fas fa-tag text-blue-800 mr-1"></i> Total Savings</span>
                            <span class="text-sm font-semibold text-blue-800">-$<?php echo number_format($total_savings, 2); ?></span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-white/20">
                            <span class="text-sm text-white"><i class="fas fa-truck text-pink-800 mr-1"></i> Shipping</span>
                            <?php if ($shipping == 0): ?>
                                <span class="text-sm font-semibold text-blue-800">FREE</span>
                            <?php else: ?>
                                <span class="text-sm font-semibold text-white">$<?php echo number_format($shipping, 2); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex justify-between py-2 border-b border-white/20">
                            <span class="text-sm text-white"><i class="fas fa-percent text-white mr-1"></i> Tax (5%)</span>
                            <span class="text-sm font-semibold text-white">$<?php echo number_format($tax, 2); ?></span>
                        </div>
                        <div class="flex justify-between items-center pt-3 mt-1">
                            <div>
                                <span class="text-base font-bold text-gray-800">Grand Total</span>
                                <p class="text-xs text-white">Including all taxes</p>
                            </div>
                            <span class="text-2xl font-bold text-gray-900">$<?php echo number_format($grand_total, 2); ?></span>
                        </div>
                    </div>
                </section>

                <!-- TRUST BADGES -->
                <div class="grid grid-cols-3 gap-2">
                    <div class="glass rounded-xl p-3 text-center"><i class="fas fa-shield-alt text-yellow-500 text-lg mb-1"></i>
                        <p class="text-xs text-white font-medium">Secure</p>
                    </div>
                    <div class="glass rounded-xl p-3 text-center"><i class="fas fa-undo text-pink-800 text-lg mb-1"></i>
                        <p class="text-xs text-white font-medium">Returns</p>
                    </div>
                    <div class="glass rounded-xl p-3 text-center"><i class="fas fa-headset text-orange-500 text-lg mb-1"></i>
                        <p class="text-xs text-white font-medium">Support</p>
                    </div>
                </div>
            </div>

            
            <!--  RIGHT COLUMN: Shipping + Payment + Confirm -->
          
            <div class="space-y-6">

                <!-- SHIPPING INFO -->
                <section>
                    <h2 class="text-sm font-bold text-gray-800 mb-3">
                        <i class="fas fa-truck text-blue-500 mr-1"></i> Shipping Information
                    </h2>
                    <div class="glass rounded-3xl shadow-sm border border-gray-100 p-5 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-black b-1.5"><i class="fas fa-user text-orange-500 mr-1"></i> Full Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($user_name); ?>" readonly class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-black mb-1.5"><i class="fas fa-map-marker-alt text-orange-500 mr-1"></i> Shipping Address <span class="text-red-500">*</span></label>
                            <textarea name="address" required rows="3" placeholder="Enter your full shipping address (Street, City, State, ZIP)" class="form-input w-full border border-gray-200 rounded-xl p-3 text-sm focus:outline-none transition resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-black mb-1.5"><i class="fas fa-phone text-orange-500 mr-1"></i> Phone Number <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" required placeholder="+95 9 XXX XXX XXX" class="form-input w-full border border-gray-200 rounded-xl p-3 text-sm focus:outline-none transition">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-black mb-1.5"><i class="fas fa-city text-orange-500 mr-1"></i> City</label>
                                <input type="text" name="city" placeholder="Yangon" class="form-input w-full border border-gray-200 rounded-xl p-3 text-sm focus:outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-black mb-1.5"><i class="fas fa-mail-bulk text-orange-500 mr-1"></i> Postal Code</label>
                                <input type="text" name="postal" placeholder="11011" class="form-input w-full border border-gray-200 rounded-xl p-3 text-sm focus:outline-none transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-black mb-1.5"><i class="fas fa-comment-alt text-orange-500 mr-1"></i> Delivery Notes <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <input type="text" name="notes" placeholder="e.g., Call before delivery" class="form-input w-full border border-gray-200 rounded-xl p-3 text-sm focus:outline-none transition">
                        </div>
                    </div>
                </section>
            </div>

            <!-- PAYMENT METHOD -->
            <section>
                <h2 class="text-sm font-bold text-white mb-3"><i class="fas fa-credit-card text-purple-500 mr-1"></i> Payment Method</h2>
                <div class="space-y-2">
                    <label class="payment-option block cursor-pointer">
                        <input type="radio" name="payment" value="cod" checked class="hidden peer">
                        <div class="payment-content glass rounded-2xl border-2 border-gray-100 p-4 flex items-center gap-3 transition peer-checked:border-orange-500 peer-checked:bg-orange-500">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center text-white shadow-lg"><i class="fas fa-money-bill-wave"></i></div>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-gray-800">Cash on Delivery</h3>
                                <p class="text-xs text-white">Pay when you receive</p>
                            </div>
                            <i class="fas fa-check-circle text-orange-500 text-xl opacity-0 peer-checked:opacity-100 transition"></i>
                        </div>
                    </label>
                    <label class="payment-option block cursor-pointer">
                        <input type="radio" name="payment" value="bank" class="hidden peer">
                        <div class="payment-content glass rounded-2xl border-2 border-gray-100 p-4 flex items-center gap-3 transition peer-checked:border-orange-500 peer-checked:bg-orange-500">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg"><i class="fas fa-university"></i></div>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-gray-800">Bank Transfer</h3>
                                <p class="text-xs text-white">KBZ, AYA, CB Bank</p>
                            </div>
                            <i class="fas fa-check-circle text-orange-500 text-xl opacity-0 peer-checked:opacity-100 transition"></i>
                        </div>
                    </label>
                    <label class="payment-option block cursor-pointer">
                        <input type="radio" name="payment" value="wallet" class="hidden peer">
                        <div class="payment-content glass rounded-2xl border-2 border-gray-100 p-4 flex items-center gap-3 transition peer-checked:border-orange-500 peer-checked:bg-orange-500">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-600 rounded-xl flex items-center justify-center text-white shadow-lg"><i class="fas fa-mobile-alt"></i></div>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-gray-800">Mobile Wallet</h3>
                                <p class="text-xs text-white">KBZPay, Wave Money, AYA Pay</p>
                            </div>
                            <i class="fas fa-check-circle text-orange-500 text-xl opacity-0 peer-checked:opacity-100 transition"></i>
                        </div>
                    </label>
                </div>
            </section>

            <!-- TERMS + CONFIRM -->
            <section class="glass rounded-3xl shadow-sm border border-gray-100 p-5">
                <label class="flex items-start gap-2 cursor-pointer mt-20">
                    <input type="checkbox" required class="mt-0.5 w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                    <span class="text-xs text-white">
                        I agree to the <a href="#" class="text-orange-500 font-semibold">Terms & Conditions</a> and <a href="#" class="text-orange-500 font-semibold">Privacy Policy</a>
                    </span>
                </label>

                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-600 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-orange-300 hover:from-orange-600 hover:to-red-700 transition flex items-center justify-center gap-2 mt-10">
                    <i class="fas fa-check-circle"></i> Confirm Order — $<?php echo number_format($grand_total, 2); ?>
                </button>

                <div class="flex items-center justify-center gap-1 mt-3">
                    <i class="fas fa-lock text-green-500 text-xs"></i>
                    <p class="text-xs text-white">Your information is secure and encrypted</p>
                </div>
            </section>

        </div>
    </form>

    <div class="h-16"></div>

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