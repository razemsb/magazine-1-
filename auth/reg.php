<?php
session_start();
require_once('../database/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $pass = $_POST['pass'];
    $repeatpass = $_POST['repeatpass'];
    $email = $_POST['email'];

    if ($pass !== $repeatpass) {
        echo "<script>alert('Пароли не совпадают'); window.location.href = '../index.php';</script>";
        exit();
    }

    $conn = new mysqli($host, $user, $password, $db);

    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE Login = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "<script>alert('Этот логин уже занят. Пожалуйста, выберите другой.'); window.location.href = '../index.php';</script>";
        exit();
    }

    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
    $is_admin = "0";
    $balance = "0";
    $avatar = "basic_avatar.webp";
    $stmt = $conn->prepare("INSERT INTO users (Login, Password, Email, avatar, balance, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $name, $hashed_pass, $email, $avatar, $balance, $is_admin);

    if ($stmt->execute()) {
        echo "<script>alert('Успешная регистрация'); window.location.href = '../main.php';</script>";
        $_SESSION['user_login'] = $name;
        $_SESSION['user_auth'] = true;
    } else {
        echo "<script>alert('Ошибка при регистрации: " . $conn->errno . "'); window.location.href = '../index.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
