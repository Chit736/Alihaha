<?php

session_start();
include 'connection.php';
include 'function.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])){
    header ("location: login.php?error=" . urlencode("Please login to add products."));
    exit();
}

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['role'] ?? 'buyer';
    
    $check_sql="SELECT seller_id, image FROM products WHERE id=?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0 ){
        $row = $result->fetch_assoc();

        // Admin မဟုတ်ရင် ပြီးတော့ ကိုပစွည်းမဟုတ်ရင် ဖျက်ခွင့်မပြုဘူး 
        if($user_role !== 'admin' && $product['seller_id'] != $user_id){
            header("location: products.php? error =" . urlencode ("You can only delete your own products."));
            exit();
        }
        // ပုံဖျက်မယ် 
        if($product['image'] && file_exits("image/" . $product['image'])){
            unlink("image"/ . $product['image']);
        }

        // ပစွည်းဖျက်မယ်
        $delete_sql = "DELETE FROM products WHERE id = ?";
        $del_stmt = $conn->prepare($delete_sql);
        $del_stmt->bind_param("i", $id);
        $del_stmt->execute();
        $del_stmt->close();

        header("location: products.php?success=Product deleted successfully!");
        exit();
    }}
    header("location: products.php");
    exit();

?>