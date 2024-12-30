<?php
session_start();
require_once ('../database/db.php');
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}   
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="icons/logotype.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/edit.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Редактирование товара</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="main.php">«ИнфоСофт»</a>
    </div>
</nav>
    <h1>Редактирование товара</h1>  
    <form action="edits.php" method="post" enctype="multipart/form-data" class="edit-form">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    <label for="title">Название:</label>
    <input type="text" id="title" name="title" placeholder="Название" value="<?= htmlspecialchars($product['title']); ?>"><br>
    <label for="description">Описание:</label>
    <input type="text" id="description" name="description" placeholder="Описание" value="<?= htmlspecialchars($product['description']); ?>"><br>
    <label for="price">Цена:</label>
    <input type="number" id="price" step="0.01" name="price" placeholder="Цена" value="<?= htmlspecialchars($product['price']); ?>"><br>
    <label for="count">Количество:</label>
    <input type="number" id="count" step="1" name="count" placeholder="Количество" value="<?= htmlspecialchars($product['quantity']); ?>"><br>
    <label for="category">Категория:</label>
    <select id="category" name="category">
        <?php
            $categories = $conn->query("SELECT * FROM categories");
            foreach ($categories as $category) {
                $selected = ($category['name'] == $product['category']) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($category['name']) . "' $selected>" . htmlspecialchars($category['name']) . "</option>";
            }
        ?>
    </select><br>
    <label for="version">Версия:</label>
    <input type="text" id="version" name="version" placeholder="Версия" value="<?= htmlspecialchars($product['version']); ?>"><br>
    <label for="license_type">Тип лицензии:</label>
    <input type="text" id="license_type" name="license_type" placeholder="Тип лицензии" value="<?= htmlspecialchars($product['license_type']); ?>"><br>
    <label for="image">Текущее изображение:</label><br>
    <img src="<?= htmlspecialchars($product['image_path']); ?>" alt="<?= htmlspecialchars($product['title']); ?>" width="100" id="image"><br>    
    <input type="submit" value="Сохранить">
</form>
</body>
</html>