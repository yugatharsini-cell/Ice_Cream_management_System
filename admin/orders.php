<?php
require_once "../config/db.php";
require_once "../config/auth.php";
requireAdmin();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId = (int)$_POST['order_id'];
    $status  = trim($_POST['status']);
    $allowed = ['Pending', 'Confirmed', 'Delivered', 'Cancelled'];
    if (in_array($status, $allowed, true)) {
        $st = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $st->execute([$status, $orderId]);
    }
    header("Location: orders.php");
    exit();
}

$stmt = $pdo->query("
    SELECT 
        o.*,
        u.name AS customer_name,
        u.email AS customer_email,
        p.payment_status
    FROM orders o
    JOIN users u ON u.id = o.customer_id
    LEFT JOIN payments p ON p.order_id = o.id
    ORDER BY o.id DESC
");
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">🍦 Orders Management</div>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="../index.php">Shop</a>
        <a href="../logout.php">Logout</a>
    </div>
</nav>

<div class="container">
    <h1>All Customer Orders</h1>

    <?php if (empty($orders)): ?>
        <div class="card">
            <p>No orders found.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order Code</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><strong><?= e($o['order_code']) ?></strong></td>
                            <td>
                                <?= e($o['customer_name']) ?><br>
                                <small style="color:#a89ea6;"><?= e($o['customer_email']) ?></small>
                            </td>
                            <td><?= e($o['order_date']) ?></td>
                            <td>Rs. <?= number_format($o['total'], 2) ?></td>
                            <td>
                                <?php if (($o['payment_status'] ?? '') === 'Successful'): ?>
                                    <span style="color:#7fe687">Successful</span>
                                <?php else: ?>
                                    <span style="color:#f3be7a"><?= e($o['payment_status'] ?? 'Pending') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge status-<?= strtolower($o['status']) ?>">
                                    <?= e($o['status']) ?>
                                </span>
                            </td>
                            <td>
                                <form method="post" style="display:flex;gap:6px;align-items:center;margin:0;">
                                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                    <select name="status" style="padding:4px 8px;border-radius:6px;background:#2d242c;color:#fff;border:1px solid #483947;">
                                        <option value="Pending" <?= $o['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Confirmed" <?= $o['status'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="Delivered" <?= $o['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="Cancelled" <?= $o['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn" style="padding:4px 10px;font-size:12px;">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
