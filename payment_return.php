<?php

require_once "config/db.php";
require_once "config/auth.php";

requireLogin();


$orderId = (int)($_GET['order_id'] ?? 0);

if ($orderId <= 0) {
    die("Invalid order.");
}


$stmt = $pdo->prepare("
    SELECT
        o.id,
        o.order_code,
        o.total,
        o.status,
        p.payment_status,
        p.transaction_reference,
        p.payment_datetime
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

$paymentStatus = $order['payment_status'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Payment Result</title>

    <link
        rel="stylesheet"
        href="assets/style.css"
    >

</head>

<body>

<nav class="navbar">

    <div class="logo">
        🍦 Ice Cream Shop
    </div>

</nav>


<div class="form-card">

    <?php if ($paymentStatus === 'Successful'): ?>

        <h2>Payment Successful</h2>

        <p>
            Your payment has been completed successfully.
        </p>

        <p>
            Order:
            <strong>
                <?= e($order['order_code']) ?>
            </strong>
        </p>

        <p>
            Amount:
            <strong>
                Rs. <?= number_format($order['total'], 2) ?>
            </strong>
        </p>

        <?php if (!empty($order['transaction_reference'])): ?>

            <p>
                Transaction Reference:
                <strong>
                    <?= e($order['transaction_reference']) ?>
                </strong>
            </p>

        <?php endif; ?>


        <a
            href="customer/order_details.php?id=<?= (int)$order['id'] ?>"
            class="btn"
        >
            View Order
        </a>


    <?php elseif ($paymentStatus === 'Pending'): ?>

        <h2>Payment Pending</h2>

        <p>
            Your payment is currently being processed.
        </p>

        <p>
            Please check your order status again shortly.
        </p>

        <a
            href="customer/order_details.php?id=<?= (int)$order['id'] ?>"
            class="btn"
        >
            View Order
        </a>


    <?php elseif ($paymentStatus === 'Cancelled'): ?>

        <h2>Payment Cancelled</h2>

        <p>
            Your payment was cancelled.
        </p>

        <p>
            You can try the payment again from your order.
        </p>

        <a
            href="customer/order_details.php?id=<?= (int)$order['id'] ?>"
            class="btn"
        >
            View Order
        </a>


    <?php elseif ($paymentStatus === 'Failed'): ?>

        <h2>Payment Failed</h2>

        <p>
            Unfortunately, your payment was not successful.
        </p>

        <p>
            Please try again.
        </p>

        <a
            href="payment.php?order_id=<?= (int)$order['id'] ?>"
            class="btn"
        >
            Try Payment Again
        </a>


    <?php else: ?>

        <h2>Payment Status</h2>

        <p>
            Current payment status:
            <strong>
                <?= e($paymentStatus) ?>
            </strong>
        </p>

        <a
            href="customer/order_details.php?id=<?= (int)$order['id'] ?>"
            class="btn"
        >
            View Order
        </a>

    <?php endif; ?>

</div>

</body>

</html>