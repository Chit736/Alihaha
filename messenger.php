<?php
session_start();
include 'connection.php';
include 'function.php';

$current_page = basename($_SERVER['PHP_SELF']);

//  Search & Filter

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';


//  Conversations Data (DB မရှိသေးရင် static)

$conversations = [
    [
        'id' => 1,
        'name' => 'RZX Trading Co.',
        'msg' => 'Thank you for your inquiry. We can offer you a bulk discount...',
        'time' => '2m',
        'unread' => 2,
        'online' => true,
        'verified' => true,
        'flag' => '🇨🇳',
        'country' => 'China',
        'gradient' => 'from-red-500 to-yellow-500',
        'avatar' => 'R',
        'label' => 'Supplier'
    ],
    [
        'id' => 2,
        'name' => 'Seoul Beauty Ltd.',
        'msg' => 'Here are the samples you requested. Let me know your thoughts.',
        'time' => '15m',
        'unread' => 0,
        'online' => true,
        'verified' => true,
        'flag' => '🇰🇷',
        'country' => 'S. Korea',
        'gradient' => 'from-pink-400 to-purple-500',
        'avatar' => 'S',
        'label' => 'Supplier'
    ],
    [
        'id' => 3,
        'name' => 'HK Toys International',
        'msg' => 'Your order has been shipped! Tracking: HK12345678',
        'time' => '1h',
        'unread' => 1,
        'online' => false,
        'verified' => true,
        'flag' => '🇭🇰',
        'country' => 'Hong Kong',
        'gradient' => 'from-blue-400 to-indigo-600',
        'avatar' => 'H',
        'label' => 'Supplier'
    ],
    [
        'id' => 4,
        'name' => 'Shenzhen Industrial',
        'msg' => 'Can you confirm the quantity? We need to prepare the shipment.',
        'time' => '3h',
        'unread' => 0,
        'online' => false,
        'verified' => true,
        'flag' => '🇨🇳',
        'country' => 'China',
        'gradient' => 'from-orange-400 to-red-600',
        'avatar' => 'S',
        'label' => 'Supplier'
    ],
    [
        'id' => 5,
        'name' => 'Vietnam Cashew Export',
        'msg' => 'New harvest is available now. Premium quality.',
        'time' => '1d',
        'unread' => 0,
        'online' => true,
        'verified' => true,
        'flag' => '🇻🇳',
        'country' => 'Vietnam',
        'gradient' => 'from-green-400 to-emerald-600',
        'avatar' => 'V',
        'label' => 'Supplier'
    ],
    [
        'id' => 6,
        'name' => 'Mumbai Medical Supplies',
        'msg' => 'We are ISO 9001 certified. Let me send you our catalog.',
        'time' => '2d',
        'unread' => 0,
        'online' => false,
        'verified' => false,
        'flag' => '🇮🇳',
        'country' => 'India',
        'gradient' => 'from-teal-400 to-cyan-600',
        'avatar' => 'M',
        'label' => 'Supplier'
    ],
    [
        'id' => 7,
        'name' => 'Yangon Export House',
        'msg' => 'We have fresh agricultural products available.',
        'time' => '3d',
        'unread' => 0,
        'online' => true,
        'verified' => true,
        'flag' => '🇲🇲',
        'country' => 'Myanmar',
        'gradient' => 'from-yellow-400 to-orange-500',
        'avatar' => 'Y',
        'label' => 'Supplier'
    ],
];


//  Filter Logic

$filtered = array_filter($conversations, function ($c) use ($search, $filter) {
    // Search filter
    if (!empty($search)) {
        $match = stripos($c['name'], $search) !== false
            || stripos($c['msg'], $search) !== false;
        if (!$match) return false;
    }

    // Type filter
    if ($filter === 'unread' && $c['unread'] == 0) return false;
    if ($filter === 'online' && !$c['online']) return false;
    if ($filter === 'verified' && !$c['verified']) return false;

    return true;
});

$total_unread = array_sum(array_column($conversations, 'unread'));
$total_online = count(array_filter($conversations, fn($c) => $c['online']));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alihaha - Messenger</title>
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
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .slide-in {
            animation: slide-in 0.4s ease-out;
        }

        /*  Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        /*  Conversation Hover */
        .conv-item {
            transition: all 0.3s ease;
        }

        .conv-item:hover {
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

        /*  Decorative Blob */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.35;
            pointer-events: none;
        }

        /*  Unread Badge Pulse */
        .unread-badge {
            animation: pulse-ring 1.5s ease-in-out infinite;
        }

        /*  Online Dot */
        .online-dot {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: pulse-online 2s infinite;
        }

        @keyframes pulse-online {
            0% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(34, 197, 94, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }

        /*  Avatar Ring */
        .avatar-ring {
            position: relative;
        }

        .avatar-ring::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f97316, #fb923c);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .conv-item:hover .avatar-ring::before {
            opacity: 1;
        }

        /*  Typing Indicator */
        @keyframes typing {

            0%,
            60%,
            100% {
                transform: translateY(0);
            }

            30% {
                transform: translateY(-4px);
            }
        }

        .typing-dot {
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
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


    
    <!--  HERO STATS -->
   
     <section class="relative overflow-hidden text-white mx-4 mt-4 rounded-3xl"
            style="background-image: url('image/hello image.jpg'); background-size: contain; background-position: left;">
        <div class="blob w-32 h-32 bg-orange-500 top-0 -right-10"></div>
        <div class="blob w-24 h-24 bg-pink-500 bottom-0 -left-10"></div>

        <div class="relative p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg text-black font-bold">Your Inbox</h2>
                    <p class="text-xs text-orange-500 mt-0.5">Stay connected with suppliers</p>
                </div>
                <div class="w-12 h-12 glass rounded-2xl flex items-center justify-center animate-float">
                    <i class="fas fa-inbox text-orange-400 text-xl"></i>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-3 gap-2">
                <div class="rounded-xl p-3 text-center">
                    <p class="text-orange-400 font-bold text-lg"><?php echo count($conversations); ?></p>
                    <p class="text-xs text-black">Total</p>
                </div>
                <div class="rounded-xl p-3 text-center">
                    <p class="text-orange-400 font-bold text-lg"><?php echo $total_unread; ?></p>
                    <p class="text-xs text-black">Unread</p>
                </div>
                <div class="rounded-xl p-3 text-center">
                    <p class="text-green-400 font-bold text-lg"><?php echo $total_online; ?></p>
                    <p class="text-xs text-black">Online</p>
                </div>
            </div>
        </div>
    </section>

   
    <!--  FILTER TABS -->
    
    <section class="px-4 py-4">
        <div class="flex gap-2 overflow-x-auto hide-scroll">
            <?php
            $filters = [
                'all' => ['label' => 'All', 'icon' => 'fa-comments', 'count' => count($conversations)],
                'unread' => ['label' => 'Unread', 'icon' => 'fa-envelope', 'count' => $total_unread],
                'online' => ['label' => 'Online', 'icon' => 'fa-circle', 'count' => $total_online],
                'verified' => ['label' => 'Verified', 'icon' => 'fa-check-circle', 'count' => count(array_filter($conversations, fn($c) => $c['verified']))],
            ];
            foreach ($filters as $key => $f):
                $is_active = ($filter === $key);
                $active_class = $is_active
                    ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-lg shadow-orange-200'
                    : 'bg-white text-gray-700 border border-gray-200';
            ?>
                <a href="messenger.php?filter=<?php echo urlencode($key); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                    class="<?php echo $active_class; ?> text-xs px-4 py-2 rounded-full whitespace-nowrap font-semibold flex items-center gap-2">
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

   
    <!--  CONVERSATIONS LIST -->
    
    <section class="px-4 pb-6 space-y-2">
        <?php if (count($filtered) > 0): ?>
            <?php foreach ($filtered as $c): ?>
                <a href="chat.php?id=<?php echo $c['id']; ?>"
                    class="conv-item slide-in block glass rounded-2xl p-3 shadow-sm border border-gray-100 hover:border-orange-200">
                    <div class="flex items-center gap-3">
                        <!-- Avatar -->
                        <div class="avatar-ring relative flex-shrink-0">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br <?php echo $c['gradient']; ?> 
                                        flex items-center justify-center text-white font-bold text-lg shadow-lg relative z-10">
                                <?php echo $c['avatar']; ?>
                            </div>
                            <!-- Online Dot -->
                            <?php if ($c['online']): ?>
                                <span class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2 border-white rounded-full online-dot z-20"></span>
                            <?php endif; ?>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <h3 class="font-bold text-gray-800 text-sm truncate">
                                    <?php echo htmlspecialchars($c['name']); ?>
                                </h3>
                                <?php if ($c['verified']): ?>
                                    <i class="fas fa-check-circle text-blue-500 text-xs flex-shrink-0"></i>
                                <?php endif; ?>
                                <span class="text-xs text-gray-400"><?php echo $c['flag']; ?></span>
                            </div>

                            <!-- Message Preview -->
                            <p class="text-xs text-gray-600 truncate mb-1">
                                <?php echo htmlspecialchars($c['msg']); ?>
                            </p>

                            <!-- Label -->
                            <div class="flex items-center gap-2">
                                <span class="text-xs bg-orange-50 text-orange-600 px-2 py-0.5 rounded-full font-medium">
                                    <?php echo $c['label']; ?>
                                </span>
                                <span class="text-xs text-black">
                                    <?php echo htmlspecialchars($c['country']); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Right Side -->
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <span class="text-xs text-gray-400 font-medium"><?php echo $c['time']; ?></span>

                            <?php if ($c['unread'] > 0): ?>
                                <span class="unread-badge bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs w-6 h-6 rounded-full flex items-center justify-center font-bold shadow-lg shadow-orange-200">
                                    <?php echo $c['unread']; ?>
                                </span>
                            <?php else: ?>
                                <i class="fas fa-check-double text-blue-400 text-xs"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-16 bg-white rounded-3xl shadow-sm">
                <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center animate-float">
                    <i class="fas fa-comment-slash text-4xl text-orange-500"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-700 mb-2">No Conversations</h2>
                <p class="text-sm text-gray-500 mb-5 max-w-xs mx-auto">
                    <?php if (!empty($search)): ?>
                        No results for "<?php echo htmlspecialchars($search); ?>"
                    <?php else: ?>
                        Start connecting with suppliers to see conversations here.
                    <?php endif; ?>
                </p>
                <a href="messenger.php" class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold px-6 py-2.5 rounded-full shadow-lg shadow-orange-200">
                    <i class="fas fa-redo mr-2"></i> Show All
                </a>
            </div>
        <?php endif; ?>
    </section>

    <!--  QUICK ACTIONS -->
   
    <section class="px-4 pb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-3">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="manufacturers.php" class="glass rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3 hover:border-orange-200 transition">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                    <i class="fas fa-building text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-800">Find Suppliers</p>
                    <p class="text-xs text-white">Browse all</p>
                </div>
            </a>
            <a href="products.php" class="glass rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3 hover:border-orange-200 transition">
                <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-xl flex items-center justify-center">
                    <i class="fas fa-box text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-800">Browse Products</p>
                    <p class="text-xs text-white">Shop now</p>
                </div>
            </a>
        </div>
    </section>

    
    <!--  CTA BANNER -->
  
    <section class="px-4 pb-6">
        <div class="relative overflow-hidden glass rounded-3xl p-6 text-white">
            <div class="blob w-32 h-32 bg-yellow-300 top-0 -right-10"></div>
            <div class="blob w-24 h-24 bg-pink-400 bottom-0 -left-10"></div>

            <div class="relative flex items-center gap-4">
                <div class="w-14 h-14 bg-green-500 backdrop-blur rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-headset text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-sm mb-1">Need Help?</h3>
                    <p class="text-xs text-orange-100 mb-2">Our support team is online 24/7</p>
                    <button class="bg-white text-green-500 text-xs font-bold px-4 py-1.5 rounded-full shadow-lg">
                        Chat Now →
                    </button>
                </div>
            </div>
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