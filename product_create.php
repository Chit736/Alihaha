<?php
session_start();
include 'connection.php';

//  Login — စစ်
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php?error=" . urlencode("Please login to add products."));
    exit();
}

// Buyer ဖြစ်ရင်  ပစွည်းတင်ခွင့် မပြုဘူး (Admin နဲ့ Seller ပဲရမယ်) 
$user_role = $_SESSION['role'] ?? 'buyer';
if ($user_role != 'admin' && $user_role !== 'seller'){
    header("location: products.php?error=" . urlencode("Only Sellers can add products. Please Register as a Seller."));
    exit();
}

//  Category + Supplier — Database ကနေ
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$suppliers = $conn->query("SELECT id, name FROM manufacturers ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alihaha - Add Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* နောက်ခံ အပြာရောင် */
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

        /* Select box ထဲက Option တွေကို ဖတ်ရလွယ်အောင် */
        select.custom-input option {
            background-color: #1e40af;
            color: white;
        }
    </style>
</head>
<body class="font-sans min-h-screen flex items-center justify-center p-4">

    <!-- Form Card -->
    <div class="relative w-full max-w-md glass-card rounded-3xl p-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto bg-green-500 rounded-2xl flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-plus-circle text-3xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">Add New Product</h1>
            <p class="text-sm text-blue-200 mt-1">Fill in the details to add to marketplace</p>
        </div>

        <!-- Error Message -->
        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-200 p-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="product_store.php" method="POST" enctype="multipart/form-data" class="space-y-5">

            <!-- Title -->
            <div>
                <label class="block text-sm font-medium text-white mb-1.5 ml-1">
                    <i class="fas fa-tag text-orange-500 mr-1"></i> Product Title
                </label>
                <input type="text" name="title" required
                    class="custom-input w-full rounded-xl p-3 text-sm"
                    placeholder="Enter product title">
            </div>

            <!-- Price -->
            <div>
                <label class="block text-sm font-medium text-white mb-1.5 ml-1">
                    <i class="fas fa-dollar-sign text-green-500 mr-1"></i> Price ($)
                </label>
                <input type="number" step="0.01" name="price" required
                    class="custom-input w-full rounded-xl p-3 text-sm"
                    placeholder="0.00">
            </div>

            <!-- Min Order -->
            <div>
                <label class="block text-sm font-medium text-white mb-1.5 ml-1">
                    <i class="fas fa-boxes text-orange-500 mr-1"></i> Minimum Order
                </label>
                <input type="number" name="min_order" value="1" min="1"
                    class="custom-input w-full rounded-xl p-3 text-sm">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-white mb-1.5 ml-1">
                    <i class="fas fa-list text-green-500 mr-1"></i> Category
                </label>
                <select name="category_id" required
                    class="custom-input w-full rounded-xl p-3 text-sm appearance-none">
                    <option value="">-- Select Category --</option>
                    <?php while ($c = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $c['id']; ?>">
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Supplier -->
            <div>
                <label class="block text-sm font-medium text-white mb-1.5 ml-1">
                    <i class="fas fa-industry text-orange-500 mr-1"></i> Supplier
                </label>
                <select name="supplier_id" required
                    class="custom-input w-full rounded-xl p-3 text-sm appearance-none">
                    <option value="">-- Select Supplier --</option>
                    <?php while ($s = $suppliers->fetch_assoc()): ?>
                        <option value="<?php echo $s['id']; ?>">
                            <?php echo htmlspecialchars($s['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Image -->
            <div>
                <label class="block text-sm font-medium text-white mb-1.5 ml-1">
                    <i class="fas fa-image text-green-500 mr-1"></i> Image
                </label>
                <div class="custom-input w-full rounded-xl p-2 flex items-center">
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-sm text-blue-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-500 file:text-white hover:file:bg-orange-600 transition cursor-pointer">
                </div>
            </div>

            <!-- Save Button -->
            <button type="submit"
                class="w-full bg-orange-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-orange-500/30 hover:bg-orange-600 transition-all flex items-center justify-center gap-2 mt-4">
                <i class="fas fa-save"></i> Save Product
            </button>

            <!-- Back Button -->
            <a href="products.php"
                class="block text-center text-white text-sm mt-5 hover:text-green-400 transition flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </form>
    </div>

</body>
</html>