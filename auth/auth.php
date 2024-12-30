<?php
session_start();
require_once('../database/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $pass = $_POST['pass'];

    $conn = new mysqli($host, $user, $password, $db);

    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT ID,Password, is_admin, is_active FROM users WHERE Login = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_pass, $isAdmin, $isActive);
    $stmt->fetch();
    $stmt->close();
    if($isActive == 'no_active') {
        echo "<script>alert('Ваш аккаунт заблокирован'); window.location.href = '../main.php';</script>";
        exit();
    }else {
    if (password_verify($pass, $hashed_pass)) {
       if($isAdmin == 1) {
            $_SESSION['admin_auth'] = true;
            $_SESSION['user_auth'] = true;  
            $_SESSION['user_id'] = $id;
            $_SESSION['user_login'] = $name;
            echo "<script>alert('Успешная авторизация администратора!'); window.location.href = '../main.php';</script>";
       }else {
            $_SESSION['admin_auth'] = false;
            $_SESSION['user_auth'] = true;  
            $_SESSION['user_id'] = $id;
            $_SESSION['user_login'] = $name;
            echo "<script>alert('Успешная авторизация!'); window.location.href = '../main.php';</script>";
       }
    } else {
        echo "<script>alert('Неверный логин или пароль'); window.location.href = '../main.php';</script>";
    }

    $conn->close();
}
}
?>