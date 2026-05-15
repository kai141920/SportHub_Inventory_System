<?php
session_start();
include '../config/db.php';

if(isset($_POST['buy'])){
    $equipment_id = $_POST['equipment_id'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("SELECT * FROM equipments WHERE id=?");
    $stmt->execute([$equipment_id]);

    $product = $stmt->fetch();

    if($product['stock'] < $quantity){
        die("Insufficient stock");
    }

    $subtotal = $product['price'] * $quantity;

    $stmt = $conn->prepare("INSERT INTO orders(user_id,total_amount,payment_method,payment_status) VALUES(?,?,?,?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $subtotal,
        'Card',
        'Paid'
    ]);

    $order_id = $conn->lastInsertId();

    $stmt = $conn->prepare("INSERT INTO order_items(order_id,equipment_id,quantity,subtotal) VALUES(?,?,?,?)");
    $stmt->execute([
        $order_id,
        $equipment_id,
        $quantity,
        $subtotal
    ]);

    $newStock = $product['stock'] - $quantity;

    $stmt = $conn->prepare("UPDATE equipments SET stock=? WHERE id=?");
    $stmt->execute([$newStock,$equipment_id]);

    echo "<h2>Purchase Successful!</h2>";
    echo "<a href='shop.php'>Back to Shop</a>";
}
?>
