<?php
session_start();
$user_login = isset($_SESSION['user_login']) ? $_SESSION['user_login'] : null;
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;

require_once 'database/db.php';

$categories = $conn->query("SELECT * FROM categories WHERE is_active = 'active' ORDER BY name");
$products_query = "SELECT * FROM products WHERE is_active = 'active'";
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = $conn->real_escape_string($_GET['category']);
    $products_query .= " AND category = '$category'";
}

if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

$limit = 6;
$offset = ($page - 1) * $limit;

$products_query .= " LIMIT $limit OFFSET $offset";

$products = $conn->query($products_query);
$num_products = $conn->query("SELECT COUNT(*) FROM products WHERE is_active = 'active'")->fetch_row()[0];
$num_pages = ceil($num_products / $limit);

$sql = "SELECT * FROM users WHERE Login = '$user_login'";
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
    <link rel="shortcut icon" href="icons/logotype.png" type="image/x-icon">
    <title>Магазин программ 1С</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/signin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Продукты
          </a>
          <ul class="dropdown-menu" aria-labelledby="productsDropdown">
            <?php while($category = $categories->fetch_assoc()): ?>
              <li><a class="dropdown-item" href="main.php?category=<?php echo urlencode($category['name']); ?>">
                <?php echo htmlspecialchars($category['name']); ?></a></li>
            <?php endwhile; ?>
          </ul>
        </li>
      </ul>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle mb-auto" style="font-size: 16px;" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <?php if($_SESSION['user_auth'] == true): ?>
          <img src="avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="circle-avatar" style="width:50px; height:50px; border-radius: 50%; box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);">
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
            <li><a class="dropdown-item" href="profile.php">Профиль</a></li>
            <li><a class="dropdown-item" href="auth/session_destroy.php">Выйти</a></li>
            <?php if ($_SESSION['user_auth'] == true): ?>
              <li><a class="dropdown-item" href="admin/admin.php">Админ панель</a></li>
            <?php endif; ?>
          <?php else: ?>
            <li><a class="dropdown-item" href="#" id="openModal">Вход/Регистрация</a></li>
          <?php endif; ?>
          <li><a class="dropdown-item" href="purchase/buylist.php">Корзина</a></li>
        </ul>
      </li>
    </div>
  </div>
</header>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <h4>Категории</h4>
                <div class="list-group">
                    <a href="main.php" class="list-group-item list-group-item-action">Все продукты</a>
                    <nav class="pagination mt-2" aria-label="Навигация" class="d-flex justify-content-center">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="main.php?page=<?php echo $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $num_pages; $i++): ?>
                <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                    <a class="page-link" href="main.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($page < $num_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="main.php?page=<?php echo $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
            </ul>
        </nav>
                    <?php while($category = $categories->fetch_assoc()): ?>
                        <a href="main.php?category=<?php echo urlencode($category['name']); ?>" class="list-group-item list-group-item-action"> 
                        <?php echo htmlspecialchars($category['name']); ?></a>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <?php while($product = $products->fetch_assoc()): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <?php if($product['image_path']): ?>
                                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                                         class="card-img-top" alt="<?php echo htmlspecialchars($product['title']); ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                                    <p class="card-text"><strong>Версия:</strong> <?php echo htmlspecialchars($product['version']); ?></p>
                                    <p class="card-text"><strong>Цена:</strong> <?php echo number_format($product['price'], 2); ?> ₽</p>
                                    <p class="card-text"><strong>Количество:</strong> <?php echo $product['quantity']; ?></p>
                                    <p class="card-text"><strong>Категория:</strong> <?php echo htmlspecialchars($product['category']); ?></p>   
                                </div>
                                <button type="button" class="btn btn-primary" onclick="OpenModal(<?php echo $product['id']; ?>)">Подробнее</button>
                                <?php if($user_login): ?>
                                <?php if($product['quantity'] > 0 && $product['is_active'] == 'active'): ?>
                                <form action="purchase/add_to_cart.php" method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-success mt-1">В корзину</button>
                                    </form>    
                                <?php else: ?>
                                        <button type="button" class="btn btn-danger mt-1">Товар закончился</button>
                                <?php endif; ?>
                                <?php else: ?>
                                <button class="btn-account mt-1" id="openModal">Для покупки войдите в аккаунт</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
  
<div id="modal" class="modal">
   <div class="cont">
    <form id="form1" class="auth_form" action="auth/auth.php" method="post">
        <strong class="strong">Авторизация<span class="close" onclick="closeModal();">&times;</span></strong><br>
        <input type="text" name="name" placeholder="Ваш никнейм" required><br>
        <input type="password" name="pass" placeholder="Ваш Пароль" required><br>
        <p class="text">Нет аккаунта? <a href="#" id="switchToRegister">Регистрация</a></p><br>
        <input type="submit" value="Войти">
    </form>

    <form id="form2" class="auth_form hidden" action="auth/reg.php" method="post">
        <strong class="strong">Регистрация<span class="close" onclick="closeModal();">&times;</span></strong><br>
        <input type="text" name="name" placeholder="Ваш никнейм" required><br>
        <input type="password" name="pass" placeholder="Ваш Пароль" required><br>
        <input type="password" name="repeatpass" placeholder="Повторите ваш Пароль" required><br>
        <input type="email" name="email" placeholder="Почта" required><br>
        <p class="text">Уже зарегистрированы? <a href="#" id="switchToLogin">Вход в аккаунт</a></p><br>
        <input type="submit" value="Зарегистрироваться">
    </form>
        </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script/script.js"></script>
<script src="script/authlog.js"></script>

<div id="productModal" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle"></h5>
        <button type="button" class="close" onclick="closeModal()">&times;</button>
      </div>
      <div class="modal-body" id="modalBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal()">Закрыть</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>
