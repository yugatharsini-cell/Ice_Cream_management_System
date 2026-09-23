<?php

require_once "../config/db.php";
require_once "../config/auth.php";

requireLogin();


// ===============================
// Get Order ID
// ===============================

$id = (int) ($_GET['id'] ?? 0);


// ===============================
// Get Order Details
// ===============================

$stmt = $pdo->prepare("
    SELECT
        o.*,
        p.payment_status,
        p.transaction_reference
    FROM orders o
    JOIN payments p
        ON p.order_id = o.id
    WHERE o.id = ?
      AND o.customer_id = ?
");

$stmt->execute([
    $id,
    $_SESSION['user']['id']
]);

$order = $stmt->fetch();


// ===============================
// Check Order
// ===============================

if (!$order) {
    die("Order not found.");
}


// ===============================
// Get Order Items
// ===============================

$stmt = $pdo->prepare("
    SELECT
        oi.*,
        pr.name,
        pr.image
    FROM order_items oi
    JOIN products pr
        ON pr.id = oi.product_id
    WHERE oi.order_id = ?
");

$stmt->execute([$id]);

$items = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Order Details</title>

    <link
        rel="stylesheet"
        href="../assets/style.css"
    >

</head>

<body>


    <!-- Navigation -->

    <nav class="navbar">

        <div class="logo">
            🍦 Order Details
        </div>

        <a href="orders.php">
            My Orders
        </a>

    </nav>


    <!-- Main Container -->

    <div class="container">

        <div class="card">


            <!-- Order Information -->

            <h2>
                <?= e($order['order_code']) ?>
            </h2>

            <p>
                Status:
                <strong>
                    <?= e($order['status']) ?>
                </strong>
            </p>

            <p>
                Payment:
                <strong>
                    <?= e($order['payment_status']) ?>
                </strong>
            </p>


            <!-- Order Items Table -->

            <table class="table">

                <tr>

                    <th>
                        Item
                    </th>

                    <th>
                        Product
                    </th>

                    <th>
                        Qty
                    </th>

                    <th>
                        Price
                    </th>

                    <th>
                        Total
                    </th>

                </tr>


                <?php foreach ($items as $item): ?>

                    <tr>

                        <!-- Product Image -->

                        <td>

                            <img
                                src="<?= e(
                                    get_image_url(
                                        $item['image'] ?? '',
                                        $item['name']
                                    )
                                ) ?>"
                                alt="<?= e($item['name']) ?>"
                                style="
                                    width: 45px;
                                    height: 45px;
                                    object-fit: cover;
                                    border-radius: 6px;
                                    background: #352934;
                                "
                                onerror="
                                    this.onerror=null;
                                    this.src='../assets/images/default.jpg';
                                "
                            >

                        </td>


                        <!-- Product Name -->

                        <td>
                            <?= e($item['name']) ?>
                        </td>


                        <!-- Quantity -->

                        <td>
                            <?= $item['quantity'] ?>
                        </td>


                        <!-- Price -->

                        <td>
                            Rs. <?= number_format($item['price'], 2) ?>
                        </td>


                        <!-- Item Total -->

                        <td>
                            Rs.
                            <?= number_format(
                                $item['price'] * $item['quantity'],
                                2
                            ) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>


            </table>


            <!-- Order Total -->

            <h3>
                Total:
                Rs. <?= number_format($order['total'], 2) ?>
            </h3>


        </div>

    </div>


</body>

</html>