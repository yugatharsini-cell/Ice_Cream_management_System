<?php

require_once "config/db.php";
require_once "config/auth.php";

requireLogin();


// Get order ID
$orderId = (int)($_GET['order_id'] ?? 0);

if ($orderId <= 0) {
    die("Invalid order.");
}


// Get order + customer details
$stmt = $pdo->prepare("
    SELECT
        o.*,
        p.id AS payment_id,
        p.payment_status,
        u.name AS customer_name,
        u.email AS customer_email
    FROM orders o
    JOIN payments p
        ON p.order_id = o.id
    JOIN users u
        ON u.id = o.customer_id
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


// If payment is already successful
if ($order['payment_status'] === 'Successful') {
    header(
        "Location: payment_return.php?order_id=" .
        $orderId
    );
    exit;
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

    <title>Online Payment</title>

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

    <h2>Online Payment</h2>


    <p>

        Order:

        <strong>
            <?= e($order['order_code']) ?>
        </strong>

    </p>


    <p>

        Amount:

        <strong>
            Rs.
            <?= number_format(
                $order['total'],
                2
            ) ?>
        </strong>

    </p>


    <hr>


    <h3>Payment Details</h3>


    <form
        method="post"
        action="payment_process.php"
    >


        <input
            type="hidden"
            name="order_id"
            value="<?= e($orderId) ?>"
        >


        <label>
            Card Holder Name
        </label>

        <input
            type="text"
            name="card_name"
            placeholder="Enter card holder name"
            required
        >


        <label>
            Card Number
        </label>

        <input
            type="text"
            name="card_number"
            placeholder="4111 1111 1111 1111"
            maxlength="19"
            required
        >


        <label>
            Expiry Date
        </label>

        <input
            type="text"
            name="expiry"
            placeholder="MM/YY"
            maxlength="5"
            required
        >


        <label>
            CVV
        </label>

        <input
            type="password"
            name="cvv"
            placeholder="123"
            maxlength="3"
            required
        >


        <br>


        <button
            type="submit"
            class="btn"
        >
            Pay Rs. <?= number_format(
                $order['total'],
                2
            ) ?>
        </button>


    </form>


    <p style="margin-top: 15px; font-size: 14px;">

        <strong>Demo Payment</strong>

        <br>

        This is a simulated online payment
        for the academic project.

    </p>


</div>


</body>

</html>