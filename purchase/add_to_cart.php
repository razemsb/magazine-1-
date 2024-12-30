<?php
session_start();
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
        header("Location: buylist.php");  
    } else {
        $_SESSION['cart'][$product_id] = 1;
        header("Location: buylist.php");   
    }
}else {
    header("Location: index.php");
}
?>    