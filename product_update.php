<?php
session_start();
ini_set('post_max_size', '100M');
ini_set('max_execution_time', '300');
ini_set('memory_limit', '256M');

include 'connection.php';
include 'function.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php?error=" . urlencode("Please login to add products."));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

$id = intval($_POST['id']);
$name = htmlspecialchars(trim($_POST['name']));
$price = $_POST['price'];
$description = htmlspecialchars(trim($_POST['description']));
$category = isset($_POST['category']) ? htmlspecialchars(trim($_POST['category'])) : 'General';

$old_image = isset($_POST['old_image']) ? $_POST['old_image'] : '';
$image = $old_image;

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        header("Location: product_edit.php?id=$id&error=" . urlencode("Only JPG, PNG, GIF, WEBP, BMP, SVG are allowed."));
        exit();
    }
    
    $target_dir = "image/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $image = time() . '_' . basename($_FILES['image']['name']);
    $target_file = $target_dir . $image;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        if (!empty($old_image) && file_exists($target_dir . $old_image)) {
            unlink($target_dir . $old_image);
        }
    } else {
        $image = $old_image;
        header("Location: product_edit.php?id=$id&error=" . urlencode("Failed to upload image."));
        exit();
    }
}

//  category update
$sql = "UPDATE products SET title=?, price=?, description=?, category=?, image=? WHERE id=?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sdsssi", $name, $price, $description, $category, $image, $id);

if ($stmt->execute()) {
    header("Location: products.php?success=Product updated successfully!");
    exit();
} else {
    header("Location: product_edit.php?id=$id&error=" . urlencode($stmt->error));
    exit();
}

$stmt->close();
$conn->close();
?>