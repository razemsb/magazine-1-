<?php 
session_start();
require_once ('../database/db.php');
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header('Location: main.php');
    exit();
}
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("UPDATE products SET is_active = 'active' WHERE id = ?;");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Продукт успешно активирован!'); window.location.href = 'admin.php';</script>";
} else {
    echo "<script>alert('Ошибка активации продукта!'); window.location.href = 'admin.php';</script>";
}
?>