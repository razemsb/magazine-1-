<?php
 session_start();
 require_once ('../database/db.php');
 if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['price']) && isset($_POST['category']) && isset($_FILES['image'])) {
     $title = $_POST['title'];
     $description = $_POST['description'];
     $price = $_POST['price'];
     $category = $_POST['category'];
     $image = $_FILES['image'];
     $image_name = $image['name'];
     $image_tmp = $image['tmp_name'];
     $image_path = "icons/" . $image_name;
     move_uploaded_file($image_tmp, $image_path);
     $stmt = $conn->prepare("INSERT INTO products (title, description, price, image_path, category, version, license_type, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
     $stmt->bind_param("sssssss", $title, $description, $price, $image_path, $category, $_POST['version'], $_POST['license_type']);
     $stmt->execute();
     $stmt->close();
     echo "<script>alert('Товар успешно добавлен!'); window.location.href = 'admin.php';</script>";
 } else {       
     echo "<script>alert('Ошибка добавления товара!'); window.location.href = 'admin.php';</script>";
 }  
?>