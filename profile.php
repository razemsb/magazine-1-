<?php
require_once('database/db.php');
session_start();
if (!isset($_SESSION['user_auth']) == true) {
    header('Location: main.php');   
    exit();
}
$user_login = $_SESSION['user_login'];
if ($_SESSION['is_admin'] == true) {
    $is_admin = true;
}else {
    $is_admin = false;
}
$sql = "SELECT * FROM users WHERE login = '$user_login'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <title>Профиль | <?= $user_login ?></title>
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
          <a class="nav-link" href="main.php">Главная</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="about.html">О нас</a>
        </li>
      </ul>
      <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
          <?php if ($user_login): ?>
            <img src="avatars/<?= $user['avatar'] ?>" alt="Avatar" class="circle-avatar" style="width:50px; height:50px; border-radius: 50%; box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);">
            <span class="user-name ms-1"><?= htmlspecialchars($user_login) ?></span>
          <?php else: ?>
            <i class="bi bi-person-circle"></i>
          <?php endif; ?>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
          <?php if ($_SESSION['user_auth'] == true): ?>
            <li><a class="dropdown-item" href="auth/session_destroy.php">Выйти</a></li>
            <?php if ($is_admin == true): ?>
              <li><a class="dropdown-item" href="admin/admin.php?section=none">Админ панель</a></li>
            <?php endif; ?>
          <?php else: ?>
            <li><a class="dropdown-item" href="index.php">Вход/Регистрация</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
</header>
<main class="container">
  <h1 class="display-4 text-center">Профиль: <?= htmlspecialchars($user_login) ?></h1>
  <div class="row">
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Данные</h5>
          <?php if($user['avatar'] == "basic_avatar.webp"): ?>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="avatar" class="form-label">Выберите файл аватара</label>
              <input class="form-control" type="file" id="avatar" name="avatar" accept=".png, .jpg, .jpeg, .gif">
            </div>
            <button type="submit" class="btn btn-primary">Загрузить</button>
          </form>
          <?php
          if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $target_dir = "avatars/";
            $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
            $final_file = basename($_FILES["avatar"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($final_file,PATHINFO_EXTENSION));
          
            $check = getimagesize($_FILES["avatar"]["tmp_name"]);
            if($check !== false) {
              $uploadOk = 1;
            } else {
              $uploadOk = 0;
            }
          
            if (file_exists($final_file)) {
              echo '<div class="alert alert-danger mt-1 alert-dismissible fade show" role="alert" style="opacity:1; transition: opacity 0.5s ease-in-out;">Изображение уже существует<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
              $uploadOk = 0;
            }
          
            if ($_FILES["avatar"]["size"] > 500000) {
              echo '<div class="alert alert-danger mt-1 alert-dismissible fade show" role="alert" style="opacity:1; transition: opacity 0.5s ease-in-out;">Изображение слишком большое<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
              $uploadOk = 0;
            }
          
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
              echo '<div class="alert alert-danger mt-1 alert-dismissible fade show" role="alert" style="opacity:1; transition: opacity 0.5s ease-in-out;">Только JPG, JPEG, PNG & GIF файлы разрешены<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
              $uploadOk = 0;
            }
          
            if ($uploadOk == 0) {
              echo '<div class="alert alert-danger mt-1 alert-dismissible fade show" role="alert" style="opacity:1; transition: opacity 0.5s ease-in-out;">Изображение не может быть загружено<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            } else {
              if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE Login = ?");
                $stmt->bind_param("ss", $final_file, $user_login);
                $stmt->execute();
                echo '<div class="alert alert-success mt-1 alert-dismissible fade show" role="alert" style="opacity:1; transition: opacity 0.5s ease-in-out;">Изображение успешно загружено<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
              } else {
                echo '<div class="alert alert-danger mt-1 alert-dismissible fade show" role="alert" style="opacity:1; transition: opacity 0.5s ease-in-out;">Ошибка при загрузке изображения<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
              }
            }
          }
          ?>
          <?php else: ?>

          <?php endif; ?>
          <p class="card-text">Email: <?= $user['Email'] ?></p>
          <p class="card-text">Дата регистрации: <?= date('d.m.Y в H:i', strtotime($user['date_reg'])) ?></p>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Мои заказы</h5>
          <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#orders" aria-expanded="false" aria-controls="orders" class="mb-5">
            Показать мои заказы
          </button>
          <div class="collapse" id="orders">
            <div class="card card-body">
              <?php
                $user_id = $user['ID'];
                $sql = "SELECT * FROM orders WHERE user_id = '$user_id'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0):
              ?>
              <ul>
                <?php while($order = $result->fetch_assoc()): ?>
                  <li>
                    <h6>Заказ No<?= $order['id'] ?></h6>
                    <p>Дата: <?= date('d.m.Y в H:i', strtotime($order['order_date'])) ?></p>
                    <p>Товары:</p>
                    <ul>
                      <?php
                        $order_id = $order['id'];
                        $sql2 = "SELECT o.id AS order_id, o.product_id, p.title, p.image_path FROM orders o INNER JOIN products p ON o.product_id = p.id WHERE o.id = '$order_id'";
                        $result2 = $conn->query($sql2);
                        if ($result2->num_rows > 0):
                          while($product = $result2->fetch_assoc()):
                      ?>
                        <li>
                          <?= $product['title'] ?>
                          <img src="<?= $product['image_path'] ?>" alt="<?= $product['image_path'] ?>" width="50" style="float:right;"> 
                        </li>
                        <hr class="mt-4 mb-2 text-muted">
                      <?php
                          endwhile;
                        endif;
                      ?>
                    </ul>
                  </li>
                <?php endwhile; ?>
              </ul>
              <?php else: ?>
                <p class="card-text">Здесь пока что ничего нет</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="script/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>