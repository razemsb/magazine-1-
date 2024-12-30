<?php
session_start();
require_once ('../database/db.php');
require_once('../functions.php');

if (!$_SESSION['admin_auth']) {
    header('Location: ../main.php');
    exit();
}

$section = isset($_GET['section']) ? $_GET['section'] : 'none';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="icons/logotype.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Админ панель</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="">«ИнфоСофт» Админ панель</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="delete_admin_session.php">На главную</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Выбор категории
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item <?= ($section == 'products') ? 'active' : '' ?>" href="?section=products">Товары</a></li>
                        <li><a class="dropdown-item <?= ($section == 'categories') ? 'active' : '' ?>" href="?section=categories">Категории</a></li>
                        <li><a class="dropdown-item <?= ($section == 'users') ? 'active' : '' ?>" href="?section=users">Пользователи</a></li>
                        <li><a class="dropdown-item <?= ($section == 'orders') ? 'active' : '' ?>" href="?section=orders">Заказы</a></li>
                        <li><a class="dropdown-item <?= ($section == 'add_products') ? 'active' : '' ?>" href="?section=add_products">История пополнений</a></li>
                        <li><a class="dropdown-item <?= ($section == 'adds_tovars') ? 'active' : '' ?>" href="?section=adds_tovars">Пополнить товар</a></li>
                        <?php if ($section != 'none'): ?>
                            <li><a class="dropdown-item <?= ($section == 'none') ? 'active' : '' ?>" href="?section=none">Сбросить</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="add_product.php">Добавить товар</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1>Админ панель</h1>
    <?php if ($section == 'products'): ?>
    <?php
    $limit = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $productsPagination = getPaginatedData($conn, 'products', $limit, $page);
    ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Цена</th>
                <th>Категория</th>
                <th>Картинка</th>
                <th>Статус</th>
                <th>Состояние</th>
                <th>Количество</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($productsPagination['data'] as $product): 
            ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= $product['title'] ?></td>    
                    <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $product['description'] ?></td>
                    <td><?= $product['price'] ?></td>
                    <td><?= $product['category'] ?></td>            
                    <td><img src="<?= '../'.$product['image_path'] ?>" alt="<?= $product['title'] ?>" width="100"></td>
                    <td>
                        <?php if ($product['is_active'] == 'active'): ?>
                            <p class="text-success">Активен</p>
                        <?php else: ?>
                            <p class="text-danger">Не активен</p>
                        <?php endif; ?> 
                    </td>   
                    <td>
                        <?php if ($product['is_active'] == 'active' && $product['quantity'] > 0 ): ?>
                            <p class="text-success">Доступен</p>
                        <?php else: ?>
                            <p class="text-danger">Недоступен</p>
                        <?php endif; ?> 
                    </td>
                    <td><?= $product['quantity'] ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-primary mt-1 mb-2">Редактировать</a>
                        <?php if ($product['is_active'] == 'active'): ?>
                        <form action="delete_product.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-danger">Удалить</button>
                        </form>
                        <?php else: ?>
                            <form action="activate_product.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-success">Активировать</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="pagination">
    <?php for ($i = 1; $i <= $productsPagination['pages']; $i++): ?>
        <a href="?section=products&page=<?= $i ?>" class="btn btn-primary <?= ($page == $i) ? 'active' : '' ?> ms-2 me-2 mb-2"><?= $i ?></a>
    <?php endfor; ?>
</div>
    <?php elseif ($section == 'users'): ?>
<?php
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$userPagination = getPaginatedData($conn,'users', $limit, $page);
?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Логин</th>
            <th>Email</th>
            <th>Роль</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($userPagination['data'] as $user): ?>
            <tr>
                <td><?= $user['ID'] ?></td>
                <td><?= htmlspecialchars($user['Login']) ?></td>
                <td><?= htmlspecialchars($user['Email']) ?></td>
                <td><?= $user['is_admin'] ? '<p class="text-danger fw-bold">Администратор</p>' : '<p class="text-success fw-bold">Пользователь</p>' ?></td>
                <td>
                    <?php if ($user['is_active'] == 'active'): ?>
                        <p class="text-success">Активен</p>
                    <?php else: ?>
                        <p class="text-danger">Не активен</p>
                    <?php endif; ?> 
                </td>
                <td>
                    <form action="" method="POST">
                        <input type="hidden" name="id" value="<?= $user['ID'] ?>">
                        <input type="hidden" name="category" value="recovery">
                        <button type="submit" class="btn btn-primary">Редактировать</button>
                    </form>
                    <?php if ($user['is_active'] == 'active'): ?>
                        <form action="edit_user.php" method="POST">
                            <input type="hidden" name="id" value="<?= $user['ID'] ?>">
                            <input type="hidden" name="category" value="delete">
                            <button type="submit" class="btn btn-danger mt-2">Удалить</button>
                        </form>
                    <?php else: ?>
                        <form action="edit_user.php" method="POST">
                            <input type="hidden" name="id" value="<?= $user['ID'] ?>">
                            <input type="hidden" name="category" value="recovery">
                            <button type="submit" class="btn btn-success mt-2">Восстановить</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="pagination">
    <?php for ($i = 1; $i <= $userPagination['pages']; $i++): ?>
        <a href="?section=users&page=<?= $i ?>" class="btn btn-primary <?= ($page == $i) ? 'active' : '' ?> ms-2 me-2 mb-2"><?= $i ?></a>
    <?php endfor; ?>
</div>
    <?php elseif ($section == 'orders'): ?>
        <?php
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$ordersPagination = getPaginatedData($conn, 'orders', $limit, $page);
?>
        <p>Заказы</p>   
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Пользователь</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Дата</th>
                    <th>Товары</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach ($ordersPagination['data'] as $order): 
                    $username = $conn->query("SELECT Login FROM users WHERE ID = '$order[user_id]'")->fetch_assoc()['Login'];
            ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= $order['name'] ?></td>
                    <td><?= $username." (ID: ".$order['user_id'].")"?></td>
                    <td><?= $order['phone'] ?></td>
                    <td><?= $order['email'] ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($order['order_date'])) ?></td>
                    <td>
                    <?php
                        $order_products = $conn->query("SELECT * FROM products ORDER BY id")->fetch_all(MYSQLI_ASSOC);
                        $count = 0;
                        foreach ($order_products as $product):
                            if (strpos($order['product_id'], $product['id']) !== false) {
                                $count++;
                                if ($count == 1) {
                                    echo $product['title'];
                                } else {
                                    echo ",  " . $product['title'];
                                }
                            }
                        endforeach;
                    ?>
                    </td>
                    <td>
                        <form action="delete_order.php" method="POST">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <button type="submit" class="btn btn-danger">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination">
    <?php for ($i = 1; $i <= $ordersPagination['pages']; $i++): ?>
        <a href="?section=orders&page=<?= $i ?>" class="btn btn-primary <?= ($page == $i) ? 'active' : '' ?> ms-2 me-2 mb-2"><?= $i ?></a>
    <?php endfor; ?>
</div>
    <?php elseif ($section == 'categories'): ?>
        <p>Категории</p>
        <table class="table">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody> 
            <?php   
                $result = $conn->query("SELECT * FROM categories");
                foreach ($result as $category): 
            ?>
                <tr>
                    <td><?= $category['name'] ?></td>
                    <td>
                        <?php if ($category['is_active'] == 'active'): ?>
                        <form action="edit_category.php" method="POST">
                            <input type="hidden" name="id" value="<?= $category['id'] ?>">
                            <input type="hidden" name="category" value="delete">
                            <button type="submit" class="btn btn-danger mt-2">Удалить</button>
                        </form>
                        <?php else: ?>
                            <form action="edit_category.php" method="POST">
                            <input type="hidden" name="id" value="<?= $category['id'] ?>">
                            <input type="hidden" name="category" value="recovery">
                            <button type="submit" class="btn btn-success mt-2">Восстановить</button>
                        </form>  
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
    <?php elseif($section == 'add_products'): ?>
    <?php
    $limit = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $orderesPagination = getPaginatedData($conn, 'adds_tovar', $limit, $page);
    ?>
        <p>История пополнений</p>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID товара</th>
                    <th>Название</th>
                    <th>Количество</th>
                    <th>Пользователь</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $result = $conn->query("SELECT * FROM adds_tovar");
                foreach ($orderesPagination['data'] as $order): 
                $username = $conn->query("SELECT Login FROM users WHERE ID = '$order[user_id]'")->fetch_assoc()['Login'];
            ?>
                <tr>
                    <td><?= $order['ID'] ?></td>
                    <td><?= $order['tovar_id'] ?></td>
                    <td style="display: flex; align-items: center;">
                        <?php 
                            $product = $conn->query("SELECT title, image_path FROM products WHERE id = '$order[tovar_id]'")->fetch_assoc();
                            echo $product['title'].'<img src="'.'../'.$product['image_path'].'" width="100" style="margin-left: auto;">';
                        ?>
                    </td>
                    <td><?= $order['Count'] ?></td>
                    <td><?= $username." (ID: ".$order['user_id'].")" ?></td>
                    <td><?= $order['date'] ?></td>
                </tr>   
            <?php endforeach; ?>
            </tbody>
        </table>    
        <?php for ($i = 1; $i <= $orderesPagination['pages']; $i++): ?>
        <a href="?section=add_products&page=<?= $i ?>" class="btn btn-primary <?= ($page == $i) ? 'active' : '' ?> ms-2 me-2 mb-2"><?= $i ?></a>
        <?php endfor; ?>
        <?php elseif ($section == 'none'): ?>
        <p>Статистика</p>
        <div class="d-flex justify-content-center">
            <h2>Статистика</h2>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Количество</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Количество товаров</td>
                    <td><?= $conn->query("SELECT COUNT(*) FROM products")->fetch_assoc()['COUNT(*)'] ?></td>
                </tr>
                <tr>
                    <td>Количество категорий</td>
                    <td><?= $conn->query("SELECT COUNT(*) FROM categories")->fetch_assoc()['COUNT(*)'] ?></td>
                </tr>
                <tr>
                    <td>Количество заказов</td>
                    <td><?= $conn->query("SELECT COUNT(*) FROM orders")->fetch_assoc()['COUNT(*)'] ?></td>
                </tr>
                <tr>
                    <td>Количество пользователей</td>
                    <td><?= $conn->query("SELECT COUNT(*) FROM users")->fetch_assoc()['COUNT(*)'] ?></td>
                </tr>  
                <tr>
                    <td>Количество Администраторов</td> 
                    <td><?= $conn->query("SELECT COUNT(*) FROM users WHERE is_admin = '1'")->fetch_assoc()['COUNT(*)'] ?></td>
                </tr>
            </tbody>
        </table>
    <?php elseif($section == 'adds_tovars'): ?>
    <div class="d-flex justify-content-center">
        <h2>Пополнить товар</h2>
    </div>
    <form action="add_tovar.php" method="POST">
        <select name="tovar_id" class="form-select mb-3">
            <option value="" selected>Выберите товар</option>
            <?php
                $tovars = $conn->query("SELECT * FROM products");
                while($tovar = $tovars->fetch_assoc()) {
                    echo "<option value='" . $tovar['id'] . "'>" . $tovar['title'] . " (Цена: " . $tovar['price'] . ")"; if($tovar['quantity'] == 0) {
                        echo "<p class='text-danger'> (Нет в наличии)</p>"; 
                    } else { 
                        echo "<p style='color: green;'> (В наличии: " . $tovar['quantity'].")</p>"; 
                    } 
                    echo "<p class='text-muted'> (ID: " . $tovar['id'] .")  </p>";
                }
            ?>
        </select>
        <label for="Count" class="form-label">Количество:</label>
        <input type="number" class="form-control" id="Count" name="Count" min="1" required><br>
        <label for="user_id" class="form-label">Пользователь:</label>
        <select name="user_id" class="form-select mb-3">
            <option value="" selected>Выберите пользователя</option>
            <?php
                $users = $conn->query("SELECT * FROM users WHERE is_admin = '1'");
                while($user = $users->fetch_assoc()) {
                    echo "<option value='" . $user['ID'] . "'>" . $user['Login']." (ID: ".$user['ID']. ")</option>";
                }
            ?>
        </select>
        <button type="submit" class="btn btn-primary">Добавить</button>
    </form>
    <?php endif; ?>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="script/script.js"></script>
</body>
</html>