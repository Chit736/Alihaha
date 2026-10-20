<?php
session_start();
include 'connection.php';
include 'function.php';

if(!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])){
    header("location:login.php?error=" . urlencode("Please login to add products."));
    exit();
}

if(isset($_GET['id'])){
    $product_id = intval($_GET['id']);

    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = array();
    }

    if (isset($_SESSION['cart'][$product_id])){
        $_SESSION['cart'][$product_id] += 1;
    }else{
        $_SESSION['cart'][$product_id] = 1;
    }
    
    header("location: cart.php?success=Product added to Cart!");
    exit();
}else{
    header("location: products.php?error=Invalid product.");
    exit();
}

?>