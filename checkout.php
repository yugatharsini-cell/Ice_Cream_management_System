<?php

require_once "config/db.php";
require_once "config/auth.php";
require_once "config/cart.php";

requireLogin();

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$ids = array_keys($_SESSION['cart']);

$ph = implode(',', array_fill(0, count($ids), '?'));

$st = $pdo->prepare("
    SELECT *
    FROM products
    WHERE id IN ($ph)
    AND availability = 1
");

$st->execute($ids);

$total = 0;
$items = [];

foreach ($st as $p) {

    $qty = (int)$_SESSION['cart'][$p['id']];

    // Check stock before creating order
    if ($qty > (int)$p['stock_quantity']) {
        die(
            "Sorry, only " .
            (int)$p['stock_quantity'] .
            " stock available for " .
            htmlspecialchars($p['name'])
        );
    }

    $p['qty'] = $qty;

    $total += $p['price'] * $qty;

    $items[] = $p;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pdo->beginTransaction();

    try {

        /*
        ---------------------------------------
        1. Create Order
        ---------------------------------------
        */

        $code = 'ORD-' . date('YmdHis') . '-' . random_int(100, 999);

        $st = $pdo->prepare("
            INSERT INTO orders
            (order_code, customer_id, total, status)
            VALUES (?, ?, ?, 'Pending')
        ");

        $st->execute([
            $code,
            $_SESSION['user']['id'],
            $total
        ]);

        $orderId = $pdo->lastInsertId();


        /*
        ---------------------------------------
        2. Insert Order Items
        ---------------------------------------
        */

        $oi = $pdo->prepare("
            INSERT INTO order_items
            (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");


        /*
        ---------------------------------------
        3. Reduce Stock Quantity
        ---------------------------------------
        */

        $updateStock = $pdo->prepare("
            UPDATE products
            SET stock_quantity = stock_quantity - ?
            WHERE id = ?
            AND stock_quantity >= ?
        ");


        foreach ($items as $p) {

            // Add item to order
            $oi->execute([
                $orderId,
                $p['id'],
                $p['qty'],
                $p['price']
            ]);


            // Reduce product stock
            $updateStock->execute([
                $p['qty'],
                $p['id'],
                $p['qty']
            ]);


            // Make sure stock was actually reduced
            if ($updateStock->rowCount() !== 1) {
                throw new Exception(
                    "Not enough stock for product: " . $p['name']
                );
            }
        }


        /*
        ---------------------------------------
        4. Create Payment Record
        ---------------------------------------
        */

        $payment = $pdo->prepare("
            INSERT INTO payments
            (order_id, payment_method, amount, payment_status)
            VALUES (?, ?, ?, 'Pending')
        ");

        $payment->execute([
            $orderId,
            'Demo Online Payment',
            $total
        ]);


        /*
        ---------------------------------------
        5. Complete Transaction
        ---------------------------------------
        */

        $pdo->commit();

        // Clear shopping cart
        $_SESSION['cart'] = [];

        // Go to payment page
        header("Location: payment.php?order_id=" . $orderId);
        exit;

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $error = "Could not create order. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout</title>

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

<nav class="navbar">

    <div class="logo">
        🍦 Ice Cream Shop
    </div>

    <a href="cart.php">
        Back to Cart
    </a>

</nav>


<div class="form-card">

    <h2>Checkout</h2>


    <?php if ($error): ?>

        <div class="error">
            <?= e($error) ?>
        </div>

    <?php endif; ?>


    <p>
        Total:
        <strong>
            Rs. <?= number_format($total, 2) ?>
        </strong>
    </p>


    <p>
        Online payment is required to complete this Version 1.0 checkout flow.
    </p>


    <form method="post">

        <button class="btn" type="submit">
            Place Order & Continue to Payment
        </button>

    </form>

</div>

</body>

</html>