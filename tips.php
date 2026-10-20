<?php
session_start();
include 'connection.php';
include 'function.php';

$current_page = basename($_SERVER['PHP_SELF']);

$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);


//  Data တွေကို ပြင်ဆင်ထားပါတယ်

$trending_topics = [
    [
        'title' => 'Global new products',
        'count' => '779',
        'emoji' => '🌍',
        'bg_image' => 'image/clothing company.webp', // ပုံလမ်းကြောင်း
        'tag' => '#Selected'
    ],
    [
        'title' => 'High-quality finds',
        'count' => '8.6K',
        'emoji' => '⭐',
        'bg_image' => 'image/solar company.jpeg',
        'tag' => '#Find'
    ],
    [
        'title' => 'Trending 2025',
        'count' => '2.1K',
        'emoji' => '🔥',
        'bg_image' => 'image/skin care company.webp',
        'tag' => '#Hot'
    ],
    [
        'title' => 'Eco-friendly picks',
        'count' => '1.5K',
        'emoji' => '🌱',
        'bg_image' => 'image/camera company 2.webp',
        'tag' => '#Green'
    ],
];

//  Video အတွက် 'video_id' ကို ထည့်ပေးထားပါတယ် (YouTube ID)
$tips_videos = [
    [
        'title' => 'How to source products from China',
        'author' => 'Alihaha Academy',
        'views' => '12.5K',
        'duration' => '5:24',
        'video_id' => 'https://youtube.com/shorts/xMEPAYyZH7k?si=KFH9m8vpaS5WEFty', // ဒီနေရာမှာ သင့ YouTube Video ID ထည့်မှရတယ်
        'tag' => 'Tutorial'
    ],
    [
        'title' => '5 tips for negotiating with suppliers',
        'author' => 'Business Pro',
        'views' => '8.3K',
        'duration' => '3:45',
        'video_id' => 'https://youtube.com/shorts/ho0EhuO3RNs?si=wnFTibw-ZgPw5kDH', 
        'tag' => 'Tips'
    ],
    [
        'title' => 'How to spot fake suppliers',
        'author' => 'Trade Expert',
        'views' => '15.2K',
        'duration' => '7:12',
        'video_id' => 'https://youtube.com/shorts/RcHz051o-70?si=ErALfNn53YqhtuL2',
        'tag' => 'Safety'
    ],
    [
        'title' => 'Building your brand with AI',
        'author' => 'Alihaha Academy',
        'views' => '6.8K',
        'duration' => '4:30',
        'video_id' => 'https://youtube.com/shorts/9OkFD10Idew?si=-2ziBS-dUXU9MD2_',
        'tag' => 'AI'
    ],
];

$tips_categories = [
    ['icon' => 'fa-shopping-bag', 'label' => 'Sourcing', 'gradient' => 'from-orange-400 to-red-500', 'count' => '45'],
    ['icon' => 'fa-handshake', 'label' => 'Negotiation', 'gradient' => 'from-blue-400 to-indigo-500', 'count' => '28'],
    ['icon' => 'fa-truck', 'label' => 'Shipping', 'gradient' => 'from-green-400 to-emerald-500', 'count' => '32'],
    ['icon' => 'fa-chart-line', 'label' => 'Marketing', 'gradient' => 'from-purple-400 to-pink-500', 'count' => '56'],
];

$tips_list = [
    [
        'icon' => 'fa-lightbulb',
        'title' => 'Always request samples first',
        'desc' => 'Never order in bulk without testing samples from your supplier',
        'gradient' => 'from-yellow-400 to-orange-500',
        'category' => 'Sourcing'
    ],
    [
        'icon' => 'fa-file-contract',
        'title' => 'Get everything in writing',
        'desc' => 'Verbal agreements mean nothing. Always sign contracts.',
        'gradient' => 'from-blue-400 to-indigo-500',
        'category' => 'Legal'
    ],
    [
        'icon' => 'fa-search-dollar',
        'title' => 'Compare at least 3 suppliers',
        'desc' => 'Don\'t settle for the first quote. Shop around for better deals.',
        'gradient' => 'from-green-400 to-emerald-500',
        'category' => 'Sourcing'
    ],
    [
        'icon' => 'fa-shield-alt',
        'title' => 'Use Trade Assurance',
        'desc' => 'Protected payments keep your money safe until delivery.',
        'gradient' => 'from-red-400 to-pink-500',
        'category' => 'Safety'
    ],
    [
        'icon' => 'fa-calculator',
        'title' => 'Calculate total landed cost',
        'desc' => 'Include shipping, customs, and taxes in your budget.',
        'gradient' => 'from-purple-400 to-pink-500',
        'category' => 'Budget'
    ],
    [
        'icon' => 'fa-star',
        'title' => 'Check reviews and ratings',
        'desc' => 'Past buyers are your best source of honest feedback.',
        'gradient' => 'from-yellow-400 to-red-500',
        'category' => 'Trust'
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alihaha - Tips & Insights</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
        .bottom-nav-item.active { color: #f97316; }
        .bottom-nav-item.active i { color: #f97316; }

        /*  Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
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
        .tip-card {
            transition: all 0.3s ease;
        }
        .tip-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(249, 115, 22, 0.15);
        }

        /*  Topic Card Hover */
        .topic-card {
            transition: all 0.3s ease;
        }
        .topic-card:hover {
            transform: scale(1.03);
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

        /*  Category Pills */
        .cat-pill {
            transition: all 0.3s ease;
        }
        .cat-pill:hover {
            transform: translateY(-2px) scale(1.02);
        }

        /*  Tip List Icon Hover */
        .tip-list-item:hover .tip-icon {
            transform: scale(1.1) rotate(-5deg);
        }
        .tip-icon {
            transition: transform 0.3s ease;
        }

        /*  Pulse Dot */
        .live-dot {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: pulse-live 2s infinite;
        }
        @keyframes pulse-live {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
    </style>
</head>
<body class="bg-blue-800 font-sans pb-16">

    <!-- HEADER -->
    <header class="glass backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-gray-100">
        <div class="px-4 pt-3">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h1 class="text-lg font-black text-white">
                        <i class="fas fa-lightbulb text-yellow-500 mr-1"></i> Tips
                    </h1>
                    <p class="text-xs text-white mt-0.5">Learn, grow, and source smarter</p>
                </div>
                <div class="flex items-center gap-2">
                    <button class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-orange-100 hover:text-orange-500 transition">
                        <i class="fas fa-search text-sm"></i>
                    </button>
                    <button class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-orange-100 hover:text-orange-500 transition">
                        <i class="fas fa-bookmark text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="flex gap-4 px-4 pb-2 overflow-x-auto hide-scroll border-b border-gray-100">
            <a href="tips.php" class="text-sm font-bold text-orange-500 border-b-2 border-orange-500 pb-2 whitespace-nowrap">
                <i class="fas fa-fire mr-1"></i> Following
            </a>
            <a href="tips.php" class="text-sm text-white pb-2 whitespace-nowrap">Recommend</a>
            <a href="tips.php" class="text-sm text-white pb-2 whitespace-nowrap">Trending</a>
            <a href="tips.php" class="text-sm text-white pb-2 whitespace-nowrap">Latest</a>
        </div>
    </header>

    <main>
        <?php if (!$is_logged_in): ?>
            <!-- NOT LOGGED IN - LOCKED VIEW -->
            <section class="px-4 pt-6">
                <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-purple-900 to-orange-900 text-white rounded-3xl p-6">
                    <div class="blob w-32 h-32 bg-purple-500 top-0 -right-10"></div>
                    <div class="blob w-24 h-24 bg-orange-500 bottom-0 -left-10"></div>

                    <div class="relative text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-orange-400 to-red-500 rounded-3xl flex items-center justify-center shadow-xl shadow-orange-500/30 animate-float">
                            <i class="fas fa-lock text-3xl"></i>
                        </div>
                        <h2 class="text-xl font-bold mb-2">Unlock Tips & Insights</h2>
                        <p class="text-sm text-gray-300 mb-5 max-w-xs mx-auto">
                            Sign in to access exclusive tips, tutorials, and industry insights
                        </p>

                        <div class="text-left max-w-xs mx-auto mb-6 space-y-2">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-400 mt-0.5"></i>
                                <span class="text-sm text-gray-300">Discover the latest product releases</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-400 mt-0.5"></i>
                                <span class="text-sm text-gray-300">Get supplier special offers & updates</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-400 mt-0.5"></i>
                                <span class="text-sm text-gray-300">Access expert tutorials & guides</span>
                            </div>
                        </div>

                        <a href="login.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold px-8 py-3 rounded-2xl shadow-lg hover:from-orange-600 hover:to-red-600 transition">
                            <i class="fas fa-sign-in-alt"></i> Sign In to Unlock
                        </a>
                    </div>
                </div>
            </section>

            <!-- Preview Topics (Locked) - ပုံနဲ့ပြ -->
            <section class="px-4 pt-6">
                <h2 class="text-sm font-bold text-white mb-3">
                    <i class="fas fa-fire text-orange-500 mr-1"></i> Hot Topics Preview
                </h2>
                <div class="flex gap-3 overflow-x-auto hide-scroll pb-2">
                    <?php foreach (array_slice($trending_topics, 0, 3) as $topic): ?>
                        <div class="topic-card min-w-[160px] h-40 rounded-2xl relative overflow-hidden shadow-lg">
                            <!-- Background Image -->
                            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo htmlspecialchars($topic['bg_image']); ?>');"></div>
                            <div class="absolute inset-0 bg-black/40"></div>
                            <div class="absolute inset-0 shimmer"></div>

                            <div class="absolute top-2 left-2 bg-black/40 backdrop-blur text-white text-xs px-2 py-1 rounded-full z-10"><?php echo $topic['tag']; ?></div>
                            <div class="absolute top-2 right-2 w-8 h-8 bg-black/40 backdrop-blur rounded-full flex items-center justify-center z-10">
                                <i class="fas fa-lock text-white text-xs"></i>
                            </div>
                            
                            <div class="absolute bottom-0 left-0 right-0 p-3 text-white z-10">
                                <div class="text-3xl mb-1"><?php echo $topic['emoji']; ?></div>
                                <p class="text-xs font-bold"><?php echo $topic['title']; ?></p>
                                <p class="text-xs opacity-80"><?php echo $topic['count']; ?> posts</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

        <?php else: ?>
            <!-- LOGGED IN - FULL VIEW -->

            <!-- HERO SECTION -->
        <section class="relative overflow-hidden text-white mx-4 mt-4 rounded-3xl"
            style="background-image: url('image/shopping8.jpeg'); background-position: left;">

                <div class="blob w-32 h-32 bg-purple-500 top-0 -right-10"></div>
                <div class="blob w-24 h-24 bg-orange-500 bottom-0 -left-10"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="inline-flex items-center gap-2 glass rounded-full px-3 py-1.5">
                            <span class="w-2 h-2 bg-red-500 rounded-full live-dot"></span>
                            <span class="text-xs font-medium">🔥 TRENDING NOW</span>
                        </div>
                        <div class="w-10 h-10 glass rounded-2xl flex items-center justify-center animate-float">
                            <i class="fas fa-lightbulb text-yellow-400"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Learn & <span class="gradient-text">grow</span></h2>
                    <p class="text-sm text-gray-300 mb-5">Expert tips, tutorials, and insights to help you succeed in global sourcing</p>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="rounded-xl p-2.5 text-center">
                            <p class="text-orange-400 font-bold text-lg">500+</p>
                            <p class="text-xs text-gray-300">Tips</p>
                        </div>
                        <div class="rounded-xl p-2.5 text-center">
                            <p class="text-purple-400 font-bold text-lg">120+</p>
                            <p class="text-xs text-gray-300">Videos</p>
                        </div>
                        <div class="rounded-xl p-2.5 text-center">
                            <p class="text-green-400 font-bold text-lg">50K+</p>
                            <p class="text-xs text-gray-300">Learners</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- HOT TOPICS (ပုံနဲ့ပြ) -->
            <section class="pt-6">
                <div class="px-4 flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-sm font-bold text-white">
                            <i class="fas fa-fire text-red-500 mr-1"></i> Hot Topics
                        </h2>
                        <p class="text-xs text-white mt-0.5">Trending discussions</p>
                    </div>
                    <a href="#" class="text-xs text-orange-500 font-semibold">See All →</a>
                </div>
                <div class="flex gap-3 overflow-x-auto hide-scroll px-4 pb-2">
                    <?php foreach ($trending_topics as $topic): ?>
                        <div class="topic-card min-w-[160px] h-44 rounded-2xl relative overflow-hidden shadow-lg">
                            <!-- Background Image -->
                            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo htmlspecialchars($topic['bg_image']); ?>');"></div>
                            <div class="absolute inset-0 bg-black/30"></div>
                            <div class="absolute inset-0 shimmer"></div>

                            <div class="absolute top-2 left-2 bg-black/40 backdrop-blur text-white text-xs px-2 py-1 rounded-full font-medium z-10"><?php echo $topic['tag']; ?></div>
                            <div class="absolute top-3 right-3 flex items-center gap-1 z-10">
                                <span class="w-2 h-2 bg-red-400 rounded-full live-dot"></span>
                            </div>
                            
                            <div class="absolute bottom-0 left-0 right-0 p-3 text-white z-10">
                                <div class="text-4xl mb-2 animate-float"><?php echo $topic['emoji']; ?></div>
                                <p class="text-xs font-bold mb-0.5"><?php echo $topic['title']; ?></p>
                                <p class="text-xs opacity-80"><i class="fas fa-eye mr-1"></i><?php echo $topic['count']; ?> posts</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- CATEGORIES -->
            <section class="px-4 pt-5">
                <h2 class="text-sm font-bold text-white mb-3">
                    <i class="fas fa-th-large text-purple-500 mr-1"></i> Browse Categories
                </h2>
                <div class="grid grid-cols-4 gap-2">
                    <?php foreach ($tips_categories as $cat): ?>
                        <a href="#" class="cat-pill glass rounded-2xl p-3 shadow-sm border border-gray-100 text-center hover:border-orange-200">
                            <div class="w-10 h-10 mx-auto mb-1.5 rounded-xl bg-gradient-to-br <?php echo $cat['gradient']; ?> flex items-center justify-center text-white shadow-lg">
                                <i class="fas <?php echo $cat['icon']; ?> text-sm"></i>
                            </div>
                            <p class="text-xs font-bold text-gray-800"><?php echo $cat['label']; ?></p>
                            <p class="text-xs text-white"><?php echo $cat['count']; ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- VIDEO TUTORIALS (YouTube Video နဲ့ပြ) -->
            <section class="pt-6">
                <div class="px-4 flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-sm font-bold text-white">
                            <i class="fas fa-play-circle text-red-500 mr-1"></i> Video Tutorials
                        </h2>
                        <p class="text-xs text-white mt-0.5">Learn from experts</p>
                    </div>
                    <a href="#" class="text-xs text-orange-500 font-semibold">See All →</a>
                </div>
                <div class="flex gap-3 overflow-x-auto hide-scroll px-4 pb-2">
                    <?php foreach ($tips_videos as $video): ?>
                        <div class="tip-card min-w-[240px] bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
                            
                            <!-- YouTube Video (Iframe) -->
                            <div class="relative h-32 bg-black">
                                <iframe class="w-full h-full" 
                                        src="https://www.youtube.com/embed/<?php echo $video['video_id']; ?>" 
                                        title="YouTube video player" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>
                            </div>

                            <!-- Content -->
                            <div class="p-3">
                                <h3 class="text-xs font-bold text-gray-800 leading-snug mb-2 line-clamp-2">
                                    <?php echo $video['title']; ?>
                                </h3>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span class="truncate"><i class="fas fa-user-circle mr-1"></i><?php echo $video['author']; ?></span>
                                    <span><i class="fas fa-eye mr-1"></i><?php echo $video['views']; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- QUICK TIPS LIST -->
            <section class="px-4 pt-6">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-sm font-bold text-white">
                            <i class="fas fa-bolt text-yellow-500 mr-1"></i> Quick Tips
                        </h2>
                        <p class="text-xs text-white mt-0.5">Bite-sized wisdom</p>
                    </div>
                    <span class="text-xs bg-orange-100 text-orange-600 px-2 py-1 rounded-full font-semibold">
                        <?php echo count($tips_list); ?> tips
                    </span>
                </div>
                <div class="space-y-2">
                    <?php foreach ($tips_list as $tip): ?>
                        <div class="tip-list-item slide-in glass rounded-2xl p-3.5 shadow-sm border border-gray-100 flex items-start gap-3 hover:border-orange-200 transition">
                            <div class="tip-icon w-11 h-11 rounded-2xl bg-gradient-to-br <?php echo $tip['gradient']; ?> flex items-center justify-center text-white shadow-lg flex-shrink-0">
                                <i class="fas <?php echo $tip['icon']; ?>"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-sm font-bold text-gray-800"><?php echo $tip['title']; ?></h3>
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-medium"><?php echo $tip['category']; ?></span>
                                </div>
                                <p class="text-xs text-white leading-relaxed"><?php echo $tip['desc']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- CTA SECTION -->
            <section class="px-4 pt-6 pb-6">
                <div class="relative overflow-hidden glass rounded-3xl p-6 text-white gradient-animate">
                    <div class="blob w-32 h-32 bg-yellow-300 top-0 -right-10"></div>
                    <div class="blob w-24 h-24 bg-purple-400 bottom-0 -left-10"></div>
                    <div class="relative text-center">
                        <div class="w-14 h-14 mx-auto mb-3 bg-green-500 backdrop-blur rounded-2xl flex items-center justify-center animate-float">
                            <i class="fas fa-graduation-cap text-2xl"></i>
                        </div>
                        <h2 class="text-lg font-bold mb-1">Want to learn more?</h2>
                        <p class="text-xs text-orange-100 mb-4 max-w-xs mx-auto">Join our community of 50K+ learners and grow your business</p>
                        <a href="#" class="inline-block bg-white text-green-500 font-bold px-6 py-2.5 rounded-full shadow-lg hover:shadow-xl transition">
                            <i class="fas fa-rocket mr-2"></i> Join Community
                        </a>
                    </div>
                </div>
            </section>

        <?php endif; ?>
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