<?php
session_start();
include 'connection.php';

//  Login — စစ်
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php?error=" . urlencode("Please login to add products."));
    exit();
}

//  POST — စစ်
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: product_create.php");
    exit();
}

// Data — ယူ
$title       = isset($_POST['title']) ? trim($_POST['title']) : '';
$price       = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$min_order   = isset($_POST['min_order']) ? intval($_POST['min_order']) : 1;
$category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
$supplier_id = isset($_POST['supplier_id']) ? intval($_POST['supplier_id']) : 0;
$seller_id = intval($_SESSION['user_id']);

//  Validation
if (empty($title) || $price <= 0 || $category_id <= 0 || $supplier_id <= 0) {
    header("Location: product_create.php?error=" . urlencode("Please fill all required fields."));
    exit();
}

//  Image Upload
$image = '';

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        header("Location: product_create.php?error=" . urlencode("Invalid image type."));
        exit();
    }

    $target_dir = "image/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image = time() . '_' . basename($_FILES['image']['name']);
    $target_file = $target_dir . $image;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        header("Location: product_create.php?error=" . urlencode("Failed to upload image."));
        exit();
    }
}

//  INSERT — Prepared Statement
$sql = "INSERT INTO products (title, price, min_order, image, category_id, supplier_id, seller_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sdisiii", $title, $price, $min_order, $image, $category_id, $supplier_id, $seller_id);

if ($stmt->execute()) {
    header("Location: products.php?success=" . urlencode("Product added successfully!"));
    exit();
} else {
    header("Location: product_create.php?error=" . urlencode($stmt->error));
    exit();
}
?>