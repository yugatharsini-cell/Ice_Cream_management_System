<?php

require_once "config/db.php";
require_once "config/auth.php";
require_once "config/cart.php";

requireLogin();


// Check whether cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}


// Get product IDs from cart
$ids = array_keys($_SESSION['cart']);

$ph = implode(
    ',',
    array_fill(0, count($ids), '?')
);


// Get available products
$st = $pdo->prepare("
    SELECT *
    FROM products
    WHERE id IN ($ph)
      AND availability = 1
");

$st->execute($ids);


// Calculate total
$total = 0;

$items = [];

foreach ($st as $p) {

    $qty = (int)($_SESSION['cart'][$p['id']] ?? 0);

    if ($qty <= 0) {
        continue;
    }


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

    $total += (float)$p['price'] * $qty;

    $items[] = $p;
}


// Make sure products exist
if (empty($items)) {

    die("No valid products found in your cart.");
}


$error = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pdo->beginTransaction();

    try {

       

        $code =
            'ORD-' .
            date('YmdHis') .
            '-' .
            random_int(100, 999);


        $st = $pdo->prepare("
            INSERT INTO orders
            (
                order_code,
                customer_id,
                total,
                status
            )
            VALUES
            (
                ?,
                ?,
                ?,
                'Pending'
            )
        ");


        $st->execute([
            $code,
            $_SESSION['user']['id'],
            $total
        ]);


        $orderId = $pdo->lastInsertId();


        $oi = $pdo->prepare("
            INSERT INTO order_items
            (
                order_id,
                product_id,
                quantity,
                price
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )
        ");


        foreach ($items as $p) {

            $oi->execute([
                $orderId,
                $p['id'],
                $p['qty'],
                $p['price']
            ]);
        }


        $payment = $pdo->prepare("
            INSERT INTO payments
            (
                order_id,
                payment_method,
                amount,
                payment_status
            )
            VALUES
            (
                ?,
                ?,
                ?,
                'Pending'
            )
        ");


        $payment->execute([
            $orderId,
            'PayHere',
            $total
        ]);



        $pdo->commit();


        $_SESSION['cart'] = [];



        header(
            "Location: payment.php?order_id=" .
            $orderId
        );

        exit;


    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }


        $error =
            "Could not create order. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Checkout</title>

    <link
        rel="stylesheet"
        href="assets/style.css"
    >

    <style>
        /* Logged-in user name in navbar */

        .nav-user {
            font-weight: bold;
            margin: 0 10px;
        }
    </style>

</head>


<body>


<nav class="navbar">

    <div class="logo">
        🍦 Ice Cream Shop
    </div>


    <div class="nav-links">

        <a href="cart.php">
            Back to Cart
        </a>

        <span class="nav-user">
            Hi, <?= e($_SESSION['user']['name']) ?>
        </span>

        <a href="logout.php">
            Logout
        </a>

    </div>

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
        Online payment is required to complete your order.
    </p>


    <p>
        After placing the order, you will be redirected
        to the PayHere secure payment page.
    </p>


    <form method="post">

        <button
            class="btn"
            type="submit"
        >
            Place Order & Continue to Payment
        </button>

    </form>


</div>


</body>

</html>