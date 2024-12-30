<?php
   session_start();
   require_once ('../database/db.php');
   if (isset($_POST['id']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['price']) && isset($_POST['category']) && isset($_POST['count']) && isset($_POST['version']) && isset($_POST['license_type'])) {
       $id = $_POST['id'];
       $title = $_POST['title'];
       $description = $_POST['description'];
       $price = $_POST['price'];
       $category = $_POST['category'];
       $count = $_POST['count'];    
       $stmt = $conn->prepare("UPDATE products SET title = ?, description = ?, price = ?, category = ?, quantity = ?, version = ?, license_type = ? WHERE id = ?");
       $stmt->bind_param("ssssssii", $title, $description, $price, $category, $count, $_POST['version'], $_POST['license_type'], $id);
       $stmt->execute();
       $stmt->close();

       $stmt = $conn->prepare("INSERT INTO adds_tovar (tovar_id, Count, user_id, date) VALUES (?, ?, ?, NOW())");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }
       $stmt->bind_param("iii", $id, $count, $_SESSION['user_id']);

    if (!$stmt->execute()) {
        die("Ошибка выполнения запроса: " . $stmt->error);
    }
       $stmt->close();
       header("Location: admin.php");
       exit();  
   } else {
       header("Location: main.php");
       exit();
   }    
?>