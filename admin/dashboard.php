<?php

require_once "../config/db.php";
require_once "../config/auth.php";

requireAdmin();


// ===============================
// Dashboard Statistics
// ===============================

// Total Customers
$users = $pdo
    ->query("
        SELECT COUNT(*) AS c
        FROM users
        WHERE role = 'customer'
    ")
    ->fetch()['c'];


// Total Products
$products = $pdo
    ->query("
        SELECT COUNT(*) AS c
        FROM products
    ")
    ->fetch()['c'];


// Total Orders
$orders = $pdo
    ->query("
        SELECT COUNT(*) AS c
        FROM orders
    ")
    ->fetch()['c'];


// Successful Payments
$payments = $pdo
    ->query("
        SELECT COUNT(*) AS c
        FROM payments
        WHERE payment_status = 'Successful'
    ")
    ->fetch()['c'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard</title>

    <link
        rel="stylesheet"
        href="../assets/style.css"
    >

</head>

<body>


    <!-- Navigation -->

    <nav class="navbar">

        <div class="logo">
            🍦 Admin Dashboard
        </div>

        <div class="nav-links">

            <a href="../index.php">
                Shop
            </a>

            <a href="products.php">
                Products
            </a>

            <a href="categories.php">
                Categories
            </a>

            <a href="orders.php">
                Orders
            </a>

            <a href="customers.php">
                Customers
            </a>

            <a href="../logout.php">
                Logout
            </a>

        </div>

    </nav>


    <!-- Main Container -->

    <div class="container">

        <h1>
            Dashboard
        </h1>


        <!-- Dashboard Cards -->

        <div class="grid">


            <!-- Customers -->

            <div class="card">

                <h3>
                    Customers
                </h3>

                <h2>
                    <?= $users ?>
                </h2>

            </div>


            <!-- Products -->

            <div class="card">

                <h3>
                    Products
                </h3>

                <h2>
                    <?= $products ?>
                </h2>

            </div>


            <!-- Orders -->

            <div class="card">

                <h3>
                    Orders
                </h3>

                <h2>
                    <?= $orders ?>
                </h2>

            </div>


            <!-- Successful Payments -->

            <div class="card">

                <h3>
                    Successful Payments
                </h3>

                <h2>
                    <?= $payments ?>
                </h2>

            </div>


        </div>

    </div>


</body>

</html>