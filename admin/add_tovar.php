<?php   
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);     
session_start();
require_once ('../database/db.php');
if ($_POST['tovar_id'] && $_POST['Count'] && $_POST['user_id']) {
    $tovar_id = $_POST['tovar_id'];
    $Count = $_POST['Count'];
    $user_id = $_POST['user_id'];
    $stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?" );
    $stmt->bind_param("ii", $Count, $tovar_id );
    $stmt->execute();
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO adds_tovar (tovar_id, Count, user_id, date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iii", $tovar_id, $Count, $user_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Товар успешно пополнен!'); window.location.href = 'admin.php?section=none';</script>";
    exit();
}
?>