<?php
include 'connection.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                //  Space ဖျက်ပြီး Error မတက်အောင် ပြင်ထားပါတယ်
                header("Location: index.php?success=" . urlencode("Welcome back, " . $user['name'] . "!"));
                exit();
            } else {
                $error = "Incorrect Password";
            }
        } else {
            $error = "Email not found";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alihaha - Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!--  Font Awesome  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        
        body {
            background-color: #1e40af; /* Tailwind blue-800 */
        }
        
        /* Form ကို ဖောက်ထွင်းမြင်ရတဲ့ မှန်ချပ်ပုံစံ (Glassmorphism) */
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        /* Input တွေကို အပြာရင့်နောက်ခံပေါ်မှာ လှလှပပ ပေါ်စေရန် */
        .custom-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: all 0.3s ease;
        }
        
        /* Input ကို နှိပ်လိုက်ရင် လိမ္မော်ရောင် (Orange-500) ပြောင်းမယ် */
        .custom-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #f97316; /* Tailwind orange-500 */
            outline: none;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.3);
        }
        
        .custom-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="font-sans min-h-screen flex items-center justify-center p-4">

    <!-- Form Card -->
    <div class="relative w-full max-w-md glass-card rounded-3xl p-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto bg-green-500 rounded-2xl flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-sign-in-alt text-3xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">Welcome Back</h1>
            <p class="text-sm text-blue-200 mt-1">Sign in to your Alihaha account</p>
        </div>

        <!-- Error Message -->
        <?php if($error): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-200 p-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-500/20 border border-green-500 text-green-200 p-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-5">
            
            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-white mb-1.5 ml-1">
                    <i class="fas fa-envelope text-orange-500 mr-1"></i> Email
                </label>
                <input type="email" name="email" required 
                    class="custom-input w-full rounded-xl p-3 text-sm" 
                    placeholder="you@example.com">
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-white mb-1.5 ml-1">
                    <i class="fas fa-lock text-orange-500 mr-1"></i> Password
                </label>
                <input type="password" name="password" required 
                    class="custom-input w-full rounded-xl p-3 text-sm" 
                    placeholder="••••••••">
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                class="w-full bg-orange-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-orange-500/30 hover:bg-orange-600 transition-all flex items-center justify-center gap-2 mt-4">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <!-- Register Link -->
        <p class="text-center text-sm text-blue-200 mt-6">
            Don't have an account? 
            <a href="register.php" class="text-green-400 font-bold hover:text-green-300 hover:underline transition">Register here</a>
        </p>
    </div>

</body>
</html>