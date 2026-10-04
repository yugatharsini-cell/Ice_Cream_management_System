<?php

require_once "config/db.php";
require_once "config/auth.php";

requireLogin();


// Get order ID
$orderId = (int)($_POST['order_id'] ?? 0);

if ($orderId <= 0) {
    die("Invalid order.");
}


// Get order and payment details
$stmt = $pdo->prepare("
    SELECT
        o.id,
        o.order_code,
        o.total,
        o.status,
        o.customer_id,
        p.id AS payment_id,
        p.payment_status
    FROM orders o
    JOIN payments p
        ON p.order_id = o.id
    WHERE o.id = ?
      AND o.customer_id = ?
");

$stmt->execute([
    $orderId,
    $_SESSION['user']['id']
]);

$order = $stmt->fetch();

if (!$order) {
    die("Order not found.");
}


// Prevent duplicate payment
if ($order['payment_status'] === 'Successful') {

    header(
        "Location: payment_return.php?order_id=" . $orderId
    );

    exit;
}


// Get payment details
$cardName   = trim($_POST['card_name'] ?? '');
$cardNumber = trim($_POST['card_number'] ?? '');
$expiry     = trim($_POST['expiry'] ?? '');
$cvv        = trim($_POST['cvv'] ?? '');


// Validate payment fields
if (
    $cardName === '' ||
    $cardNumber === '' ||
    $expiry === '' ||
    $cvv === ''
) {
    die("Please fill all payment details.");
}


// Start transaction
$pdo->beginTransaction();

try {

    // Get products in this order
    $stmt = $pdo->prepare("
        SELECT
            oi.product_id,
            oi.quantity,
            p.stock_quantity,
            p.name
        FROM order_items oi
        JOIN products p
            ON p.id = oi.product_id
        WHERE oi.order_id = ?
        FOR UPDATE
    ");

    $stmt->execute([$orderId]);

    $items = $stmt->fetchAll();


    if (!$items) {
        throw new Exception(
            "No products found for this order."
        );
    }


    // Check stock
    foreach ($items as $item) {

        if (
            (int)$item['stock_quantity'] <
            (int)$item['quantity']
        ) {

            throw new Exception(
                "Insufficient stock for " .
                $item['name']
            );
        }
    }


    // Reduce stock
    foreach ($items as $item) {

        $stmt = $pdo->prepare("
            UPDATE products
            SET stock_quantity = stock_quantity - ?
            WHERE id = ?
        ");

        $stmt->execute([
            $item['quantity'],
            $item['product_id']
        ]);
    }


    // Create demo transaction reference
    $transactionReference =
        'DEMO-' .
        date('YmdHis') .
        '-' .
        $orderId;


    // Update payment
    $stmt = $pdo->prepare("
        UPDATE payments
        SET
            payment_status = 'Successful',
            transaction_reference = ?,
            payment_datetime = NOW()
        WHERE id = ?
    ");

    $stmt->execute([
        $transactionReference,
        $order['payment_id']
    ]);


    // Confirm order
    $stmt = $pdo->prepare("
        UPDATE orders
        SET status = 'Confirmed'
        WHERE id = ?
    ");

    $stmt->execute([
        $orderId
    ]);


    // Save all database changes
    $pdo->commit();


    // Go to payment success page
    header(
        "Location: payment_return.php?order_id=" . $orderId
    );

    exit;


} catch (Exception $e) {

    // Undo database changes if something fails
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die(
        "Payment failed: " .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
    );
}

?>