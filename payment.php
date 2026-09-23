<?php

require_once "config/db.php";
require_once "config/auth.php";

requireLogin();


// ===============================
// Get Order ID
// ===============================

$orderId = (int) ($_GET['order_id'] ?? 0);


// ===============================
// Get Order & Payment Details
// ===============================

$stmt = $pdo->prepare("
    SELECT
        o.*,
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


// ===============================
// Check Order
// ===============================

if (!$order) {
    die("Order not found.");
}


$message = "";


// ===============================
// Process Payment
// ===============================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $result = $_POST['result'] ?? 'Failed';


    // Allow only valid payment statuses

    $status = in_array(
        $result,
        ['Successful', 'Failed', 'Cancelled'],
        true
    )
        ? $result
        : 'Failed';


    // Generate Transaction Reference

    $ref = null;

    if ($status === 'Successful') {

        $ref =
            'TXN-' .
            date('YmdHis') .
            '-' .
            random_int(1000, 9999);
    }


    // Update Payment

    $stmt = $pdo->prepare("
        UPDATE payments
        SET
            payment_status = ?,
            transaction_reference = ?,
            payment_datetime = NOW()
        WHERE id = ?
    ");

    $stmt->execute([
        $status,
        $ref,
        $order['payment_id']
    ]);


    // Successful Payment

    if ($status === 'Successful') {

        $stmt = $pdo->prepare("
            UPDATE orders
            SET status = 'Confirmed'
            WHERE id = ?
        ");

        $stmt->execute([$orderId]);


        header(
            "Location: customer/order_details.php?id=" . $orderId
        );

        exit;
    }


    // Failed / Cancelled Payment

    $message =
        "Payment $status. You can retry the demo payment.";
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


    <!-- Navigation -->

    <nav class="navbar">

        <div class="logo">
            🍦 Ice Cream Shop
        </div>

    </nav>


    <!-- Payment Form -->

    <div class="form-card">

        <h2>
            Online Payment
        </h2>


        <!-- Order Information -->

        <p>
            Order:
            <?= e($order['order_code']) ?>
        </p>


        <p>

            Amount:

            <strong>
                Rs.
                <?= number_format($order['total'], 2) ?>
            </strong>

        </p>


        <!-- Payment Message -->

        <?php if ($message): ?>

            <div class="error">

                <?= e($message) ?>

            </div>

        <?php endif; ?>


        <!-- Demo Payment Notice -->

        <p>

            This project uses a demo payment process.
            Connect a real gateway only when its merchant
            credentials and API requirements are available.

        </p>


        <!-- Payment Buttons -->

        <form method="post">


            <!-- Successful Payment -->

            <button
                name="result"
                value="Successful"
                class="btn"
                type="submit"
            >
                Pay Successfully
            </button>


            <!-- Failed Payment -->

            <button
                name="result"
                value="Failed"
                class="btn danger"
                type="submit"
            >
                Simulate Failed Payment
            </button>


            <!-- Cancel Payment -->

            <button
                name="result"
                value="Cancelled"
                class="btn light"
                type="submit"
            >
                Cancel
            </button>


        </form>


    </div>


</body>

</html>