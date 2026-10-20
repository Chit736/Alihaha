<?php
session_start();
include 'connection.php';
include 'function.php';

if (!isset($_GET['id'])) {
    header("Location: messenger.php");
    exit();
}

$chat_id = intval($_GET['id']);

// Chat လုပ်မယ့် သူတွေရဲ့ အချက်အလက် (အပေါ်က Messenger စာမျက်နှာက Data တွေအတိုင်း)
$contacts = [
    1 => ['name' => 'RZX Trading Co.', 'flag' => '🇨🇳', 'online' => true, 'verified' => true],
    2 => ['name' => 'Seoul Beauty Ltd.', 'flag' => '🇰🇷', 'online' => true, 'verified' => true],
    3 => ['name' => 'HK Toys International', 'flag' => '🇭🇰', 'online' => false, 'verified' => true],
    4 => ['name' => 'Shenzhen Industrial', 'flag' => '🇨🇳', 'online' => false, 'verified' => true],
    5 => ['name' => 'Vietnam Cashew Export', 'flag' => '🇻🇳', 'online' => true, 'verified' => true],
    6 => ['name' => 'Mumbai Medical Supplies', 'flag' => '🇮🇳', 'online' => false, 'verified' => false],
    7 => ['name' => 'Yangon Export House', 'flag' => '🇲🇲', 'online' => true, 'verified' => true],
];

$contact = $contacts[$chat_id] ?? ['name' => 'Unknown', 'flag' => '🌐', 'online' => false, 'verified' => false];

// ပို့လိုက်တဲ့ စာတွေကို Session ထဲမှာ သိမ်းမယ်
if (!isset($_SESSION['chats'])) {
    $_SESSION['chats'] = [];
}

if (!isset($_SESSION['chats'][$chat_id])) {
    // စတင်တဲ့ အချိန်မှာ ပြသမယ့် စာသား
    $_SESSION['chats'][$chat_id] = [
        ['sender' => 'them', 'text' => 'Hello! Thank you for reaching out. How can we help you today?', 'time' => date('h:i A')]
    ];
}

// စာပို့လိုက်တဲ့အခါ  (Form Submit လုပ်လိုက်တဲ့အခါ)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['message']))) {
    $msg = htmlspecialchars(trim($_POST['message']));
    $_SESSION['chats'][$chat_id][] = ['sender' => 'me', 'text' => $msg, 'time' => date('h:i A')];
    
    // ပို့ပြီးတာနဲ့ Page ကို Refresh လုပ်မယ် (စာပေါ်လာအောင်)
    header("Location: chat.php?id=" . $chat_id);
    exit();
}

$messages = $_SESSION['chats'][$chat_id];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo $contact['name']; ?> - Alihaha</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .glass { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.25); }
        .hide-scroll::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-blue-800 font-sans h-screen flex flex-col">

    <!-- Chat Header -->
    <header class="glass sticky top-0 z-50 p-3 flex items-center gap-3 border-b border-white/20 shadow-md">
        <a href="messenger.php" class="w-9 h-9 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center text-white transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-base font-bold text-white flex items-center gap-1.5 truncate">
                <?php echo htmlspecialchars($contact['name']); ?>
                <?php if ($contact['verified']): ?>
                    <i class="fas fa-check-circle text-blue-400 text-xs"></i>
                <?php endif; ?>
                <span class="text-sm"><?php echo $contact['flag']; ?></span>
            </h1>
            <p class="text-xs <?php echo $contact['online'] ? 'text-green-400' : 'text-gray-300'; ?> flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full <?php echo $contact['online'] ? 'bg-green-400' : 'bg-gray-400'; ?>"></span>
                <?php echo $contact['online'] ? 'Active Now' : 'Offline'; ?>
            </p>
        </div>
        <button class="w-9 h-9 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center text-white">
            <i class="fas fa-ellipsis-v"></i>
        </button>
    </header>

    <!-- Chat Body (Messages) -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4 hide-scroll" id="chatContainer">
        <div class="text-center mb-4">
            <span class="text-xs bg-blue-900/50 text-blue-200 px-3 py-1 rounded-full">Today</span>
        </div>

        <?php foreach ($messages as $msg): ?>
            <?php if ($msg['sender'] === 'them'): ?>
                <!-- သူတို့ ပို့တဲ့စာ (ဘယ်ဘက်) -->
                <div class="flex items-end gap-2">
                    <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                        <?php echo substr($contact['name'], 0, 1); ?>
                    </div>
                    <div class="max-w-[75%] bg-white/10 border border-white/20 p-3 rounded-2xl rounded-bl-none text-white shadow-sm">
                        <p class="text-sm"><?php echo $msg['text']; ?></p>
                        <p class="text-[10px] text-gray-300 text-right mt-1"><?php echo $msg['time']; ?></p>
                    </div>
                </div>
            <?php else: ?>
                <!-- ကျွန်တော် ပို့တဲ့စာ (ညာဘက်) -->
                <div class="flex items-end justify-end gap-2">
                    <div class="max-w-[75%] bg-gradient-to-r from-orange-500 to-red-500 p-3 rounded-2xl rounded-br-none text-white shadow-md">
                        <p class="text-sm"><?php echo $msg['text']; ?></p>
                        <p class="text-[10px] text-orange-100 text-right mt-1 flex items-center justify-end gap-1">
                            <?php echo $msg['time']; ?> <i class="fas fa-check-double"></i>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Chat Input -->
    <div class="p-3 bg-blue-900/80 backdrop-blur-md border-t border-white/10 sticky bottom-0">
        <form method="POST" class="flex items-center gap-2">
            <button type="button" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center text-gray-300 hover:text-white transition">
                <i class="fas fa-plus"></i>
            </button>
            <div class="flex-1 bg-white/10 border border-white/20 rounded-full flex items-center px-4 py-2">
                <input type="text" name="message" required placeholder="Type a message..." autocomplete="off"
                       class="bg-transparent w-full text-sm text-white placeholder-gray-400 focus:outline-none">
            </div>
            <button type="submit" class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white shadow-lg hover:bg-orange-600 transition">
                <i class="fas fa-paper-plane text-sm"></i>
            </button>
        </form>
    </div>

    <script>
        // စာပို့ပြီးတာနဲ့ အောက်ဆုံးကို အလိုအလျောက် ဆင်းသွားအောင်
        const chatContainer = document.getElementById('chatContainer');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    </script>
</body>
</html>