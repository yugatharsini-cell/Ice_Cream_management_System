<?php

// PayHere Sandbox now handles the payment directly.
// This file is kept only as a compatibility redirect for any old links.
require_once "config/auth.php";
requireLogin();

$orderId = (int)($_GET['order_id'] ?? $_POST['order_id'] ?? 0);

if ($orderId <= 0) {
    die("Invalid order.");
}

header("Location: payment.php?order_id=" . $orderId);
exit;
