<?php
session_start();
include 'connection.php';
include 'function.php';

// လက်ရှိရောက်နေတဲ့ page နာမည်ကို ယူမယ် 

$current_page = basename($_SERVER['PHP_SELF']);


//  Search, Filter, Sort

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'rating';


//  SQL Query Dynamic

$sql = "SELECT * FROM manufacturers WHERE 1=1";
$params = [];
$types = "";

// Search
if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR country LIKE ? OR description LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

// Filter
if ($filter === 'verified') {
    $sql .= " AND is_verified = 1";
} elseif ($filter !== 'all' && $filter !== 'verified') {
    $sql .= " AND country = ?";
    $params[] = $filter;
    $types .= "s";
}

// Sort
switch ($sort) {
    case 'rating':
        $sql .= " ORDER BY rating DESC, is_verified DESC";
        break;
    case 'products':
        $sql .= " ORDER BY products_count DESC";
        break;
    case 'response':
        $sql .= " ORDER BY response_rate DESC";
        break;
    case 'newest':
        $sql .= " ORDER BY id DESC";
        break;
    default:
        $sql .= " ORDER BY is_verified DESC, rating DESC";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$total_manufacturers = $result->num_rows;


//  Country list (for filters)

$countries = [
    'all' => ['label' => 'All', 'flag' => '🌐'],
    'verified' => ['label' => 'Verified', 'flag' => '✓'],
    'China' => ['label' => 'China', 'flag' => '🇨🇳'],
    'Myanmar' => ['label' => 'Myanmar', 'flag' => '🇲🇲'],
    'South Korea' => ['label' => 'S. Korea', 'flag' => '🇰🇷'],
    'Hong Kong' => ['label' => 'Hong Kong', 'flag' => '🇭🇰'],
    'India' => ['label' => 'India', 'flag' => '🇮🇳'],
    'Vietnam' => ['label' => 'Vietnam', 'flag' => '🇻🇳'],
];

//  Industry Hubs

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
    <title>Alihaha - Top Manufacturers</title>
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
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

        /*  Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        /*  Manufacturer Card Hover */
        .m-card {
            transition: all 0.3s ease;
        }

        .m-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(249, 115, 22, 0.15);
            border-color: #fdba74;
        }

        /*  Logo Hover */
        .m-card:hover .m-logo {
            transform: scale(1.05) rotate(-3deg);
        }

        .m-logo {
            transition: transform 0.3s ease;
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

        /*  Verified Ring */
        .verified-ring {
            position: relative;
        }


        /*  Response Bar */
        .response-bar {
            background: linear-gradient(90deg, #22c55e, #16a34a);
            transition: width 0.5s ease;
        }

        /*  Star Rating */
        .star-filled {
            color: #fbbf24;
        }

        .star-empty {
            color: #d1d5db;
        }

        /*  Hub Card */
        .hub-card {
            transition: all 0.3s ease;
        }

        .hub-card:hover {
            transform: translateY(-4px) scale(1.02);
        }
    </style>
</head>

<body class="bg-blue-800 font-sans pb-16">

   
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
        
        <!--  HERO SECTION -->
       
        <section class="relative overflow-hidden text-white mx-4 mt-4 rounded-3xl"
            style="background-image: url('image/Nike industry.webp'); background-position: left;">

            <div class="blob w-40 h-40 bg-orange-500 top-0 -right-10"></div>
            <div class="blob w-32 h-32 bg-blue-500 bottom-0 -left-10"></div>
            <div class="blob w-24 h-24 bg-pink-500 top-1/2 left-1/2"></div>

            <div class="relative p-6">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 glass rounded-full px-3 py-1.5 mb-4">
                    <span class="w-2 h-2 bg-green-400 rounded-full pulse-ring"></span>
                    <span class="text-xs font-medium">🏭 VERIFIED SUPPLIERS</span>
                </div>

                <!-- Title -->
                <h1 class="text-3xl font-bold leading-tight mb-3">
                    Top <span class="gradient-text">Manufacturers</span>
                    <br>worldwide
                </h1>
                <p class="text-sm text-gray-300 mb-5 max-w-md">
                    Connect with verified suppliers. Trade Assurance, quality guaranteed.
                </p>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="glass rounded-xl p-2.5 text-center">
                        <p class="text-orange-400 font-bold text-base"><?php echo $total_manufacturers; ?></p>
                        <p class="text-xs text-gray-300">Suppliers</p>
                    </div>
                    <div class="glass rounded-xl p-2.5 text-center">
                        <p class="text-green-400 font-bold text-base">150+</p>
                        <p class="text-xs text-gray-300">Countries</p>
                    </div>
                    <div class="glass rounded-xl p-2.5 text-center">
                        <p class="text-blue-400 font-bold text-base">98%</p>
                        <p class="text-xs text-gray-300">Response</p>
                    </div>
                </div>

                <!-- Search Bar -->
                <form method="GET" action="manufacturers.php" class="bg-white/10 backdrop-blur rounded-2xl px-4 py-2.5 flex items-center gap-2">
                    <i class="fas fa-search text-white/60"></i>
                    <input type="text" name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Search manufacturers..."
                        class="bg-transparent text-sm text-white placeholder-white/50 focus:outline-none w-full">
                    <?php if ($filter !== 'all'): ?>
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                    <?php endif; ?>
                    <?php if ($sort !== 'rating'): ?>
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    <?php endif; ?>
                    <?php if (!empty($search)): ?>
                        <a href="manufacturers.php" class="text-white/60 hover:text-white">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    <?php else: ?>
                        <button type="submit" class="text-orange-400">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Wave Bottom -->
            <svg class="absolute bottom-0 left-0 right-0" viewBox="0 0 1440 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 40L60 36C120 32 240 24 360 22C480 20 600 24 720 26C840 28 960 28 1080 26C1200 24 1320 20 1380 18L1440 16V40H0Z" fill="#54f519" />
            </svg>
        </section>

        
        <!--  GLOBAL INDUSTRY HUBS -->
        
        <section class="pt-6">
            <div class="px-4 flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-sm font-bold text-gray-800">
                        <i class="fas fa-globe-asia text-orange-500 mr-1"></i> Global Industry Hubs
                    </h2>
                    <p class="text-xs text-white mt-0.5">Trending industries by region</p>
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

       
        <!--  COUNTRY FILTER -->
    
        <section class="px-4 pt-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-gray-800">
                    <i class="fas fa-filter text-orange-500 mr-1"></i> Filter by Country
                </h2>
            </div>

            <div class="flex gap-2 overflow-x-auto hide-scroll pb-2">
                <?php foreach ($countries as $key => $c):
                    $is_active = ($filter === $key);
                    $active_class = $is_active
                        ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-200'
                        : 'bg-white text-gray-700 border border-gray-200';
                ?>
                    <a href="manufacturers.php?filter=<?php echo urlencode($key); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $sort !== 'rating' ? '&sort=' . urlencode($sort) : ''; ?>"
                        class="<?php echo $active_class; ?> text-xs px-4 py-2 rounded-full whitespace-nowrap font-semibold flex items-center gap-2">
                        <span><?php echo $c['flag']; ?></span>
                        <?php echo $c['label']; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!--  SORT BAR -->
        
        <section class="px-4 pt-3">
            <div class="bg-white rounded-2xl p-3 shadow-sm border border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-sort text-orange-500 text-sm"></i>
                    <span class="text-xs font-semibold text-gray-700">Sort:</span>
                </div>
                <div class="flex gap-2">
                    <?php
                    $sorts = [
                        'rating' => 'Rating',
                        'products' => 'Products',
                        'response' => 'Response',
                        'newest' => 'Newest',
                    ];
                    foreach ($sorts as $key => $label):
                        $is_active = ($sort === $key);
                        $active_class = $is_active
                            ? 'bg-orange-500 text-white'
                            : 'bg-gray-100 text-gray-600';
                    ?>
                        <a href="manufacturers.php?sort=<?php echo urlencode($key); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $filter !== 'all' ? '&filter=' . urlencode($filter) : ''; ?>"
                            class="<?php echo $active_class; ?> text-xs px-3 py-1.5 rounded-full font-semibold whitespace-nowrap">
                            <?php echo $label; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

       
        <!--  MANUFACTURERS LIST -->
        
        <section class="px-4 pt-5 pb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-800">
                        <?php
                        if ($filter === 'all') echo '🏭 All Manufacturers';
                        elseif ($filter === 'verified') echo '✓ Verified Manufacturers';
                        else echo $countries[$filter]['flag'] . ' ' . htmlspecialchars($filter) . ' Manufacturers';
                        ?>
                    </h2>
                    <p class="text-xs text-white mt-0.5">
                        <?php echo $total_manufacturers; ?> results found
                    </p>
                </div>
                <?php if (!empty($search)): ?>
                    <span class="text-xs bg-orange-100 text-orange-600 px-3 py-1 rounded-full font-semibold">
                        "<?php echo htmlspecialchars($search); ?>"
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($result && $result->num_rows > 0): ?>
                <div class="space-y-4">
                    <?php while ($m = $result->fetch_assoc()): ?>
                        <div class="m-card slide-in glass rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <!-- Top Gradient Banner -->
                            <div class="h-20 bg-gradient-to-br from-green-400 via-green-500 to-green-500 relative">
                                <div class="absolute inset-0 shimmer"></div>

                                <!-- Country Flag -->
                                <span class="absolute top-3 right-3 bg-white/20 backdrop-blur text-white text-xs px-2.5 py-1 rounded-full font-medium">
                                    <?php echo $m['flag'] . ' ' . htmlspecialchars($m['country']); ?>
                                </span>

                                <!-- Verified Badge -->
                                <?php if ($m['is_verified']): ?>
                                    <span class="absolute top-3 left-3 bg-white text-blue-600 text-xs px-2.5 py-1 rounded-full font-bold flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i> Verified
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Logo (Overlapping) -->
                            <div class="px-4 -mt-10 relative">
                                <div class="flex items-end gap-3">
                                    <!-- Logo -->
                                    <div class="flex-shrink-0">
                                        <div class="w-16 h-16 rounded-2xl bg-white p-1 shadow-md flex items-center justify-center overflow-hidden">
                                            <?php
                                            // ပုံလမ်းကြောင်းကို သတ်မှတ်မယ်
                                            $logo_path = 'logos/' . $m['logo'];
                                            // ဖိုင်တကယ်ရှိမရှိ စစ်မယ်
                                            $has_logo = !empty($m['logo']) && file_exists($logo_path);
                                            ?>

                                            <?php if ($has_logo): ?>
                                                <!-- ပုံရှိရင် ပုံပြ -->
                                                <img src="<?php echo htmlspecialchars($logo_path); ?>"
                                                    alt="<?php echo htmlspecialchars($m['name']); ?>"
                                                    class="w-full h-full object-contain rounded-xl">
                                            <?php else: ?>
                                                <!-- ပုံမရှိရင် စာလုံးပြ (flex ထည့်ထားလို့ အလယ်ကျမယ်) -->
                                                <div class="w-full h-full rounded-xl bg-gradient-to-br from-orange-400 to-orange-500 flex items-center justify-center text-white font-bold text-2xl">
                                                    <?php echo strtoupper(substr($m['name'], 0, 1)); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <!-- Response Rate -->
                                    <div class="flex-1 pb-2">
                                        <div class="flex items-center gap-2 mb-1">
                                            <i class="fas fa-bolt text-yellow-500 text-xs"></i>
                                            <span class="text-xs text-black font-medium">Response Rate</span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="response-bar h-1.5 rounded-full"
                                                style="width: <?php echo $m['response_rate']; ?>%"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5"><?php echo $m['response_rate']; ?>%</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-4">
                                <!-- Name + Verified -->
                                <div class="flex items-center gap-1.5 mb-1">
                                    <h3 class="font-bold text-gray-800 text-base">
                                        <?php echo htmlspecialchars($m['name']); ?>
                                    </h3>
                                    <?php if ($m['is_verified']): ?>
                                        <i class="fas fa-check-circle text-blue-500 text-sm"></i>
                                    <?php endif; ?>
                                </div>

                                <!-- Rating + Products -->
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="flex items-center gap-1">
                                        <div class="flex text-sm">
                                            <?php
                                            $rating = floatval($m['rating']);
                                            for ($i = 1; $i <= 5; $i++):
                                                if ($i <= floor($rating)): ?>
                                                    <i class="fas fa-star star-filled"></i>
                                                <?php elseif ($i - 0.5 <= $rating): ?>
                                                    <i class="fas fa-star-half-alt star-filled"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star star-empty"></i>
                                            <?php endif;
                                            endfor; ?>
                                        </div>
                                        <span class="text-xs font-bold text-white ml-1">
                                            <?php echo number_format($m['rating'], 1); ?>
                                        </span>
                                    </div>
                                    <span class="text-gray-300">•</span>
                                    <span class="text-xs text-white">
                                        <i class="fas fa-box text-orange-500 mr-1"></i>
                                        <?php echo $m['products_count']; ?> products
                                    </span>
                                </div>

                                <!-- Description -->
                                <?php if ($m['description']): ?>
                                    <p class="text-xs text-gray-600 mb-3 leading-relaxed">
                                        <?php echo htmlspecialchars($m['description']); ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Badges -->
                                <div class="flex gap-1.5 mb-3 flex-wrap">
                                    <?php if ($m['is_verified']): ?>
                                        <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-full font-medium flex items-center gap-1">
                                            <i class="fas fa-shield-alt text-xs"></i> Trade Assurance
                                        </span>
                                    <?php endif; ?>
                                    <span class="text-xs bg-green-50 text-green-600 px-2 py-1 rounded-full font-medium flex items-center gap-1">
                                        <i class="fas fa-flask text-xs"></i> Sample Available
                                    </span>
                                    <span class="text-xs bg-orange-50 text-orange-600 px-2 py-1 rounded-full font-medium flex items-center gap-1">
                                        <i class="fas fa-truck text-xs"></i> Global Shipping
                                    </span>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="messenger.php"
                                        class="flex-1 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 text-gray-700 text-sm font-semibold py-2.5 rounded-2xl text-center hover:from-gray-100 hover:to-gray-200 transition">
                                        <i class="far fa-comment mr-1"></i> Contact
                                    </a>
                                    <a href="products.php?manufacturer=<?php echo $m['id']; ?>"
                                        class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-sm font-semibold py-2.5 rounded-2xl text-center shadow-lg shadow-orange-200 hover:from-orange-600 hover:to-orange-700 transition">
                                        <i class="fas fa-box mr-1"></i> View Products
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-16 bg-white rounded-3xl shadow-sm">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center animate-float">
                        <i class="fas fa-building text-4xl text-orange-500"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-700 mb-2">No Manufacturers Found</h2>
                    <p class="text-sm text-gray-500 mb-5 max-w-xs mx-auto">
                        <?php if (!empty($search)): ?>
                            No results for "<?php echo htmlspecialchars($search); ?>". Try a different keyword.
                        <?php else: ?>
                            No manufacturers in this country yet.
                        <?php endif; ?>
                    </p>
                    <a href="manufacturers.php" class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold px-6 py-2.5 rounded-full shadow-lg shadow-orange-200">
                        <i class="fas fa-redo mr-2"></i> Show All Manufacturers
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <!--  WHY CHOOSE US -->
      
        <section class="px-4 pb-6">
            <div class="glass rounded-3xl p-5 shadow-sm border border-gray-100">
                <div class="text-center mb-5">
                    <h2 class="text-base font-bold text-gray-800 mb-1">Why Buy from Verified Suppliers?</h2>
                    <p class="text-xs text-white">Trusted by millions of businesses worldwide</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-shield-alt text-blue-600"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-800">Trade Assurance</p>
                        <p class="text-xs text-white mt-0.5">Protected payments</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-medal text-green-600"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-800">Quality Assured</p>
                        <p class="text-xs text-white mt-0.5">Inspected products</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-headset text-purple-600"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-800">24/7 Support</p>
                        <p class="text-xs text-white mt-0.5">Always here</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-shipping-fast text-orange-600"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-800">Fast Shipping</p>
                        <p class="text-xs text-white mt-0.5">Global delivery</p>
                    </div>
                </div>
            </div>
        </section>

        
        <!--  CTA BANNER -->
   
        <section class="px-4 pb-6">
            <div class="relative overflow-hidden glass rounded-3xl p-6 text-white gradient-animate">
                <div class="blob w-32 h-32 bg-yellow-300 top-0 -right-10"></div>
                <div class="blob w-24 h-24 bg-pink-400 bottom-0 -left-10"></div>

                <div class="relative flex items-center gap-4">
                    <div class="w-14 h-14 bg-green-500 backdrop-blur rounded-2xl flex items-center justify-center flex-shrink-0 animate-float">
                        <i class="fas fa-handshake text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-sm mb-1">Become a Supplier</h3>
                        <p class="text-xs text-orange-100 mb-2">Join 100K+ manufacturers worldwide</p>
                        <button class="bg-white text-green-500 text-xs font-bold px-4 py-1.5 rounded-full shadow-lg">
                            Get Started →
                        </button>
                    </div>
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