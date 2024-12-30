<?php
session_start();
require_once('database/db.php');

if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE title LIKE ?");
    $stmt->bind_param("s", "%$search%");
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    echo json_encode($products);
}
?>