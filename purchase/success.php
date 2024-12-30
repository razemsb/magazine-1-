<?php
session_start();
require_once ('../database/db.php');
$user_login = $_SESSION['user_login'] ?? null;
$orders_count = $_GET['order_id'] ?? 'не указан';
$pdf_path = $_SESSION['pdf_path'] ?? null;

$sql = "SELECT * FROM users WHERE Login = '$user_login'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ успешно оформлен</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<header class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="../main.php">«ИнфоСофт»</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключение навигации">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="../main.php">Главная</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../about.html">О нас</a>
        </li>
      </ul>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle mb-auto" style="font-size: 16px;" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <?php if($_SESSION['user_auth'] == true): ?>
          <img src="../avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="circle-avatar" style="width:50px; height:50px; border-radius: 50%; box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);">
          <?php endif; ?>
          <?php if ($_SESSION['user_auth'] == true): ?>
            <span class="me-2"><?php echo htmlspecialchars($user_login); ?></span>
            <span class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-circle"></i></span>
          <?php else: ?>
          <img src="avatars/basic_avatar.webp" alt="Avatar" class="circle-avatar mb-auto" style="width:50px; height:50px; border-radius: 50%; box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);">
          <?php endif; ?>
        </a>
        <ul class="dropdown-menu" aria-labelledby="userDropdown">
          <?php if ($user_login): ?>
            <li><a class="dropdown-item" href="../profile.php">Профиль</a></li>
            <li><a class="dropdown-item" href="../auth/session_destroy.php">Выйти</a></li>
            <?php if ($_SESSION['user_auth'] == true): ?>
              <li><a class="dropdown-item" href="../admin/admin.php">Админ панель</a></li>
            <?php endif; ?>
          <?php else: ?>
            <li><a class="dropdown-item" href="#" id="openModal">Вход/Регистрация</a></li>
          <?php endif; ?>
          <li><a class="dropdown-item" href="buylist.php">Корзина</a></li>
        </ul>
      </li>
    </div>
  </div>
</header>
    <div class="container text-center mt-5">
        <h1>Спасибо за ваш заказ! (№<?= htmlspecialchars($orders_count) ?>).</h1>
        <p>Ваш заказ был успешно оформлен.</p>
        <div class="mt-4">
            <?php if (isset($pdf_path)): ?>
                <a href="data:application/pdf;base64,<?= base64_encode(file_get_contents($pdf_path)) ?>" download="check_order_<?= htmlspecialchars($orders_count) ?>.pdf" class="btn btn-success mt-2">
                    Скачать чек
                </a>   
            <?php else: ?>
                <p>Чек недоступен для скачивания.</p>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary mt-2" onclick="window.location.href='../main.php'">Вернуться назад</button>
        </div>
    </div>
</body>
</html>
