<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once ('../database/db.php');
require_once('../tcpdf/tcpdf.php');
date_default_timezone_set('Europe/Moscow');

if (isset($_POST['name'], $_POST['email'], $_POST['product_ids'], $_POST['phone'], $_SESSION['user_id'])) {    
    $login = $_SESSION['user_login'];  
    $user_id = $_SESSION['user_id'];
    $name = htmlspecialchars($_POST['name']); 
    $phone = htmlspecialchars($_POST['phone']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $product_ids = htmlspecialchars($_POST['product_ids']);
    
    // Получаем общий итог заказа
    $total_amount = 0;
    $product_ids_array = explode(",", $product_ids);
    $products = []; 
    
    // Проверка баланса пользователя
    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $balance = $user['balance'];
    
    // Получение цены товаров и расчет суммы
    foreach ($product_ids_array as $product_id) {
        $stmt = $conn->prepare("SELECT title, price FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product_data = $stmt->get_result()->fetch_assoc();
        
        if ($product_data) {
            $products[] = $product_data;
            $total_amount += $product_data['price'];
        } else {
            echo "<script>alert('Товар с ID $product_id не найден'); window.location.href = 'buylist.php';</script>";
            exit;
        }
    }
    
    // Если у пользователя недостаточно средств
    if ($balance < $total_amount) {
        echo "<script>alert('Недостаточно средств на балансе для выполнения заказа.'); window.location.href = 'buylist.php';</script>";
        exit;
    }
    
    // Начинаем транзакцию
    $conn->begin_transaction();
    try {
        // Вставляем заказ в таблицу
        $stmt = $conn->prepare("INSERT INTO orders (user_id, name, phone, email, product_id, order_date, total_amount) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->bind_param("issssi", $user_id, $name, $phone, $email, $product_ids, $total_amount);
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Обновляем количество товаров и снимаем деньги с баланса
        foreach ($product_ids_array as $product_id) {
            $stmt = $conn->prepare("UPDATE products SET quantity = quantity - 1 WHERE id = ? AND quantity > 0");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $product_data = $conn->query("SELECT title FROM products WHERE id = $product_id")->fetch_assoc();
            
            if ($stmt->affected_rows == 0) {
                throw new Exception("Товар {$product_data['title']} недоступен или уже распродан.");
            }
        }

        // Снижаем баланс пользователя
        $new_balance = $balance - $total_amount;
        $stmt = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
        $stmt->bind_param("di", $new_balance, $user_id);
        $stmt->execute();

        // Завершаем транзакцию
        $conn->commit();

        // Очищаем корзину
        unset($_SESSION['cart']);
        
        // Генерация PDF чека
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 12);
        $pdf->Ln(20);
        $pdf->SetFont('dejavusans', 'B', 16);
        $pdf->Cell(0, 10, 'Чек на заказ #' . $order_id, 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('dejavusans', '', 12);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(0, 10, "Информация о клиенте:", 0, 1, 'L', true);
        $pdf->Ln(2);
        $pdf->MultiCell(0, 10, "Имя клиента: $name", 0, 'L', false);
        $pdf->MultiCell(0, 10, "Логин на сайте: $login", 0, 'L', false);
        $pdf->MultiCell(0, 10, "Телефон: $phone", 0, 'L', false);
        $pdf->MultiCell(0, 10, "Email: $email", 0, 'L', false);
        $pdf->MultiCell(0, 10, "Дата заказа: " . date('d.m.Y H:i:s'), 0, 'L', false);
        $pdf->Ln(10);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(80, 10, 'Название товара', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Цена', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Количество', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Итого', 1, 1, 'C', true);
        $total = 0;
        foreach ($products as $product) {
            $title = $product['title'];
            $price = $product['price'];
            $quantity = 1;
            $subtotal = $price * $quantity;
            $total += $subtotal;

            $pdf->MultiCell(80, 10, $title, 1, 'L', false, 0);
            $pdf->Cell(40, 10, "$price руб.", 1, 0, 'R');
            $pdf->Cell(40, 10, $quantity, 1, 0, 'C');
            $pdf->Cell(30, 10, "$subtotal руб.", 1, 1, 'R');
        }
        $pdf->Ln(10);
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(160, 10, 'Итого:', 0, 0, 'R');
        $pdf->Cell(30, 10, "$total руб.", 1, 1, 'R');
        $pdf->Ln(10);
        $pdf->SetFont('dejavusans', '', 12);
        $pdf->Cell(0, 10, 'Спасибо за ваш заказ!', 0, 1, 'C');
        $pdf->Ln(10);  
        $pdf->SetFont('dejavusans', 'I', 10);
        $pdf->Cell(0, 10, 'Этот чек является подтверждением вашего заказа.', 0, 1, 'C');
        $pdf->Ln(20);  
        $pdf->SetFont('dejavusans', 'B', 14);
        $pdf->Cell(0, 10, 'ООО "Enigma-Hub"', 0, 1, 'C'); 
        $pdf->SetFont('dejavusans', '', 12);
        $pdf->SetXY(80, $pdf->GetY() + 10);
        $pdf->Cell(50, 20, 'Enigma', 1, 0, 'C');
        $file_name = "check_order_$order_id.pdf";
        $file_path = dirname(__DIR__) . '/temp/check_order_' . $order_id . '.pdf';
        $pdf->Output($file_path, 'F');
        if (file_exists($file_path)) {
            $_SESSION['pdf_path'] = $file_path;
            header("Location: success.php?order_id=$order_id");
            exit;
        } else {
            echo "<script>alert('Ошибка: Файл не найден.'); window.location.href = 'buylist.php';</script>";
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Ошибка: " . $e->getMessage() . "'); window.location.href = 'buylist.php';</script>";
    }
} else {
    echo "<script>alert('Ошибка: Не все данные заполнены!'); window.location.href = 'buylist.php';</script>";
}
?>
