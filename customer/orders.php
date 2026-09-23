<?php

require_once "../config/db.php";
require_once "../config/auth.php";

requireLogin();


// ===============================
// Get Customer Orders
// ===============================

$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE customer_id = ?
    ORDER BY id DESC
");

$stmt->execute([
    $_SESSION['user']['id']
]);

$orders = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Orders</title>

    <link
        rel="stylesheet"
        href="../assets/style.css"
    >

</head>

<body>


    <!-- Navigation -->

    <nav class="navbar">

        <div class="logo">
            🍦 My Orders
        </div>

        <a href="../index.php">
            Home
        </a>

    </nav>


    <!-- Main Container -->

    <div class="container">

        <h1>
            Order History
        </h1>


        <!-- Orders Table -->

        <div class="table-wrap">

            <table class="table">

                <tr>

                    <th>
                        Order
                    </th>

                    <th>
                        Date
                    </th>

                    <th>
                        Total
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Action
                    </th>

                </tr>


                <?php foreach ($orders as $order): ?>

                    <tr>

                        <!-- Order Code -->

                        <td>
                            <?= e($order['order_code']) ?>
                        </td>


                        <!-- Order Date -->

                        <td>
                            <?= e($order['order_date']) ?>
                        </td>


                        <!-- Total -->

                        <td>
                            Rs.
                            <?= number_format(
                                $order['total'],
                                2
                            ) ?>
                        </td>


                        <!-- Status -->

                        <td>
                            <?= e($order['status']) ?>
                        </td>


                        <!-- View Order -->

                        <td>

                            <a
                                class="btn"
                                href="order_details.php?id=<?= $order['id'] ?>"
                            >
                                View
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>


            </table>

        </div>


    </div>


</body>

</html>