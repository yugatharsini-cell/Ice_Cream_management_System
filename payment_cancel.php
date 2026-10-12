<?php

require_once "config/db.php";
require_once "config/auth.php";

requireLogin();

$orderId = (int)($_GET['order_id'] ?? 0);

if ($orderId <= 0) {
    die("Invalid order.");
}

$stmt = $pdo->prepare("
    SELECT o.id, o.order_code, p.id AS payment_id
    FROM orders o
    JOIN payments p ON p.order_id = o.id
    WHERE o.id = ?
      AND o.customer_id = ?
");
$stmt->execute([$orderId, $_SESSION['user']['id']]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found.");
}

$update = $pdo->prepare("
    UPDATE payments
    SET payment_status = 'Cancelled'
    WHERE id = ?
      AND payment_status = 'Pending'
");
$update->execute([$order['payment_id']]);

header("Location: payment_return.php?order_id=" . $orderId);
exit;
