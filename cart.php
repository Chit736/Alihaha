<?php
session_start();
include 'connection.php';
include 'function.php';

$current_page = basename($_SERVER['PHP_SELF']);

$session_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
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
            'category' => $product['category_id'] ?? 'General',
            'quantity' => $quantity,
            'line_total' => $line_total
        );
        $total += $line_total;
        $total_items += $quantity;
        $total_savings += $savings;
    }
}

$shipping = $total > 0 ? 5.00 : 0;
$tax = $total * 0.05;
$grand_total = $total + $shipping + $tax;

$free_shipping_threshold = 100;
$remaining_for_free = max(0, $free_shipping_threshold - $total);
$free_shipping_progress = min(100, ($total / $free_shipping_threshold) * 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alihaha - Shopping Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
        .bottom-nav-item.active { color: #f97316; }
        .bottom-nav-item.active i { color: #f97316; }

        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
        .animate-float { animation: float 3s ease-in-out infinite; }

        @keyframes pulse-ring { 0% { transform: scale(0.95); opacity: 1; } 50% { transform: scale(1.1); opacity: 0.6; } 100% { transform: scale(0.95); opacity: 1; } }
        .pulse-ring { animation: pulse-ring 2s ease-in-out infinite; }

        @keyframes slide-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .slide-in { animation: slide-in 0.4s ease-out; }

        .glass { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.25); }

        .cart-item { transition: all 0.3s ease; }
        .cart-item:hover { transform: translateX(4px); box-shadow: 0 10px 25px rgba(249,115,22,0.1); border-color: #fdba74; }

        .gradient-text { background: linear-gradient(135deg,#f97316,#fb923c,#fdba74); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

        .blob { position: absolute; border-radius: 50%; filter: blur(60px); opacity: 0.35; pointer-events: none; }

        .progress-bar { background: linear-gradient(90deg,#f97316,#fb923c); transition: width 0.5s ease; }

        .qty-btn { transition: all 0.2s ease; }
        .qty-btn:hover { transform: scale(1.1); }

        .trash-btn { transition: all 0.3s ease; }
        .trash-btn:hover { transform: rotate(15deg) scale(1.1); }

        @keyframes shine { 0% { left: -100%; } 100% { left: 100%; } }
        .shine-effect { position: relative; overflow: hidden; }
        .shine-effect::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); animation: shine 3s infinite; }
    </style>
</head>
<body class="bg-blue-700 font-sans pb-16">

<!-- HEADER -->
<header class="glass backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="products.php" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center text-black hover:bg-orange-100 hover:text-orange-500 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-base font-bold text-orange-500">Shopping Cart</h1>
                <p class="text-xs text-white"><?php echo $total_items; ?> item<?php echo $total_items != 1 ? 's' : ''; ?> in cart</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <?php if (count($cart_items) > 0): ?>
                <a href="cart_clear.php" onclick="return confirm('Clear all items?')" class="w-9 h-9 bg-red-50 rounded-xl flex items-center justify-center text-red-500 hover:bg-red-100 transition">
                    <i class="fas fa-trash-alt text-sm"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- SUCCESS / ERROR -->
<?php if (isset($_GET['success'])): ?>
    <div class="max-w-6xl mx-auto px-4 mt-4">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 p-3 rounded-2xl text-sm flex items-center gap-2 slide-in">
            <i class="fas fa-check-circle text-green-500"></i>
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    </div>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
    <div class="max-w-6xl mx-auto px-4 mt-4">
        <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-700 p-3 rounded-2xl text-sm flex items-center gap-2 slide-in">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    </div>
<?php endif; ?>

<?php if (count($cart_items) > 0): ?>

<!--  2-COLUMN LAYOUT -->

<div class="max-w-6xl mx-auto px-4 pt-6 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    
    <!--  LEFT: CART ITEMS (2 cols span on lg) -->
  
    <div class="lg:col-span-2 space-y-4">

        <!-- YOUR ITEMS -->
        <section>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-gray-800">
                    <i class="fas fa-shopping-cart text-orange-500 mr-1"></i> Your Items
                </h2>
                <a href="products.php" class="text-xs text-orange-500 font-semibold">+ Add more</a>
            </div>

            <div class="space-y-3">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item slide-in glass rounded-2xl p-3 shadow-sm border border-gray-100">
                        <div class="flex gap-3">
                            <div class="relative flex-shrink-0">
                                <?php if ($item['image']): ?>
                                    <img src="image/<?php echo htmlspecialchars($item['image']); ?>" class="w-30 h-40 object-cover rounded-xl">
                                <?php else: ?>
                                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image text-xl"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="absolute -top-1 -left-1 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs px-1.5 py-0.5 rounded-full font-bold shadow-lg">-30%</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-bold text-gray-800 truncate mb-1"><?php echo htmlspecialchars($item['title']); ?></h3>
                                <span class="inline-block text-xs bg-orange-50 text-orange-600 px-2 py-0.5 rounded-full font-medium mb-2">
                                    <?php echo htmlspecialchars($item['category']); ?>
                                </span>

                                <div class="flex items-baseline gap-1.5 mb-2">
                                    <p class="text-orange-500 font-bold text-base">$<?php echo number_format($item['price'], 2); ?></p>
                                    <p class="text-white text-xs line-through">$<?php echo number_format($item['original_price'], 2); ?></p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1 bg-gray-100 rounded-full p-0.5">
                                        <a href="cart_update.php?id=<?php echo $item['product_id']; ?>&action=decrease" class="qty-btn w-7 h-7 bg-white rounded-full flex items-center justify-center text-gray-600 shadow-sm">
                                            <i class="fas fa-minus text-xs"></i>
                                        </a>
                                        <span class="text-sm font-bold text-gray-800 w-8 text-center"><?php echo $item['quantity']; ?></span>
                                        <a href="cart_update.php?id=<?php echo $item['product_id']; ?>&action=increase" class="qty-btn w-7 h-7 bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-full flex items-center justify-center shadow-sm">
                                            <i class="fas fa-plus text-xs"></i>
                                        </a>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-bold text-gray-800">$<?php echo number_format($item['line_total'], 2); ?></p>
                                        <a href="cart_remove.php?id=<?php echo $item['product_id']; ?>" class="trash-btn w-7 h-7 bg-red-50 rounded-full flex items-center justify-center text-red-500">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- TRUST BADGES (moved here on left) -->
        <section>
            <div class="grid grid-cols-3 gap-2">
                <div class="glass rounded-xl p-2.5 text-center border border-gray-100">
                    <i class="fas fa-shield-alt text-yellow-600 text-lg mb-1"></i>
                    <p class="text-xs text-white font-medium">Secure</p>
                </div>
                <div class="glass rounded-xl p-2.5 text-center border border-gray-100">
                    <i class="fas fa-undo text-pink-800 text-lg mb-1"></i>
                    <p class="text-xs text-white font-medium">Returns</p>
                </div>
                <div class="glass rounded-xl p-2.5 text-center border border-gray-100">
                    <i class="fas fa-headset text-orange-500 text-lg mb-1"></i>
                    <p class="text-xs text-white font-medium">Support</p>
                </div>
            </div>
        </section>
    </div>

    
    <!--  RIGHT: SUMMARY + STATS + CHECKOUT -->
    
    <div class="space-y-4 lg:sticky lg:top-24">

        <!-- FREE SHIPPING PROGRESS -->
        <section>
            <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-orange-900 text-white rounded-2xl p-4">
                <div class="blob w-24 h-24 bg-orange-500 top-0 -right-10"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-truck text-orange-400"></i>
                        <?php if ($remaining_for_free > 0): ?>
                            <p class="text-xs font-medium">Add <span class="text-orange-400 font-bold">$<?php echo number_format($remaining_for_free, 2); ?></span> for FREE shipping!</p>
                        <?php else: ?>
                            <p class="text-xs font-bold text-green-400">🎉 You've unlocked FREE shipping!</p>
                        <?php endif; ?>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden">
                        <div class="progress-bar h-2 rounded-full" style="width: <?php echo $free_shipping_progress; ?>%"></div>
                    </div>
                    <div class="flex justify-between items-center mt-1.5">
                        <span class="text-xs text-gray-400">$0</span>
                        <span class="text-xs text-gray-400">$<?php echo $free_shipping_threshold; ?></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- CART STATS -->
        <section>
            <div class="grid grid-cols-3 gap-2">
                <div class="glass rounded-2xl p-2.5 text-center border border-gray-100">
                    <div class="w-8 h-8 mx-auto mb-1 bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-orange-500 text-xs"></i>
                    </div>
                    <p class="text-base font-bold text-gray-800"><?php echo $total_items; ?></p>
                    <p class="text-xs text-white">Items</p>
                </div>
                <div class="glass rounded-2xl p-2.5 text-center border border-gray-100">
                    <div class="w-8 h-8 mx-auto mb-1 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tag text-green-500 text-xs"></i>
                    </div>
                    <p class="text-base font-bold text-green-500">$<?php echo number_format($total_savings, 0); ?></p>
                    <p class="text-xs text-white">Saved</p>
                </div>
                <div class="glass rounded-2xl p-2.5 text-center border border-gray-100">
                    <div class="w-8 h-8 mx-auto mb-1 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-blue-500 text-xs"></i>
                    </div>
                    <p class="text-base font-bold text-gray-800"><?php echo count($cart_items); ?></p>
                    <p class="text-xs text-white">Products</p>
                </div>
            </div>
        </section>

        <!-- ORDER SUMMARY -->
        <section>
            <h2 class="text-sm font-bold text-white mb-3">
                <i class="fas fa-receipt text-orange-500 mr-1"></i> Order Summary
            </h2>
            <div class="bg-orange-500 rounded-2xl p-4 shadow-sm">
                <div class="flex justify-between items-center py-2 border-b border-white/20">
                    <span class="text-sm text-white">Subtotal (<?php echo $total_items; ?> items)</span>
                    <span class="text-sm font-semibold text-white">$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-white/20">
                    <span class="text-sm text-white"><i class="fas fa-tag text-blue-800 mr-1"></i> Total Savings</span>
                    <span class="text-sm font-semibold text-blue-800">-$<?php echo number_format($total_savings, 2); ?></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-white/20">
                    <span class="text-sm text-white"><i class="fas fa-truck text-pink-800 mr-1"></i> Shipping</span>
                    <?php if ($shipping == 0): ?>
                        <span class="text-sm font-semibold text-blue-800">FREE</span>
                    <?php else: ?>
                        <span class="text-sm font-semibold text-gray-800">$<?php echo number_format($shipping, 2); ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-white/20">
                    <span class="text-sm text-white"><i class="fas fa-percent text-white mr-1"></i> Tax (5%)</span>
                    <span class="text-sm font-semibold text-gray-800">$<?php echo number_format($tax, 2); ?></span>
                </div>
                <div class="flex justify-between items-center pt-3 mt-1">
                    <span class="text-base font-bold text-gray-800">Grand Total</span>
                    <span class="text-xl font-bold text-black">$<?php echo number_format($grand_total, 2); ?></span>
                </div>
            </div>
        </section>

        <!-- PROMO CODE -->
        <section>
            <div class="glass border border-orange-200 rounded-2xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center text-white">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-gray-800">Have a promo code?</h3>
                        <p class="text-xs text-white">Apply it at checkout</p>
                    </div>
                    <i class="fas fa-chevron-right text-orange-500"></i>
                </div>
            </div>
        </section>

        <!-- CHECKOUT BUTTON (inline on right column) -->
        <section>
            <form action="checkout.php" method="POST">
                <input type="hidden" name="total" value="<?php echo $grand_total; ?>">
                <button type="submit" class="shine-effect w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-orange-300 hover:from-orange-600 hover:to-orange-700 transition flex items-center justify-center gap-2">
                    <i class="fas fa-credit-card"></i>
                    Proceed to Checkout
                </button>
            </form>
            <p class="text-xs text-white text-center mt-2">
                <i class="fas fa-lock text-green-500 mr-1"></i> Secure checkout
            </p>
        </section>
    </div>
</div>

<?php else: ?>

<!-- EMPTY CART -->
<section class="px-4 py-12">
    <div class="text-center max-w-md mx-auto">
        <div class="w-32 h-32 mx-auto mb-6 glass rounded-full flex items-center justify-center animate-float">
            <i class="fas fa-shopping-cart text-5xl text-orange-500"></i>
        </div>
        <h2 class="text-xl font-bold text-black mb-2">Your Cart is Empty</h2>
        <p class="text-sm text-white/60 mb-6">Looks like you haven't added anything yet. Start shopping to fill it up!</p>
        <a href="products.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold px-8 py-3 rounded-2xl shadow-lg shadow-orange-300 hover:from-orange-600 hover:to-orange-700 transition">
            <i class="fas fa-shopping-bag"></i> Start Shopping
        </a>

        <div class="mt-10">
            <h3 class="text-sm font-bold text-black mb-3">Popular Categories</h3>
            <div class="flex flex-wrap gap-2 justify-center">
                <a href="products.php?category=General" class="bg-white border border-gray-200 text-gray-700 text-xs px-4 py-2 rounded-full hover:border-orange-300 hover:text-orange-500 transition"><i class="fas fa-box mr-1"></i> General</a>
                <a href="products.php?category=Industrial%20Machinery" class="bg-white border border-gray-200 text-gray-700 text-xs px-4 py-2 rounded-full hover:border-orange-300 hover:text-orange-500 transition"><i class="fas fa-industry mr-1"></i> Machinery</a>
                <a href="products.php?category=Chemicals" class="bg-white border border-gray-200 text-gray-700 text-xs px-4 py-2 rounded-full hover:border-orange-300 hover:text-orange-500 transition"><i class="fas fa-flask mr-1"></i> Chemicals</a>
                <a href="products.php?category=Testing%20Instrument" class="bg-white border border-gray-200 text-gray-700 text-xs px-4 py-2 rounded-full hover:border-orange-300 hover:text-orange-500 transition"><i class="fas fa-microscope mr-1"></i> Instruments</a>
            </div>
        </div>
    </div>
</section>

<!-- WHY SHOP -->
<section class="max-w-md mx-auto px-4 pb-6">
    <div class="glass rounded-3xl p-5 shadow-sm border border-gray-100">
        <h3 class="text-sm font-bold text-gray-800 mb-4 text-center">Why Shop with Alihaha?</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center"><i class="fas fa-shield-alt text-green-600"></i></div>
                <p class="text-xs font-bold text-gray-800">Trade Assurance</p>
                <p class="text-xs text-white mt-0.5">Protected payments</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center"><i class="fas fa-shipping-fast text-blue-600"></i></div>
                <p class="text-xs font-bold text-gray-800">Fast Delivery</p>
                <p class="text-xs text-white mt-0.5">Global shipping</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center"><i class="fas fa-medal text-purple-600"></i></div>
                <p class="text-xs font-bold text-gray-800">Quality Assured</p>
                <p class="text-xs text-white mt-0.5">Verified suppliers</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl flex items-center justify-center"><i class="fas fa-headset text-orange-600"></i></div>
                <p class="text-xs font-bold text-gray-800">24/7 Support</p>
                <p class="text-xs text-white mt-0.5">Always here</p>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>

<!-- FOOTER (same as before) -->
<footer class="glass border-t border-gray-200 mt-8 pb-20">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
            <div>
                <h4 class="text-sm font-bold text-gray-800 mb-3">About Alihaha</h4>
                <ul class="space-y-2 text-xs text-white">
                    <li><a href="#" class="hover:text-orange-500">Why choose Alihaha</a></li>
                    <li><a href="#" class="hover:text-orange-500">Co-Create Pitch</a></li>
                    <li><a href="#" class="hover:text-orange-500">Corporate responsibility</a></li>
                    <li><a href="#" class="hover:text-orange-500">Careers</a></li>
                </ul>
            </div>
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
            <div>
                <h4 class="text-sm font-bold text-gray-800 mb-3">Source on Alihaha</h4>
                <ul class="space-y-2 text-xs text-white">
                    <li><a href="#" class="hover:text-orange-500">Verified manufacturers</a></li>
                    <li><a href="#" class="hover:text-orange-500">Request for Quotation</a></li>
                </ul>
            </div>
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
            <div>
                <h4 class="text-sm font-bold text-gray-800 mb-3">Sell on Alihaha</h4>
                <ul class="space-y-2 text-xs text-white">
                    <li><a href="#" class="hover:text-orange-500">Sell on Alihaha</a></li>
                    <li><a href="#" class="hover:text-orange-500">Start selling</a></li>
                    <li><a href="#" class="hover:text-orange-500">Check order status</a></li>
                    <li><a href="#" class="hover:text-orange-500">Become a Verified Supplier</a></li>
                    <li><a href="#" class="hover:text-orange-500">Partnerships</a></li>
                </ul>
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

        <div class="border-t border-gray-100 pt-6 mt-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-wrap justify-center items-center gap-3 py-4 border-t border-gray-100">
                    <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition"><i class="fab fa-cc-visa text-blue-700 text-2xl"></i></a>
                    <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition"><i class="fab fa-cc-mastercard text-red-600 text-2xl"></i></a>
                    <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition"><i class="fab fa-cc-paypal text-blue-500 text-2xl"></i></a>
                    <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition"><i class="fab fa-cc-amex text-blue-800 text-2xl"></i></a>
                    <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition"><i class="fab fa-apple-pay text-black text-2xl"></i></a>
                    <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition"><i class="fab fa-google-pay text-gray-700 text-2xl"></i></a>
                    <a href="#" class="bg-white border border-gray-200 rounded px-3 py-1.5 hover:shadow-md transition"><i class="fab fa-cc-discover text-orange-500 text-2xl"></i></a>
                </div>
                <div class="flex items-center gap-3 justify-center md:justify-end">
                    <a href="#" class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                        <i class="fab fa-apple text-2xl"></i>
                        <div class="leading-tight"><p class="text-[10px] text-gray-300">Download on the</p><p class="text-sm font-semibold">App Store</p></div>
                    </a>
                    <a href="#" class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                        <i class="fab fa-google-play text-xl text-green-400"></i>
                        <div class="leading-tight"><p class="text-[10px] text-gray-300">GET IT ON</p><p class="text-sm font-semibold">Google Play</p></div>
                    </a>
                </div>
            </div>
        </div>

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
            <p class="text-xs text-white">© 1999-2026 Alihaha.com. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- BOTTOM NAVIGATION -->
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