<?php
require 'database/db.php'; 

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM products WHERE id = $id");
    
    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode($row); 
    } else {
        echo json_encode(['error' => 'Продукт не найден']);
    }
} else {
    echo json_encode(['error' => 'ID продукта не указан']);
}    
?>