
<?php

require_once "config/auth.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once "config/db.php";
require_once "config/cart.php";

// Get product search keyword
$search = trim($_GET['search'] ?? '');

// Fetch available products
$sql = "
    SELECT
        p.*,
        c.name AS category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.availability = 1
";

$params = [];

if ($search !== '') {
    $sql .= "
        AND (
            p.name LIKE :search_name
            OR c.name LIKE :search_category
            OR p.description LIKE :search_description
        )
    ";

    $keyword = '%' . $search . '%';

    $params['search_name'] = $keyword;
    $params['search_category'] = $keyword;
    $params['search_description'] = $keyword;
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ice Cream Shop</title>

    <link rel="stylesheet" href="assets/style.css">

    <style>
        /* Product search */

        .product-search {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .product-search input {
            flex: 1;
            min-width: 200px;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
        }

        .product-search input:focus {
            outline: none;
            border-color: #ffbd59;
            box-shadow: 0 0 5px rgba(255, 189, 89, 0.4);
        }

        .search-info {
            margin: 15px 0 20px;
            line-height: 1.6;
        }

        .no-products {
            grid-column: 1 / -1;
            text-align: center;
            padding: 35px 20px;
            border-radius: 10px;
            background: #2b242b;
            color: #ffffff;
        }

        .no-products h3 {
            margin-bottom: 10px;
        }

        .no-products p {
            margin-bottom: 18px;
        }

        /* Footer */

        .footer {
            background: #211a20;
            color: #ffffff;
            margin-top: 50px;
            border-top: 1px solid #40333d;
        }

        .footer-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 25px;

            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
        }

        .footer-section h3 {
            margin-bottom: 15px;
            color: #ffffff;
        }

        .footer-section p {
            color: #cfc5cc;
            margin: 8px 0;
            line-height: 1.6;
        }

        .footer-bottom {
            border-top: 1px solid #40333d;
            text-align: center;
            padding: 18px 20px;
        }

        .footer-bottom p {
            margin: 5px 0;
            color: #aaa0a8;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .footer-container {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .product-search {
                flex-direction: column;
            }

            .product-search input {
                width: 100%;
            }
        }
    </style>

</head>

<body>

<!-- Navigation bar -->

<nav class="navbar">

    <div class="logo">
        🍦 Ice Cream Shop
    </div>

    <div class="nav-links">

        <a href="index.php">
            Home
        </a>

        <a href="cart.php">
            Cart (<?= cartCount() ?>)
        </a>

        <a href="customer/orders.php">
            My Orders
        </a>

        <?php if ($_SESSION['user']['role'] === 'admin'): ?>

            <a href="admin/dashboard.php">
                Admin
            </a>

        <?php endif; ?>

        <a href="logout.php">
            Logout
        </a>

    </div>

</nav>


<!-- Main content -->

<div class="container">

    <!-- Hero section -->

    <section class="hero">

        <h1>
            Bing chung Ice Cream shop🍨
        </h1>

        <p>
            Browse our delicious ice cream collection and order online.
        </p>

    </section>


    <!-- Product heading -->

    <h2>
        Our Products
    </h2>


    <!-- Product search form -->

    <form method="GET" action="index.php" class="product-search">

        <input
            type="search"
            name="search"
            placeholder="Search by product name, category or description..."
            value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
            aria-label="Search ice cream products"
        >

        <button type="submit" class="btn secondary">
            🔍 Search
        </button>

        <?php if ($search !== ''): ?>

            <a
                href="index.php"
                class="btn secondary"
                style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;"
            >
                Clear
            </a>

        <?php endif; ?>

    </form>


    <!-- Search results information -->

    <div class="search-info">

        <?php if ($search !== ''): ?>

            <p>
                Search results for
                <strong><?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?></strong>:
                <?= count($products) ?> product(s) found.
            </p>

        <?php else: ?>

            <p>
                Browse our available ice cream products below.
            </p>

        <?php endif; ?>

    </div>


    <!-- Products grid -->

    <div class="grid">

        <?php if (!empty($products)): ?>

            <?php foreach ($products as $p): ?>

                <div class="card">

                    <!-- Stock availability -->

                    <?php if ((int)$p['stock_quantity'] > 0): ?>

                        <div style="
                            font-size:14px;
                            font-weight:bold;
                            color:#7fe687;
                            margin-bottom:10px;
                            text-align:center;
                        ">

                            Available Stock:
                            <?= (int)$p['stock_quantity'] ?>

                        </div>

                    <?php else: ?>

                        <div style="
                            font-size:14px;
                            font-weight:bold;
                            color:#e06d75;
                            margin-bottom:10px;
                            text-align:center;
                        ">

                            Out of Stock

                        </div>

                    <?php endif; ?>


                    <!-- Product image -->

                    <a
                        href="product.php?id=<?= (int)$p['id'] ?>"
                        style="text-decoration:none; display:block;"
                    >

                        <img
                            src="/ice_cream_shop/assets/images/<?= htmlspecialchars($p['image'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>"
                            class="product-image"
                            onerror="this.onerror=null; this.src='assets/images/default.jpg';"
                        >

                    </a>


                    <!-- Product name -->

                    <h3>

                        <a
                            href="product.php?id=<?= (int)$p['id'] ?>"
                            style="color:inherit; text-decoration:none;"
                        >

                            <?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>

                        </a>

                    </h3>


                    <!-- Product description -->

                    <p>
                        <?= htmlspecialchars($p['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>


                    <!-- Product category -->

                    <p>
                        Category:
                        <?= htmlspecialchars($p['category_name'], ENT_QUOTES, 'UTF-8') ?>
                    </p>


                    <!-- Product price -->

                    <p class="price">

                        Rs.
                        <?= number_format((float)$p['price'], 2) ?>

                    </p>


                    <!-- Add to cart -->

                    <div class="actions">

                        <?php if ((int)$p['stock_quantity'] > 0): ?>

                            <form method="post" action="cart.php">

                                <input
                                    type="hidden"
                                    name="action"
                                    value="add"
                                >

                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?= (int)$p['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn secondary"
                                >
                                    Add to Cart
                                </button>

                            </form>

                        <?php else: ?>

                            <button
                                type="button"
                                class="btn secondary"
                                disabled
                                style="opacity:0.5; cursor:not-allowed;"
                            >
                                Out of Stock
                            </button>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <!-- No matching products -->

            <div class="no-products">

                <h3>
                    No products found 🍦
                </h3>

                <p>
                    Try another product name or category.
                </p>

                <a
                    href="index.php"
                    class="btn secondary"
                    style="text-decoration:none;"
                >
                    View All Products
                </a>

            </div>

        <?php endif; ?>

    </div>

</div>


<!-- Footer -->

<footer class="footer">

    <div class="footer-container">

        <div class="footer-section">

            <h3>
                Bing chung Ice Cream shop 🍨
            </h3>

            <p>
                Ice Cream Shop E-Commerce Management System
            </p>

            <p>
                Fresh and delicious ice cream,
                available to order online.
            </p>

        </div>


        <div class="footer-section">

            <h3>
                Contact Us
            </h3>

            <p>
                📧 Admin:
                admin@gmail.com
            </p>

            <p>
                📞 Phone:
                +94 77 123 4567
            </p>

            <p>
                📍 Address:
                Bing chung Ice Cream shop 🍨, Jaffna, Sri Lanka
            </p>

        </div>


        <div class="footer-section">

            <h3>
                Opening Hours
            </h3>

            <p>
                Monday - Saturday
            </p>

            <p>
                🕐 10:00 AM - 9:00 PM
            </p>

            <p>
                Sunday
            </p>

            <p>
                🕐 11:00 AM - 8:00 PM
            </p>

        </div>

    </div>


    <div class="footer-bottom">

        <p>
            © <?= date('Y') ?> Ice Cream Shop E-Commerce Management System
        </p>

        <p>
            All Rights Reserved.
        </p>

    </div>

</footer>

</body>

</html>
