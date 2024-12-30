<?php
session_start();
require_once ('../database/db.php');
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header('Location: main.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" href="icons/logotype.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Админ панель</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="admin.php?section=none">1С Магазин</a>
    </div>
</nav>
    <h1>Добавить товар</h1>
    <form action="adds.php" method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Название" required><br>
        <input type="text" name="description" placeholder="Описание" required><br>
        <input type="number" step="0.01" name="price" placeholder="Цена" required><br>
        <select name="category"> 
        <?php
            $categories = $conn->query("SELECT * FROM categories");
            foreach ($categories as $category) {
                echo "<option value='" . $category['name'] . "'>" . $category['name'] . "</option>";
            }
        ?>    
        </select> 
        <br>
        <input type="text" name="version" placeholder="Версия"><br>
        <input type="text" name="license_type" placeholder="Тип лицензии"><br>
        <input type="file" name="image" required><br>
        <input type="submit" value="Добавить">
    </form>
</body>
</html>