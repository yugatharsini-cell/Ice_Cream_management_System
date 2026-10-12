<?php

// PayHere server-to-server payment notification endpoint.
require_once "config/db.php";
require_once "config/payhere.php";

$merchantId = $_POST['merchant_id'] ?? '';
$orderCode = $_POST['order_id'] ?? '';
$payhereAmount = $_POST['payhere_amount'] ?? '';
$payhereCurrency = $_POST['payhere_currency'] ?? '';
$statusCode = (string)($_POST['status_code'] ?? '');
$md5sig = strtoupper($_POST['md5sig'] ?? '');
$paymentId = $_POST['payment_id'] ?? null;
$method = $_POST['method'] ?? null;
$statusMessage = $_POST['status_message'] ?? null;

if (
    $merchantId === '' ||
    $orderCode === '' ||
    $payhereAmount === '' ||
    $payhereCurrency === '' ||
    $statusCode === '' ||
    $md5sig === ''
) {
    http_response_code(400);
    exit('Invalid notification.');
}

// Verify the notification checksum before changing the database.
$localMd5sig = strtoupper(
    md5(
        $merchantId .
        $orderCode .
        $payhereAmount .
        $payhereCurrency .
        $statusCode .
        strtoupper(md5($payhere['merchant_secret']))
    )
);

if (!hash_equals($localMd5sig, $md5sig)) {
    http_response_code(400);
    exit('Invalid checksum.');
}

if ($merchantId !== $payhere['merchant_id']) {
    http_response_code(400);
    exit('Invalid merchant.');
}

// PayHere status codes: 2 success, 0 pending, -1 cancelled, -2 failed, -3 chargeback.
$statusMap = [
    '2'  => 'Successful',
    '0'  => 'Pending',
    '-1' => 'Cancelled',
    '-2' => 'Failed',
    '-3' => 'Failed',
];

$paymentStatus = $statusMap[$statusCode] ?? 'Failed';

try {
    $pdo->beginTransaction();

    // Find the local order using the PayHere order code.
    $stmt = $pdo->prepare("SELECT id, total, status FROM orders WHERE order_code = ? FOR UPDATE");
    $stmt->execute([$orderCode]);
    $order = $stmt->fetch();

    if (!$order) {
        throw new Exception('Order not found.');
    }

    // Verify the amount and currency against the local order before updating it.
    if (number_format((float)$order['total'], 2, '.', '') !== number_format((float)$payhereAmount, 2, '.', '')) {
        throw new Exception('Payment amount does not match the order total.');
    }

    if ($payhereCurrency !== $payhere['currency']) {
        throw new Exception('Payment currency does not match the configured currency.');
    }

    // Lock the matching payment row.
    $stmt = $pdo->prepare("SELECT id, payment_status FROM payments WHERE order_id = ? FOR UPDATE");
    $stmt->execute([$order['id']]);
    $payment = $stmt->fetch();

    if (!$payment) {
        throw new Exception('Payment record not found.');
    }

    // Do not reduce stock twice if PayHere retries the notification.
    if ($paymentStatus === 'Successful' && $payment['payment_status'] !== 'Successful') {
        $itemsStmt = $pdo->prepare("
            SELECT oi.product_id, oi.quantity, p.stock_quantity, p.name
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = ?
            FOR UPDATE
        ");
        $itemsStmt->execute([$order['id']]);
        $items = $itemsStmt->fetchAll();

        foreach ($items as $item) {
            if ((int)$item['stock_quantity'] < (int)$item['quantity']) {
                throw new Exception('Insufficient stock for ' . $item['name']);
            }
        }

        foreach ($items as $item) {
            $stockStmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $stockStmt->execute([(int)$item['quantity'], (int)$item['product_id']]);
        }
    }

    $reference = $paymentId ? 'PAYHERE-' . $paymentId : null;

    $updatePayment = $pdo->prepare("
        UPDATE payments
        SET
            payment_status = ?,
            transaction_reference = COALESCE(?, transaction_reference),
            payment_datetime = CASE WHEN ? = 'Successful' THEN NOW() ELSE payment_datetime END
        WHERE id = ?
    ");
    $updatePayment->execute([
        $paymentStatus,
        $reference,
        $paymentStatus,
        $payment['id']
    ]);

    if ($paymentStatus === 'Successful') {
        $updateOrder = $pdo->prepare("UPDATE orders SET status = 'Confirmed' WHERE id = ?");
        $updateOrder->execute([$order['id']]);
    } elseif ($paymentStatus === 'Cancelled' || $paymentStatus === 'Failed') {
        // Keep the order Pending so the customer can retry the payment.
        $updateOrder = $pdo->prepare("UPDATE orders SET status = 'Pending' WHERE id = ? AND status = 'Pending'");
        $updateOrder->execute([$order['id']]);
    }

    $pdo->commit();
    http_response_code(200);
    echo 'OK';

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo 'Notification processing failed.';
}
