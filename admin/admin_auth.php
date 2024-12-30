<?php 
session_start();
require_once ('../database/db.php');
if (isset($_POST['user_id']) && isset($_POST['admin_password'])) {            
    $user_id = $_POST['user_id'];
    $admin_password = $_POST['admin_password'];
    $stmt = $conn->prepare("SELECT user_id, admin_password FROM admin WHERE user_id = ? AND admin_password = ?");
    $stmt->bind_param("is", $user_id, $admin_password);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['is_admin'] = 1;
        $_SESSION['admin_auth_password'] = True;
        header("Location: admin.php?section=none");
        exit();
    } else {
        $_SESSION['error'] = 'Неверные данные';
        header("Location: admin.php"); 
        exit();
    }   
}else {
    $_SESSION['error'] = 'Не все данные переданы';
    header("Location: admin.php"); 
    exit();
}
