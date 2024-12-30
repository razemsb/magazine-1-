<?php
session_start();

if (isset($_SESSION['user_login'])) {
    unset($_SESSION['user_login']);
    
    session_unset();
    session_destroy();
    if(isset($_SESSION['user_Login'])) {

    echo "<script>alert('Вы вышли из аккаунта.')</script>";
    header("Location: ../main.php");
    exit();
    }else {
    header("Location: ../main.php");   
    exit();
    }
} else {
    header("Location: ../main.php");
    exit();
}
?>