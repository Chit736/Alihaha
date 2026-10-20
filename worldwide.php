<?php
session_start();
include 'connection.php';
include 'function.php';

$current_page = basename($_SERVER['PHP_SELF']);


//  Country ကို PHP GET နဲ့ လက်ခံမယ်

$selected_country = isset($_GET['country']) ? trim($_GET['country']) : 'all';

//  Countries Data (ပိုလှတဲ့ gradient နဲ့)

$countries = [
    ['code' => 'all', 'flag' => '🌐', 'name' => 'All', 
     'gradient' => 'from-slate-600 to-slate-800', 'ring' => 'ring-slate-400'],
    ['code' => 'Vietnam', 'flag' => '🇻🇳', 'name' => 'Vietnam', 
     'gradient' => 'from-red-500 to-yellow-500', 'ring' => 'ring-red-400'],
    ['code' => 'India', 'flag' => '🇮🇳', 'name' => 'India', 
     'gradient' => 'from-orange-400 to-green-500', 'ring' => 'ring-orange-400'],
    ['code' => 'Hong Kong', 'flag' => '🇭🇰', 'name' => 'Hong Kong', 
     'gradient' => 'from-red-500 to-rose-600', 'ring' => 'ring-red-400'],
    ['code' => 'Indonesia', 'flag' => '🇮🇩', 'name' => 'Indonesia', 
     'gradient' => 'from-red-500 to-red-700', 'ring' => 'ring-red-400'],
    ['code' => 'Myanmar', 'flag' => '🇲🇲', 'name' => 'Myanmar', 
     'gradient' => 'from-yellow-400 to-green-600', 'ring' => 'ring-yellow-400'],
    ['code' => 'China', 'flag' => '🇨🇳', 'name' => 'China', 
     'gradient' => 'from-red-500 to-yellow-500', 'ring' => 'ring-red-400'],
    ['code' => 'South Korea', 'flag' => '🇰🇷', 'name' => 'S. Korea', 
     'gradient' => 'from-pink-400 to-purple-500', 'ring' => 'ring-pink-400'],
    ['code' => 'Thailand', 'flag' => '🇹🇭', 'name' => 'Thailand', 
     'gradient' => 'from-blue-500 to-red-500', 'ring' => 'ring-blue-400'],
];


//  Industry Hubs Data

$industry_hubs = [
    [
        'country' => 'South Korea',
        'flag' => '🇰🇷',
        'title' => 'Hair and body care',
        'desc' => 'Natural and affordable',
        'emoji' => '💄',
        'bg_image' => 'clothing company.webp',
        'count' => '450+'
    ],
    [
        'country' => 'Hong Kong',
        'flag' => '🇭🇰',
        'title' => 'Mother, kids and toys',
        'desc' => 'Chemical-free and safe',
        'emoji' => '🧸',
        'bg_image' => 'skin care company.webp',
        'count' => '320+'
    ],
    [
        'country' => 'China',
        'flag' => '🇨🇳',
        'title' => 'Industrial Machinery',
        'desc' => 'Factory direct pricing',
        'emoji' => '⚙️',
        'bg_image' => 'solar company.jpeg',
        'count' => '1.2K+'
    ],
    [
        'country' => 'Vietnam',
        'flag' => '🇻🇳',
        'title' => 'Agriculture Products',
        'desc' => 'Fresh from the farm',
        'emoji' => '🌾',
        'bg_image' => 'camera company 2.webp',
        'count' => '280+'
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alihaha - Worldwide Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
        .bottom-nav-item.active { color: #f97316; }
        .bottom-nav-item.active i { color: #f97316; }

        /*  Custom Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }

        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.7; }
            100% { transform: scale(0.95); opacity: 1; }
        }
        .pulse-ring { animation: pulse-ring 2s ease-in-out infinite; }

        /*  Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /*  Card Hover Effect */
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
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
            opacity: 0.4;
            pointer-events: none;
        }

        /*  Country Circle Hover */
        .country-circle {
            transition: all 0.3s ease;
        }
        .country-circle:hover {
            transform: scale(1.1);
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
                <?php echo ($current_page == 'index.php') ? 'text-ornage-500 border-b-2 border-ornage-500' :
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
       
        <!--  HERO SECTION - Alibaba ထက်ပိုလှ -->
      
        <section class="relative overflow-hidden text-white mx-4 mt-4 rounded-3xl"
            style="background-image: url('image/skin care company.webp'); background-position: left;">
            <!-- Decorative Blobs -->
            <div class="blob w-72 h-72 bg-orange-500 top-0 -left-20"></div>
            <div class="blob w-96 h-96 bg-pink-500 bottom-0 -right-20"></div>
            <div class="blob w-64 h-64 bg-blue-500 top-1/2 left-1/2"></div>

            <div class="relative px-6 py-10">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 glass rounded-full px-3 py-1.5 mb-4">
                    <span class="w-2 h-2 bg-green-400 rounded-full pulse-ring"></span>
                    <span class="text-xs font-medium">🌐 GLOBAL MARKETPLACE</span>
                </div>

                <!-- Title -->
                <h1 class="text-3xl font-bold leading-tight mb-3">
                    Source from
                    <span class="gradient-text">around the world</span>
                </h1>
                <p class="bg-green-500 rounded text-sm text-white mb-6 max-w-md">
                    Connect with 100,000+ verified suppliers from 150+ countries. 
                    Quality products, competitive prices, global shipping.
                </p>

                <!-- Stats Cards -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="glass rounded-xl w-40 p-3 text-center hover-lift">
                        <div class="text-green-500 text-2xl mb-1">
                            <i class="fas fa-building"></i>
                        </div>
                        <p class="text-white font-bold text-lg">100K+</p>
                        <p class="text-xs text-black">Suppliers</p>
                    </div>
                    <div class="glass rounded-xl w-40 p-3 text-center hover-lift">
                        <div class="text-green-500 text-2xl mb-1">
                            <i class="fas fa-globe-asia"></i>
                        </div>
                        <p class="text-white font-bold text-lg">150+</p>
                        <p class="text-xs text-black">Countries</p>
                    </div>
                    <div class="glass rounded-xl w-40 p-3 text-center hover-lift">
                        <div class="text-green-500 text-2xl mb-1">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <p class="text-white font-bold text-lg">1M+</p>
                        <p class="text-xs text-black">Products</p>
                    </div>
                </div>
            </div>

            <!-- Wave Bottom -->
            <svg class="absolute bottom-0 left-0 right-0" viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 80L60 74.7C120 69.3 240 58.7 360 53.3C480 48 600 48 720 53.3C840 58.7 960 69.3 1080 69.3C1200 69.3 1320 58.7 1380 53.3L1440 48V80H0Z" fill="#3feb0f"/>
            </svg>
        </section>

        
        <!--  COUNTRY CIRCLES -->
        
        <section class="glass px-4 py-6 -mt-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Browse by Country</h2>
                    <p class="text-xs text-white">Select a country to explore</p>
                </div>
                <a href="worldwide.php" class="text-xs text-orange-500 font-semibold">
                    View All →
                </a>
            </div>

            <div class="flex gap-4 overflow-x-auto hide-scroll pb-2">
                <?php foreach ($countries as $c): 
                    $is_active = ($selected_country === $c['code']);
                ?>
                    <a href="worldwide.php?country=<?php echo urlencode($c['code']); ?>" 
                       class="flex flex-col items-center min-w-[72px] country-circle">
                        <div class="relative">
                            <!-- Gradient Circle -->
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br <?php echo $c['gradient']; ?> 
                                        flex items-center justify-center text-2xl shadow-lg
                                        <?php echo $is_active ? 'ring-4 ring-offset-2 ' . $c['ring'] : ''; ?>">
                                <?php echo $c['flag']; ?>
                            </div>
                            <?php if ($is_active): ?>
                                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-orange-500 rounded-full"></div>
                            <?php endif; ?>
                        </div>
                        <span class="text-xs mt-2 <?php echo $is_active ? 'font-bold text-orange-500' : 'text-white'; ?>">
                            <?php echo $c['name']; ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        
        <!-- PRODUCTS SECTION -->
       
        <section class="px-4 py-4">
            <?php
            if ($selected_country === 'all') {
                $sql = "SELECT * FROM products ORDER BY id DESC LIMIT 8";
                $stmt = $conn->prepare($sql);
            } else {
                $sql = "SELECT p.* FROM products p 
                        INNER JOIN manufacturers m ON p.supplier_id = m.id 
                        WHERE m.country = ? 
                        ORDER BY p.id DESC LIMIT 8";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $selected_country);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            ?>

            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-800">
                        <?php 
                        if ($selected_country === 'all') {
                            echo "🔥 Featured Products";
                        } else {
                            echo "📦 Products from " . htmlspecialchars($selected_country);
                        }
                        ?>
                    </h2>
                    <p class="text-xs text-white mt-0.5">
                        <?php echo $result->num_rows; ?> products available
                    </p>
                </div>
                <a href="products.php" class="text-xs bg-orange-500 text-white px-3 py-1.5 rounded-full font-semibold">
                    See All
                </a>
            </div>

            <?php if ($result && $result->num_rows > 0): ?>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <?php while ($p = $result->fetch_assoc()): ?>
                        <div class="glass rounded-2xl shadow-sm overflow-hidden hover-lift border border-gray-100">
                            <!-- Image -->
                            <div class="relative">
                                <?php if ($p['image']): ?>
                                    <img src="image/<?php echo htmlspecialchars($p['image']); ?>" 
                                         class="w-full h-36 object-cover">
                                <?php else: ?>
                                    <div class="w-full h-36 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image text-3xl"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Wishlist Button -->
                                <button class="absolute top-2 right-2 w-7 h-7 bg-white/90 backdrop-blur rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 transition">
                                    <i class="far fa-heart text-xs"></i>
                                </button>
                                
                                <!-- Badge -->
                                <span class="absolute top-2 left-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs px-2 py-0.5 rounded-full font-semibold">
                                    NEW
                                </span>
                            </div>

                            <!-- Content -->
                            <div class="p-3">
                                <h3 class="text-xs font-semibold text-gray-800 truncate mb-1">
                                    <?php echo htmlspecialchars($p['title']); ?>
                                </h3>
                                
                                <!-- Rating -->
                                <div class="flex items-center gap-1 mb-2">
                                    <div class="flex text-yellow-400 text-xs">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span class="text-xs text-white">(4.5)</span>
                                </div>

                                <!-- Price -->
                                <div class="flex items-baseline gap-1 mb-2">
                                    <p class="text-orange-500 font-bold text-base">
                                        $<?php echo number_format($p['price'], 2); ?>
                                    </p>
                                    <p class="text-white text-xs line-through">
                                        $<?php echo number_format($p['price'] * 1.3, 2); ?>
                                    </p>
                                </div>

                                <!-- Add to Cart -->
                                <a href="cart-add.php?id=<?php echo $p['id']; ?>" 
                                   class="block text-center text-xs bg-gradient-to-r from-orange-500 to-orange-600 text-white py-2 rounded-xl font-semibold hover:from-orange-600 hover:to-orange-700 transition shadow-sm shadow-orange-200">
                                    <i class="fas fa-cart-plus mr-1"></i> Add to Cart
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-16 bg-white rounded-2xl shadow-sm">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-globe text-3xl text-gray-400"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-600 mb-2">No Products Found</h2>
                    <p class="text-sm text-gray-500 mb-4">Try selecting a different country.</p>
                    <a href="worldwide.php" class="inline-block bg-orange-500 text-white text-sm font-semibold px-6 py-2 rounded-full">
                        Show All Products
                    </a>
                </div>
            <?php endif; ?>
        </section>

       
        <!--  GLOBAL INDUSTRY HUBS -->
        
        <section class="px-4 py-6 glass mt-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-800">🌏 Global Industry Hubs</h2>
                    <p class="text-xs text-gray-500">Explore trending industries by region</p>
                </div>
            </div>

             <div class="flex gap-3 overflow-x-auto hide-scroll px-4 pb-2">
                <?php foreach ($industry_hubs as $hub): ?>
                    <div class="hub-card min-w-[240px] rounded-2xl overflow-hidden shadow-sm border border-gray-100">
                        <div class="h-32 relative p-4 overflow-hidden">

                            <!-- background Image  -->
                            <div class="absolute inset-0 bg-cover bg-center"
                                style="background-image: url('image/<?php echo htmlspecialchars($hub['bg_image']); ?>');">
                            </div>
                            <!-- Country Badge -->
                            <div class="inline-flex items-center gap-1 bg-orange-500 backdrop-blur text-white text-xs px-2 py-1 rounded-full">
                                <span><?php echo $hub['flag']; ?></span>
                                <span class="font-medium"><?php echo $hub['country']; ?></span>
                            </div>

                            <!-- Count Badge -->
                            <div class="absolute top-4 right-4 bg-black/30 backdrop-blur text-white text-xs px-2 py-1 rounded-full font-bold">
                                <?php echo $hub['count']; ?>
                            </div>

                            <!-- Big emoji -->
                            <div class="absolute bottom-2 right-3 text-5xl opacity-90 animate-float">
                                <?php echo $hub['emoji']; ?>
                            </div>

                            <!-- Shimmer -->
                            <div class="absolute inset-0 shimmer"></div>
                        </div>
                        <div class="p-3 bg-green-500">
                            <h3 class="font-bold text-gray-800 text-sm mb-0.5"><?php echo $hub['title']; ?></h3>
                            <p class="text-xs text-white"><?php echo $hub['desc']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>


        <!--  WHY CHOOSE US -->
        
        <section class="px-4 py-6">
            <h2 class="text-base font-bold text-gray-800 mb-4 text-center">Why Choose Alihaha Worldwide?</h2>
            
            <div class="grid grid-cols-2 gap-3">
                <div class="glass rounded-2xl p-4 shadow-sm border border-gray-100 text-center hover-lift">
                    <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-shield-alt text-blue-600 text-lg"></i>
                    </div>
                    <h3 class="text-xs font-bold text-gray-800 mb-1">Trade Assurance</h3>
                    <p class="text-xs text-white">Protected payments</p>
                </div>
                <div class="glass rounded-2xl p-4 shadow-sm border border-gray-100 text-center hover-lift">
                    <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-shipping-fast text-green-600 text-lg"></i>
                    </div>
                    <h3 class="text-xs font-bold text-gray-800 mb-1">Global Shipping</h3>
                    <p class="text-xs text-white">To 150+ countries</p>
                </div>
                <div class="glass rounded-2xl p-4 shadow-sm border border-gray-100 text-center hover-lift">
                    <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-headset text-purple-600 text-lg"></i>
                    </div>
                    <h3 class="text-xs font-bold text-gray-800 mb-1">24/7 Support</h3>
                    <p class="text-xs text-white">Always here to help</p>
                </div>
                <div class="glass rounded-2xl p-4 shadow-sm border border-gray-100 text-center hover-lift">
                    <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-medal text-orange-600 text-lg"></i>
                    </div>
                    <h3 class="text-xs font-bold text-gray-800 mb-1">Verified Suppliers</h3>
                    <p class="text-xs text-white">Quality guaranteed</p>
                </div>
            </div>
        </section>

      
        <!--  CTA SECTION -->
        
        <section class="px-4 py-6">
            <div class="relative overflow-hidden glass rounded-3xl p-6 text-white text-center">
                <div class="blob w-40 h-40 bg-yellow-400 top-0 -right-10"></div>
                <div class="blob w-32 h-32 bg-pink-400 bottom-0 -left-10"></div>
                
                <div class="relative">
                    <div class="w-16 h-16 mx-auto mb-4 bg-white/20 backdrop-blur rounded-full flex items-center justify-center">
                        <i class="fas fa-rocket text-3xl"></i>
                    </div>
                    <h2 class="text-xl font-bold mb-2">Start Sourcing Today</h2>
                    <p class="text-sm text-orange-100 mb-5">
                        Join thousands of businesses finding quality products worldwide
                    </p>
                    <a href="products.php" class="inline-block bg-white text-orange-600 font-bold px-8 py-3 rounded-full shadow-lg hover:shadow-xl transition">
                        <i class="fas fa-search mr-2"></i> Browse Products
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
    <nav class="fixed bottom-0 left-0 right-0 glass drop-blur-md border-t border-blue-300 flex justify-around py-2 z-50">
        <a href="index.php" class="bottom-nav-item flex flex-col items-center text-black text-xs">
            <i class="fas fa-home text-xl mb-1"></i><span>Home</span>
        </a>
        <a href="tips.php" class="bottom-nav-item flex flex-col items-center text-black text-xs">
            <i class="fas fa-eye text-xl mb-1"></i><span>Tips</span>
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