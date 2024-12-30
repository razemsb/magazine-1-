<?php
session_start();
require_once ('../database/db.php');
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        header("Location: buylist.php");
    } else {
        header("Location: buylist.php");
    }
} else {
    header("Location: buylist.php");
}
?>  