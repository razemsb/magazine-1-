<?php
session_start();    
require_once ('../database/db.php');    
if(isset($_POST['id']) && isset($_POST['category'])) {
    $ID = $_POST['id'];
    $category = $_POST['category'];
    if($category == 'recovery') {
        $stmt = $conn->prepare("UPDATE categories SET is_active = 'active' WHERE ID = ?");
        $stmt->bind_param("i", $ID);
        $stmt->execute();
        $stmt->close();
        header("Location: admin.php?section=categories");
    } elseif($category == 'delete') {
        $stmt = $conn->prepare("UPDATE categories SET is_active = 'no_active' WHERE ID = ?");
        $stmt->bind_param("i", $ID);
        $stmt->execute();
        $stmt->close();
        header("Location: admin.php?section=categories");
    }else {
        header("Location: admin.php");
}
}else {
    header("Location: admin.php");
}
?>