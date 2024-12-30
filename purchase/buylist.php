<?php
session_start();
require_once ('../database/db.php');
if (!isset($_SESSION['user_auth']) == true) {
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
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Корзина</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../main.php">«ИнфоСофт»</a>
    </div>
</nav>
<div class="container mt-4">
    <h1>Корзина</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Название</th>
                <th>Количество</th>
                <th>Цена</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($_SESSION['cart']) > 0): ?>
                <?php foreach($_SESSION['cart'] as $product_id => $quantity): ?>
                    <?php
                        $product = $conn->query("SELECT * FROM products WHERE id = '$product_id'")->fetch_assoc();
                        $total_price = $product['price'] * $quantity;
                    ?>
                    <tr>
                        <td><?= $product['title'] ?>
                        <img src="<?= '../'.$product['image_path'] ?>" alt="<?= $product['title'] ?>" width="160px" class="ms-1 d-flex" style="margin-left: auto;">
                        </td>
                        <td><input type="number" min="1" max="<?php $max_quantity = $product['quantity']; echo $max_quantity; ?>" value="<?= $quantity ?>" name="quantity"></td>
                        <td><?= $total_price ?></td>
                        <td>
                            <form action="delete_from_cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn btn-danger">Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php elseif(count($_SESSION['cart']) == 0): ?>
                <tr>
                    <td colspan="3">В корзине пока нет товаров</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if (count($_SESSION['cart']) > 0): ?>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkoutModal">Оформить заказ</button>
    <?php endif; ?>
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Оформление заказа</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <?php
                $product_ids = array_keys($_SESSION['cart']); 
                if (count($product_ids) == 1) {
                    $product_ids_str = $product_ids[0];
                } else {
                    $product_ids_str = implode(",", $product_ids); 
                }
                ?>
                <form action="checkout.php" method="POST">
                    <input type="text" name="name" placeholder="Ваше ФИО" required class="form-control mb-2">
                    <input type="tel" name="phone" placeholder="Ваш телефон" required class="form-control mb-2">
                    <input type="email" name="email" placeholder="Ваш email" required class="form-control mb-2">
                    <input type="hidden" name="product_ids" value="<?= $product_ids_str ?>">
                    <button type="submit" class="btn btn-primary">Оформить заказ</button>
                </form>
               </div> 
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>