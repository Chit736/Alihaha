<?php
session_start();
include 'connection.php';
include 'function.php';

// header အတွက်ပါ 
$current_page = basename($_SERVER['PHP_SELF']);


$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$user_name = $is_logged_in ? ($_SESSION['user_name'] ?? 'User') : 'Guest';


//  Quick Tags

$quick_tags = [
    ['icon' => '🔥', 'label' => 'Verified manufacturer search', 'color' => 'from-orange-100 to-orange-100', 'text' => 'text-black-600'],
    ['icon' => '🏢', 'label' => 'About Alihaha', 'color' => 'from-orange-100 to-orange-100', 'text' => 'text-black-600'],
    ['icon' => '📊', 'label' => 'Analyze bestsellers', 'color' => 'from-orange-100 to-orange-100', 'text' => 'text-black-600'],
    ['icon' => '🔍', 'label' => 'Product search', 'color' => 'from-red-100 to-orange-100', 'text' => 'text-black-600'],
    ['icon' => '💡', 'label' => 'Get product ideas', 'color' => 'from-red-100 to-orange-100', 'text' => 'text-black-600'],
];


//  Company Info 

$company_info = [
    [
        'icon' => 'fa-building',
        'title' => 'About Alihaha',
        'desc' => 'Global B2B marketplace connecting buyers & suppliers',
        'gradient' => 'from-orange-400 to-red-600',
        'bg' => 'from-orange-50 to-red-50',
        'text' => 'text-orange-600',
        'badge' => '2026'
    ],
    [
        'icon' => 'fa-globe',
        'title' => 'Our Services',
        'desc' => 'Product sourcing, verification, and shipping',
        'gradient' => 'from-blue-400 to-cyan-600',
        'bg' => 'from-blue-50 to-cyan-50',
        'text' => 'text-blue-600',
        'badge' => 'Global'
    ],
    [
        'icon' => 'fa-shield-alt',
        'title' => 'Why Alihaha',
        'desc' => 'Verified suppliers, secure payment, 24/7 support',
        'gradient' => 'from-green-400 to-emerald-600',
        'bg' => 'from-green-50 to-emerald-50',
        'text' => 'text-green-600',
        'badge' => 'Trusted'
    ],
    [
        'icon' => 'fa-headset',
        'title' => 'Contact Us',
        'desc' => 'Get in touch with our support team',
        'gradient' => 'from-purple-400 to-pink-600',
        'bg' => 'from-purple-50 to-pink-50',
        'text' => 'text-purple-600',
        'badge' => null
    ],
];


// Trending Products 

$trending = [];
$sql = "SELECT * FROM products ORDER BY id DESC LIMIT 6";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $trending[] = $row;
    }
}


//  Suggestions

$suggestions = [
    ['icon' => 'fa-lightbulb', 'text' => 'Find trending phone cases under $5'],
    ['icon' => 'fa-chart-bar', 'text' => 'What are the top-selling beauty products?'],
    ['icon' => 'fa-truck', 'text' => 'Best suppliers for eco-friendly packaging'],
    ['icon' => 'fa-globe', 'text' => 'Show me products from South Korea'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alihaha - Home</title>
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

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-6px);
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

        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .gradient-text {
            background: linear-gradient(135deg, #f97316, #fb923c, #fdba74);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.35;
            pointer-events: none;
        }

        .ai-card {
            transition: all 0.3s ease;
        }

        .ai-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(249, 115, 22, 0.15);
        }

        .ai-card:hover .icon-box {
            transform: scale(1.1) rotate(-5deg);
        }

        .icon-box {
            transition: transform 0.3s ease;
        }

        .quick-tag {
            transition: all 0.3s ease;
        }

        .quick-tag:hover {
            transform: translateY(-2px) scale(1.02);
        }

        .suggestion-item {
            transition: all 0.3s ease;
        }

        .suggestion-item:hover {
            transform: translateX(4px);
            background: linear-gradient(90deg, #fff7ed 0%, #ffffff 100%);
            border-color: #fdba74;
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
                <a href="index.php" class="flex-1 text-center py-2.5 text-sm font-semibold 
                <?php echo ($current_page == 'index.php') ? 'text-orange-500 border-b-2 border-orange-500' :
                'text-black hover:text-orange-500'; ?>">Home</a>

                <a href="products.php" class="flex-1 text-center py-2.5 text-sm font-semibold 
                <?php echo ($current_page == 'products.php') ? 'text-orange-500 border-b-2 border-orange-500':
                 'text-black hover:text-orange-500'; ?>">Products</a>

                <a href="manufacturers.php" class="flex-1 text-center py-2.5 text-sm font-semibold
                <?php echo ($current_page == 'manufacturers.php') ? 'text-orange-500 border-b-2 border-orange-500' :
                 'text-black hover:text-orange-500'; ?>">Manufacturers</a>


                <a href="worldwide.php" class="flex-1 text-center py-2.5 text-sm font-semibold
                <?php echo ($current_page =='worldwide.php') ? 'text-orange-500 border-b-2 border-orange-500':
                 'text-black hover:text-orange-500';?>">Worldwide</a>
            </div>
        </nav>
    </header>

    <main>

        <!-- HERO SECTION -->

        <section class="relative overflow-hidden text-white mx-4 mt-4 rounded-3xl"
            style="background-image: url('image/shopping8.jpeg'); background-position: left;">

            <div class="absolute inset-0 bg-black/20"></div>

            <div class="blob w-40 h-40 top-0 -right-10"></div>
            <div class="blob w-32 h-32 bottom-0 -left-10"></div>

            <div class="relative p-6">
                <div class="inline-flex items-center gap-2 glass rounded-full px-3 py-1.5 mb-4">
                    <span class="w-2 h-2 bg-blue-800 rounded-full pulse-ring"></span>
                    <span class="text-xs font-medium">🏢 SINCE 2026</span>
                </div>

                <h1 class="text-3xl font-bold leading-tight mb-3">
                    All sourcing tasks
                    <br>
                    in <span class="gradient-text">one ask</span>
                </h1>

                <p class="text-sm text-white mb-5 max-w-md">
                    Smart business with Alihaha. Describe your needs and find the best products, suppliers, and insights.
                </p>

                <div class="grid grid-cols-3 gap-2">
                    <div class="glass rounded-xl p-2.5 text-center">
                        <p class="text-orange-400 font-bold text-base">10K+</p>
                        <p class="text-xs text-white">Products</p>
                    </div>
                    <div class="glass rounded-xl p-2.5 text-center">
                        <p class="text-red-400 font-bold text-base">99%</p>
                        <p class="text-xs text-white">Verified</p>
                    </div>
                    <div class="glass rounded-xl p-2.5 text-center">
                        <p class="text-yellow-400 font-bold text-base">24/7</p>
                        <p class="text-xs text-white">Support</p>
                    </div>
                </div>
            </div>
            <!-- Wave Bottom -->
            <svg class="absolute bottom-0 left-0 right-0" viewBox="0 0 1440 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 40L60 36C120 32 240 24 360 22C480 20 600 24 720 26C840 28 960 28 1080 26C1200 24 1320 20 1380 18L1440 16V40H0Z" fill="#f97316" />
            </svg>

        </section>


        <!--  SEARCH BOX -->

        <section class="px-4 pt-6">
            <div class="relative glass rounded-3xl border-2 border-blue-800 p-4 shadow-sm shadow-blue-100">
                <div class="absolute -top-3 -right-3 w-10 h-10 bg-blue-800 rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-search text-sm animate-float"></i>
                </div>

                <div class="flex items-start gap-2 mb-3 glass">
                    <i class="fas fa-sparkles text-blue-500 mt-1"></i>
                    <textarea rows="3"
                        placeholder="Describe your needs... e.g., 'I want to find eco-friendly phone cases under $5 from verified suppliers'"
                        class="w-full focus:outline-none text-sm text-black resize-none placeholder-gray-400"></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <button class="w-9 h-9 bg-orange-500 rounded-full flex items-center justify-center text-white hover:bg-orange-100 hover:text-orange-500 transition">
                            <i class="fas fa-paperclip text-sm"></i>
                        </button>
                        <button class="w-9 h-9 bg-orange-500 rounded-full flex items-center justify-center text-white hover:bg-orange-100 hover:text-orange-500 transition">
                            <i class="fas fa-image text-sm"></i>
                        </button>
                        <button class="w-9 h-9 bg-orange-500 rounded-full flex items-center justify-center text-white hover:bg-orange-100 hover:text-orange-500 transition">
                            <i class="fas fa-microphone text-sm"></i>
                        </button>
                    </div>

                    <button class="send-btn w-12 h-12 bg-gradient-to-br bg-orange-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-200">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <div class="mt-3 flex items-center gap-2 px-2">
                <span class="text-xs text-white-500">Ready to help...</span>
            </div>
        </section>

        <!--  QUICK TAGS -->

        <section class="pt-5">
            <div class="px-4 flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-black-800">
                    <i class="fas fa-bolt text-black-500 mr-1"></i> Quick Actions
                </h2>
                <span class="text-xs text-white-500">Tap to try</span>
            </div>

            <div class="flex gap-2 overflow-x-auto hide-scroll px-4 pb-2 text-black-500">
                <?php foreach ($quick_tags as $tag): ?>
                    <button class="quick-tag bg-white <?php echo $tag['color']; ?> border border-white text-xs px-4 py-2.5 rounded-full whitespace-nowrap flex items-center gap-2 shadow-sm">
                        <span><?php echo $tag['icon']; ?></span>
                        <span class="<?php echo $tag['text']; ?> text-black font-semibold"><?php echo $tag['label']; ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </section>


        <!--  SUGGESTIONS -->

        <section class="px-4 pt-4">
            <div class="glass rounded-3xl p-4 border border-blue-100">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-300 rounded-xl flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-lightbulb text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Suggestions</h3>
                        <p class="text-xs text-white">Try asking these</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <?php foreach ($suggestions as $s): ?>
                        <button class="suggestion-item w-full bg-orange-500 rounded-2xl p-3 border border-orange-100 flex items-center gap-3 text-left">
                            <div class="w-8 h-8 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas <?php echo $s['icon']; ?> text-blue-500 text-xs"></i>
                            </div>
                            <span class="text-xs text-gray-700 flex-1"><?php echo $s['text']; ?></span>
                            <i class="fas fa-arrow-right text-orange-400 text-xs"></i>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>


        <!--  COMPANY INFO  -->

        <section class="px-4 pt-6">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-sm font-bold text-white-800">
                        <i class="fas fa-building text-orange-500 mr-1"></i> About Alihaha
                    </h2>
                    <p class="text-xs text-white-500 mt-0.5">Your trusted B2B partner</p>
                </div>
                <span class="text-xs bg-orange-100 text-orange-600 px-2 py-1 rounded-full font-semibold">
                    Since 2026
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <?php foreach ($company_info as $item): ?>
                    <div class="ai-card slide-in glass rounded-2xl p-4 shadow-sm border border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="icon-box w-12 h-12 rounded-2xl bg-orange-500 <?php echo $item['gradient']; ?> flex items-center justify-center text-white shadow-lg flex-shrink-0">
                                <i class="fas <?php echo $item['icon']; ?>"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-bold text-gray-800 text-sm">
                                        <?php echo $item['title']; ?>
                                    </h3>
                                    <?php if ($item['badge']): ?>
                                        <span class="bg-gradient-to-r <?php echo $item['gradient']; ?> text-white text-xs px-2 py-0.5 rounded-full font-bold">
                                            <?php echo $item['badge']; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-white leading-relaxed">
                                    <?php echo $item['desc']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- TRENDING PRODUCTS -->

        <?php if (count($trending) > 0): ?>
            <section class="pt-6">
                <div class="px-4 flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-sm font-bold text-gray-800">
                            <i class="fas fa-fire text-red-500 mr-1"></i> Trending Now
                        </h2>
                        <p class="text-xs text-white-500 mt-0.5">Hot products</p>
                    </div>
                    <a href="products.php" class="text-xs text-orange-500 font-semibold">
                        See All →
                    </a>
                </div>

                <div class="flex gap-3 overflow-x-auto hide-scroll px-4 pb-2">
                    <?php foreach ($trending as $p): ?>
                        <a href="cart-add.php?id=<?php echo $p['id']; ?>"
                            class="min-w-[160px] bg-blue-800 rounded-1xl shadow-sm border p-1 border-orange-500 overflow-hidden hover-lift">
                            <div class="relative">
                                <?php if ($p['image']): ?>
                                    <img src="image/<?php echo htmlspecialchars($p['image']); ?>"
                                        class="w-full h-32 rounded-1xl object-cover">
                                <?php else: ?>
                                    <div class="w-full h-32 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="absolute top-2 left-2 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">
                                    🔥 HOT
                                </span>
                            </div>
                            <div class="p-2.5">
                                <h3 class="text-xs font-semibold text-white truncate">
                                    <?php echo htmlspecialchars($p['title']); ?>
                                </h3>
                                <p class="text-orange-500 font-bold text-sm mt-1">
                                    $<?php echo number_format($p['price'], 2); ?>
                                </p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>


        <!--  HOW IT WORKS -->

        <section class="px-4 pt-6">
            <div class="glass rounded-3xl p-5 shadow-sm border border-gray-100">
                <div class="text-center mb-5">
                    <h2 class="text-base font-bold text-gray-800 mb-1">How Alihaha Works</h2>
                    <p class="text-xs text-white">3 simple steps</p>
                </div>

                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0">
                            1
                        </div>
                        <div class="flex-1 pt-1">
                            <h3 class="text-sm font-bold text-gray-800 mb-0.5">Describe Your Needs</h3>
                            <p class="text-xs text-white">Type in plain language what you're looking for</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0">
                            2
                        </div>
                        <div class="flex-1 pt-1">
                            <h3 class="text-sm font-bold text-gray-800 mb-0.5">We Analyze</h3>
                            <p class="text-xs text-white">Our team searches millions of products & suppliers</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-600 flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0">
                            3
                        </div>
                        <div class="flex-1 pt-1">
                            <h3 class="text-sm font-bold text-gray-800 mb-0.5">Get Results</h3>
                            <p class="text-xs text-white">Receive curated recommendations instantly</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!--  CTA SECTION -->

        <section class="px-4 pt-6 pb-6">
            <div class="relative overflow-hidden glass rounded-3xl p-6 text-white text-center gradient-animate">
                <div class="blob w-32 h-32 bg-yellow-300 top-0 -right-10"></div>
                <div class="blob w-24 h-24 bg-pink-400 bottom-0 -left-10"></div>

                <div class="relative">
                    <div class="w-14 h-14 mx-auto mb-3 bg-orange-500 backdrop-blur rounded-2xl flex items-center justify-center animate-float">
                        <i class="fas fa-shopping-bag text-white text-2xl"></i>
                    </div>
                    <h2 class="text-lg font-bold mb-1">Start Shopping Now</h2>
                    <p class="text-xs text-orange-100 mb-4 max-w-xs mx-auto">
                        Experience the best B2B marketplace with Alihaha
                    </p>
                    <a href="products.php" class="inline-block bg-orange-500 text-white font-bold px-6 py-2.5 rounded-full shadow-lg hover:shadow-xl transition">
                        <i class="fas fa-arrow-right mr-2"></i> Browse Products
                    </a>
                </div>
            </div>
        </section>
    </main>


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