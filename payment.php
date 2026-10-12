<?php

require_once "config/db.php";
require_once "config/auth.php";
require_once "config/payhere.php";

requireLogin();

$orderId = (int)($_GET['order_id'] ?? $_POST['order_id'] ?? 0);

if ($orderId <= 0) {
    die("Invalid order.");
}

// Get order + payment + customer details.
$stmt = $pdo->prepare("
    SELECT
        o.*,
        p.id AS payment_id,
        p.payment_status,
        u.name AS customer_name,
        u.email AS customer_email
    FROM orders o
    JOIN payments p ON p.order_id = o.id
    JOIN users u ON u.id = o.customer_id
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

if ($order['payment_status'] === 'Successful') {
    header("Location: payment_return.php?order_id=" . $orderId);
    exit;
}

$error = '';
$phone = trim($_POST['phone'] ?? '0771234567');
$address = trim($_POST['address'] ?? 'Jaffna');
$city = trim($_POST['city'] ?? 'Jaffna');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        $phone === '' ||
        $address === '' ||
        $city === ''
    ) {
        $error = 'Please enter your phone number, address and city.';
    } elseif (
        $payhere['merchant_id'] === 'YOUR_SANDBOX_MERCHANT_ID' ||
        $payhere['merchant_secret'] === 'YOUR_SANDBOX_MERCHANT_SECRET'
    ) {
        $error = 'PayHere Sandbox credentials are not configured yet. Open config/payhere.php and add your Merchant ID and Merchant Secret.';
    } elseif (strpos($payhere['notify_url'], 'YOUR-PUBLIC-URL') !== false) {
        $error = 'A public notify URL is required. Update notify_url in config/payhere.php before testing PayHere.';
    } else {
        // Split the registered name into first/last name for PayHere.
        $nameParts = preg_split('/\s+/', trim($order['customer_name']));
        $firstName = $nameParts[0] ?? 'Customer';
        $lastName = count($nameParts) > 1
            ? implode(' ', array_slice($nameParts, 1))
            : 'Customer';

        $itemsStmt = $pdo->prepare("
            SELECT
                oi.quantity,
                oi.price,
                pr.name
            FROM order_items oi
            JOIN products pr ON pr.id = oi.product_id
            WHERE oi.order_id = ?
            ORDER BY oi.id
        ");
        $itemsStmt->execute([$orderId]);
        $orderItems = $itemsStmt->fetchAll();

        $itemNames = [];
        foreach ($orderItems as $item) {
            $itemNames[] = $item['name'] . ' x ' . (int)$item['quantity'];
        }

        $orderIdForPayHere = (string)$order['order_code'];
        $amount = (float)$order['total'];
        $currency = $payhere['currency'];

        $hash = generate_payhere_hash(
            $payhere['merchant_id'],
            $orderIdForPayHere,
            $amount,
            $currency,
            $payhere['merchant_secret']
        );

        // Save the selected PayHere method before redirecting.
        $updatePayment = $pdo->prepare("
            UPDATE payments
            SET payment_method = 'PayHere Sandbox'
            WHERE id = ?
        ");
        $updatePayment->execute([$order['payment_id']]);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Redirecting to PayHere</title>
            <link rel="stylesheet" href="assets/style.css">
        </head>
        <body>
            <div class="form-card" style="text-align:center;">
                <h2>Redirecting to PayHere</h2>
                <p>Please wait while we open the PayHere Sandbox payment page.</p>
                <form id="payhereForm" method="post" action="<?= e($payhere['checkout_url']) ?>">
                    <input type="hidden" name="sandbox" value="true">
                    <input type="hidden" name="merchant_id" value="<?= e($payhere['merchant_id']) ?>">
                    <input type="hidden" name="return_url" value="<?= e($payhere['return_url'] . '?order_id=' . $orderId) ?>">
                    <input type="hidden" name="cancel_url" value="<?= e($payhere['cancel_url'] . '?order_id=' . $orderId) ?>">
                    <input type="hidden" name="notify_url" value="<?= e($payhere['notify_url']) ?>">
                    <input type="hidden" name="first_name" value="<?= e($firstName) ?>">
                    <input type="hidden" name="last_name" value="<?= e($lastName) ?>">
                    <input type="hidden" name="email" value="<?= e($order['customer_email']) ?>">
                    <input type="hidden" name="phone" value="<?= e($phone) ?>">
                    <input type="hidden" name="address" value="<?= e($address) ?>">
                    <input type="hidden" name="city" value="<?= e($city) ?>">
                    <input type="hidden" name="country" value="Sri Lanka">
                    <input type="hidden" name="order_id" value="<?= e($orderIdForPayHere) ?>">
                    <input type="hidden" name="items" value="<?= e(implode(', ', $itemNames)) ?>">
                    <input type="hidden" name="currency" value="<?= e($currency) ?>">
                    <input type="hidden" name="amount" value="<?= number_format($amount, 2, '.', '') ?>">
                    <input type="hidden" name="hash" value="<?= e($hash) ?>">
                    <input type="hidden" name="delivery_address" value="<?= e($address) ?>">
                    <input type="hidden" name="delivery_city" value="<?= e($city) ?>">
                    <input type="hidden" name="delivery_country" value="Sri Lanka">
                    <button type="submit" class="btn">Continue to PayHere Sandbox</button>
                </form>
                <script>document.getElementById('payhereForm').submit();</script>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayHere Sandbox Payment</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">🍦 Ice Cream Shop</div>
    <a href="customer/orders.php">My Orders</a>
</nav>

<div class="form-card">
    <h2>PayHere Sandbox Payment</h2>

    <?php if ($error): ?>
        <div class="error"><?= e($error) ?></div>
    <?php endif; ?>

    <p>Order: <strong><?= e($order['order_code']) ?></strong></p>
    <p>Amount: <strong>Rs. <?= number_format($order['total'], 2) ?></strong></p>

    <p>
        This project uses <strong>PayHere Sandbox</strong> for testing.
        No real money is charged in the Sandbox environment.
    </p>

    <form method="post">
        <input type="hidden" name="order_id" value="<?= $orderId ?>">

        <label>Phone Number</label>
        <input type="text" name="phone" value="<?= e($phone) ?>" required>

        <label>Address</label>
        <input type="text" name="address" value="<?= e($address) ?>" required>

        <label>City</label>
        <input type="text" name="city" value="<?= e($city) ?>" required>

        <br>

        <button type="submit" class="btn">
            Continue to PayHere Sandbox
        </button>
    </form>

    <p style="margin-top:15px;font-size:14px;">
        You will enter the test card details on the PayHere Sandbox page,
        not on this website.
    </p>
</div>

</body>
</html>
