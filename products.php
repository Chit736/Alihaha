<?php
session_start();
include 'connection.php';
include 'function.php';

// header အတွက် 
$current_page = basename($_SERVER['PHP_SELF']);

//  Search, Category, Manufacturer Filter, sort

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$manufacturer_filter = isset($_GET['manufacturer']) ? intval($_GET['manufacturer']) : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';


//SQL Query Dynamic ဖန်တီ:

$sql = "SELECT p.*, m.name AS m_name, m.country AS m_country, m.flag AS m_flag 
        FROM products p 
        LEFT JOIN manufacturers m ON p.supplier_id = m.id 
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE 1=1";
$params = [];
$types = "";

// Search filter
if (!empty($search)) {
    $sql .= " AND p.title LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

// Category filter
if ($category !== 'all') {
    $sql .= " AND c.name = ?";
    $params[] = $category;
    $types .= "s";
}

// Manufacturer filter
if ($manufacturer_filter > 0) {
    $sql .= " AND p.supplier_id = ?";
    $params[] = $manufacturer_filter;
    $types .= "i";
}

// Sort
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'title':
        $sql .= " ORDER BY p.title ASC";
        break;
    default:
        $sql .= " ORDER BY p.id DESC";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$total_products = $result->num_rows;

//  Categories List
$categories = [
    'all' => ['label' => 'All', 'icon' => 'fa-th-large'],
    'General' => ['label' => 'General', 'icon' => 'fa-box'],
    'Industrial Machinery' => ['label' => 'Machinery', 'icon' => 'fa-industry'],
    'Chemicals' => ['label' => 'Chemicals', 'icon' => 'fa-flask'],
    'Testing Instrument' => ['label' => 'Instruments', 'icon' => 'fa-microscope'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alihaha - Products</title>
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
                transform: scale(1.05);
                opacity: 0.7;
            }

            100% {
                transform: scale(0.95);
                opacity: 1;
            }
        }

        .pulse-ring {
            animation: pulse-ring 2s ease-in-out infinite;
        }

        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        /*  Card Hover */
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(249, 115, 22, 0.15);
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

        /*  Category Pill */
        .category-pill {
            transition: all 0.3s ease;
        }

        .category-pill:hover {
            transform: translateY(-2px);
        }

        /*  Product Card Image Zoom */
        .product-img {
            transition: transform 0.4s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.08);
        }

        /*  Badge Pulse */
        .badge-hot {
            animation: pulse-ring 1.5s ease-in-out infinite;
        }
    </style>
</head>

<body class="bg-blue-700 font-sans pb-16">


    <!--  HEADER -->

    <header class="glass backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-gray-100">
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3 flex-1">
                <div class="flex items-center justify-center text-orange-500 font-bold text-lg">
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
                <?php echo ($current_page == 'products.php') ? 'text-orange-500 border-b-2 border-orange-500' :
                    'text-black hover:text-orange-500'; ?>">Products</a>

                <a href="manufacturers.php" class="flex-1 text-center py-2.5 text-sm font-semibold
                <?php echo ($current_page == 'manufacturers.php') ? 'text-orange-500 border-b-2 border-orange-500' :
                    'text-black hover:text-orange-500'; ?>">Manufacturers</a>


                <a href="worldwide.php" class="flex-1 text-center py-2.5 text-sm font-semibold
                <?php echo ($current_page == 'worldwide.php') ? 'text-orange-500 border-b-2 border-orange-500' :
                    'text-black hover:text-orange-500'; ?>">Worldwide</a>
            </div>
        </nav>
    </header>

    <main>
        <!--  HERO BANNER -->
        <section class="max-w-9xl mx-auto px-4 mt-4">
            <section class="relative overflow-hidden text-white mx-4 mt-4 rounded-3xl"
                style="background-image: url('image/camera6.jpg'); background-position: left;">
                <div class="blob w-40 h-40 bg-yellow-300 top-0 -right-10"></div>
                <div class="blob w-32 h-32 bg-pink-400 bottom-0 -left-10"></div>

                <div class="relative p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="inline-flex items-center gap-2 glass rounded-full px-3 py-1">
                            <span class="w-2 h-2 bg-green-300 rounded-full pulse-ring"></span>
                            <span class="text-xs font-medium">🔥 HOT DEALS</span>
                        </div>
                        <a href="product_create.php" class="glass rounded-full w-9 h-9 flex items-center justify-center text-black hover:bg-white/20 transition">
                            <i class="fas fa-plus text-sm"></i>
                        </a>
                    </div>

                    <h2 class="text-2xl font-bold mb-1">Discover Products</h2>
                    <p class="text-sm text-orange-100 mb-4">
                        <?php echo $total_products; ?> products available from verified suppliers
                    </p>

                    <!-- Mini Stats -->
                    <div class="grid grid-cols-3 gap-2">
                        <div class="glass rounded-xl p-2 text-center">
                            <p class="text-xs text-orange-100">Free</p>
                            <p class="text-sm font-bold">Shipping</p>
                        </div>
                        <div class="glass rounded-xl p-2 text-center">
                            <p class="text-xs text-orange-100">Secure</p>
                            <p class="text-sm font-bold">Payment</p>
                        </div>
                        <div class="glass rounded-xl p-2 text-center">
                            <p class="text-xs text-orange-100">24/7</p>
                            <p class="text-sm font-bold">Support</p>
                        </div>
                    </div>
                </div>

                <!-- Wave Bottom -->
                <svg class="absolute bottom-0 left-0 right-0" viewBox="0 0 1440 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 40L60 36C120 32 240 24 360 22C480 20 600 24 720 26C840 28 960 28 1080 26C1200 24 1320 20 1380 18L1440 16V40H0Z" fill="#f97316" />
                </svg>
            </section>
            <!--  CATEGORY PILLS -->
            <section class="px-4 py-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-white-800">Categories</h3>
                    <span class="text-xs text-white"><?php echo count($categories) - 1; ?> types</span>
                </div>

                <div class="flex gap-2 overflow-x-auto hide-scroll pb-2">
                    <?php foreach ($categories as $key => $cat):
                        $is_active = ($category === $key);
                        $active_class = $is_active
                            ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-200'
                            : 'bg-white text-gray-700 border border-gray-200';
                    ?>
                        <a href="products.php?category=<?php echo urlencode($key); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $sort !== 'newest' ? '&sort=' . urlencode($sort) : ''; ?>"
                            class="category-pill <?php echo $active_class; ?> text-xs px-4 py-2 rounded-full whitespace-nowrap font-semibold flex items-center gap-2">
                            <i class="fas <?php echo $cat['icon']; ?> text-xs"></i>
                            <?php echo $cat['label']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>


            <!--  SORT BAR -->
            <section class="px-4 pb-3">
                <div class="bg-white rounded-2xl p-3 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-sort text-orange-500 text-sm"></i>
                        <span class="text-xs font-semibold text-gray-700">Sort by:</span>
                    </div>
                    <div class="flex gap-2">
                        <?php
                        $sorts = [
                            'newest' => 'Newest',
                            'price_low' => 'Price ↑',
                            'price_high' => 'Price ↓',
                            'name' => 'Name',
                        ];
                        foreach ($sorts as $key => $label):
                            $is_active = ($sort === $key);
                            $active_class = $is_active
                                ? 'bg-orange-500 text-white'
                                : 'bg-gray-100 text-gray-600';
                        ?>
                            <a href="products.php?sort=<?php echo urlencode($key); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $category !== 'all' ? '&category=' . urlencode($category) : ''; ?>"
                                class="<?php echo $active_class; ?> text-xs px-3 py-1.5 rounded-full font-semibold whitespace-nowrap">
                                <?php echo $label; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>


            <!--  PRODUCTS GRID -->
            <section class="px-4 pb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-white-800">
                            <?php echo $category === 'all' ? '🔥 All Products' : '📦 ' . htmlspecialchars($category); ?>
                        </h3>
                        <p class="text-xs text-white-500 mt-0.5">
                            Showing <?php echo $total_products; ?> results
                        </p>
                    </div>
                    <?php if (!empty($search)): ?>
                        <span class="text-xs glass text-orange-600 px-3 py-1 rounded-full font-semibold">
                            "<?php echo htmlspecialchars($search); ?>"
                        </span>
                    <?php endif; ?>
                </div>

                <?php if ($result && $result->num_rows > 0): ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                        <?php
                        $index = 0;
                        while ($p = $result->fetch_assoc()):
                            $index++;
                            $badges = ['NEW', 'HOT', 'SALE', 'TOP'];
                            $badge = $badges[$index % 4];
                            $badge_color = $badge === 'HOT'
                                ? 'from-red-500 to-blue-500'
                                : ($badge === 'SALE'
                                    ? 'from-orange-500 to-emerald-500'
                                    : ($badge === 'TOP'
                                        ? 'from-purple-500 to-indigo-500'
                                        : 'from-orange-500 to-red-500'));
                        ?>
                            <div class="product-card glass-clear rounded-2xl shadow-sm overflow-hidden hover-lift border border-orange-500 flex flex-col ">
                                <!-- Image -->
                                <div class="relative overflow-hidden">
                                    <?php if ($p['image']): ?>
                                        <img src="image/<?php echo htmlspecialchars($p['image']); ?>"
                                            class="product-img w-full aspect-square object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-36 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-400">
                                            <i class="fas fa-image text-3xl"></i>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Badge -->
                                    <span class="absolute top-2 left-2 bg-gradient-to-r <?php echo $badge_color; ?> text-white text-xs px-2 py-0.5 rounded-full font-bold shadow-lg <?php echo $badge === 'HOT' ? 'badge-hot' : ''; ?>">
                                        <?php echo $badge; ?>
                                    </span>

                                    <!-- Wishlist -->
                                    <button class="absolute top-2 right-2 w-7 h-7 bg-white/90 backdrop-blur rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 transition">
                                        <i class="far fa-heart text-xs"></i>
                                    </button>

                                    <!-- Manufacturer Badge -->
                                    <?php if ($p['m_country']): ?>
                                        <span class="absolute bottom-2 left-2 bg-black/60 backdrop-blur text-white text-xs px-2 py-0.5 rounded-full">
                                            <?php echo $p['m_flag'] . ' ' . htmlspecialchars($p['m_country']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Content -->
                                <div class="p-3">
                                    <h3 class="text-xs font-semibold text-white truncate mb-1">
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

                                    <!-- Actions -->
                                    <div class="flex gap-1">
                                        <a href="cart_add.php?id=<?php echo $p['id']; ?>"
                                            class="flex-1 text-center text-xs bg-gradient-to-r from-orange-500 to-orange-600 text-white py-2 rounded-xl font-semibold hover:from-orange-600 hover:to-orange-700 transition shadow-sm shadow-orange-200">
                                            <i class="fas fa-cart-plus mr-1"></i> Add
                                        </a>
                                        <?php
                                        $is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
                                        $is_owner = (isset($_SESSION['user_id']) && $p['seller_id'] == $_SESSION['user_id']);

                                        if ($is_admin || $is_owner):
                                        ?>
                                            <a href="product_edit.php?id=<?php echo $p['id']; ?>"
                                                class="w-8 h-8 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center hover:bg-blue-100 transition">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            <a href="product_delete.php?id=<?php echo $p['id']; ?>"
                                                onclick="return confirm('Are you sure?')"
                                                class="w-8 h-8 bg-red-50 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-100 transition">
                                                <i class="fas fa-trash text-xs"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>

                    <!-- Empty State -->
                    <div class="text-center py-16 bg-white rounded-3xl shadow-sm">
                        <div class="w-24 h-24 mx-auto mb-4 bg-blue-800 rounded-full flex items-center justify-center animate-float">
                            <i class="fas fa-search text-4xl text-white"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-700 mb-2">No Products Found</h2>
                        <p class="text-sm text-gray-500 mb-5 max-w-xs mx-auto">
                            <?php if (!empty($search)): ?>
                                No results for "<?php echo htmlspecialchars($search); ?>". Try a different keyword.
                            <?php else: ?>
                                No products in this category yet.
                            <?php endif; ?>
                        </p>
                        <a href="products.php" class="inline-block bg-blue-800 text-white font-semibold px-6 py-2.5 rounded-full shadow-lg shadow-black-200">
                            <i class="fas fa-redo mr-2"></i> Show All Products
                        </a>
                    </div>
                <?php endif; ?>
            </section>


            <!--  CTA SECTION -->
            <section class="px-4 pb-6">
                <div class="relative overflow-hidden glass from-slate-800 via-slate-900 to-orange-900 rounded-3xl p-6 text-black-800 text-center">
                    <div class="blob w-40 h-40 bg-blue-500 top-0 -right-10"></div>
                    <div class="blob w-32 h-32 bg-blue-500 bottom-0 -left-10"></div>

                    <div class="relative">
                        <div class="w-14 h-14 mx-auto mb-3 bg-blue/10 backdrop-blur rounded-2xl flex items-center justify-center">
                            <i class="fas fa-boxes text-2xl text-blue-800"></i>
                        </div>
                        <h2 class="text-lg font-bold mb-1">Can't find what you need?</h2>
                        <p class="text-xs text-black-300 mb-4">
                            Add your own products to the marketplace
                        </p>
                        <a href="product_create.php" class="inline-block glass from-blue-200 to-blue-300 text-orange-500 font-bold px-6 py-2.5 rounded-full shadow-lg shadow-blue-500/30 hover:shadow-xl transition">
                            <i class="fas fa-plus mr-2"></i> Add New Product
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