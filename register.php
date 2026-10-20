<?php
include 'connection.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = isset($_POST['role']) ? $_POST['role'] : 'buyer';

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields.';
    }

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users(name, email, password, role) VALUES(?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now login.";

                //  Space ဖျက်ပြီး Error မတက်အောင် ပြင်ထားပါတယ်
                
                header("Location: login.php?success=" . urlencode($success));
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alihaha - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- ✅ Font Awesome ထည့်ပေးထားပါတယ် -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* နောက်ခံ အပြာရောင် */
        body {
            background-color: #1e40af;
            /* Tailwind blue-800 */
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
            border-color: #f97316;
            /* Tailwind orange-500 */
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
                <i class="fas fa-user-plus text-3xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">Create Account</h1>
            <p class="text-sm text-blue-200 mt-1">Join Alihaha today</p>
        </div>

        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-200 p-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">

            <!-- Full Name -->
            <div>
                <label class="block text-sm font-medium text-white mb-1.5 ml-1">
                    <i class="fas fa-user text-orange-500 mr-1"></i> Full Name
                </label>
                <input type="text" name="name" required
                    class="custom-input w-full rounded-xl p-3 text-sm"
                    placeholder="John Doe">
            </div>

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

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-medium text-white mb-1.5 ml-1">
                    <i class="fas fa-lock text-orange-500 mr-1"></i> Confirm Password
                </label>
                <input type="password" name="confirm_password" required
                    class="custom-input w-full rounded-xl p-3 text-sm"
                    placeholder="••••••••">
            </div>

            <!-- Role Selection -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-white mb-2 ml-1">
                    <i class="fas fa-user-tag text-orange-500 mr-1"></i> I want to join as
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <!-- Buyer -->
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="buyer" class="peer sr-only" checked>
                        <div class="glass-card border border-white/20 rounded-xl p-3 text-center peer-checked:border-orange-500 peer-checked:bg-orange-500/20 transition">
                            <i class="fas fa-shopping-bag text-xl text-blue-300 mb-1"></i>
                            <p class="text-sm font-bold text-white">Buyer</p>
                            <p class="text-[10px] text-blue-200">I want to buy</p>
                        </div>
                    </label>

                    <!-- Seller -->
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="seller" class="peer sr-only">
                        <div class="glass-card border border-white/20 rounded-xl p-3 text-center peer-checked:border-orange-500 peer-checked:bg-orange-500/20 transition">
                            <i class="fas fa-store text-xl text-green-400 mb-1"></i>
                            <p class="text-sm font-bold text-white">Seller</p>
                            <p class="text-[10px] text-blue-200">I want to sell</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-orange-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-orange-500/30 hover:bg-orange-600 transition-all flex items-center justify-center gap-2 mt-4">
                <i class="fas fa-user-plus"></i> Register
            </button>
        </form>

        <!-- Login Link -->
        <p class="text-center text-sm text-blue-200 mt-6">
            Already have an account?
            <a href="login.php" class="text-green-400 font-bold hover:text-green-300 hover:underline transition">Sign In here</a>
        </p>
    </div>

</body>

</html>