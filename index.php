<?php
session_start();
require_once ('database/db.php');
if($_SESSION['user_auth'] == true) {
  header('Location: main.php');
  exit();
}
?>  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="icons/logotype.png" type="image/x-icon" sizes="32x32">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/signin.css">
    <title>«ИнфоСофт»</title>
</head>
<body>
    <header class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
          <a class="navbar-brand" href="main.php">«ИнфоСофт»</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключение навигации">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
              <li class="nav-item">
                <a class="nav-link" href="about.html">О нас</a>
              </li>
            </ul>
          </div>
        </div>
      </header>
<div class="cont">
    <form id="form1" class="auth_form" action="auth/auth.php" method="post">
        <strong class="strong">Авторизация</strong><br>
        <input type="text" name="name" placeholder="Ваш никнейм" required><br>
        <input type="password" name="pass" placeholder="Ваш Пароль" required><br>
        <p class="text">Нет аккаунта? <a href="#" id="switchToRegister">Регистрация</a></p><br>
        <input type="submit" value="Войти">
    </form>

    <form id="form2" class="auth_form hidden" action="auth/reg.php" method="post">
        <strong class="strong">Регистрация</strong><br>
        <input type="text" name="name" placeholder="Ваш никнейм" required><br>
        <input type="password" name="pass" placeholder="Ваш Пароль" required><br>
        <input type="password" name="repeatpass" placeholder="Повторите ваш Пароль" required><br>
        <input type="email" name="email" placeholder="Почта" required><br>
        <p class="text">Уже зарегистрированы? <a href="#" id="switchToLogin">Вход в аккаунт</a></p><br>
        <input type="submit" value="Зарегистрироваться">
    </form>
</div>
<script src="script/authlog.js"></script>
</body>
</html>