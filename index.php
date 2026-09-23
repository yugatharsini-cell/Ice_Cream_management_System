<?php 

require_once "config/auth.php"; 

if (!isset($_SESSION['user'])) { 
    header("Location: login.php"); 
    exit(); 
} 

require_once "config/db.php"; 
require_once "config/cart.php"; 


$stmt = $pdo->query(" 
    SELECT 
        p.*, 
        c.name AS category_name 
    FROM products p 
    JOIN categories c 
        ON p.category_id = c.id 
    WHERE p.availability = 1 
    ORDER BY p.id DESC 
"); 

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

        }

    </style>

</head> 


<body> 

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



<div class="container"> 


    <section class="hero"> 

        <h1> 
            Bing chung Ice Cream shop🍨 
        </h1> 

        <p> 
            Browse our delicious ice cream collection and order online. 
        </p> 

    </section> 


    <h2> 
        Our Products 
    </h2> 


    <div class="grid"> 


        <?php foreach ($products as $p): ?> 

            <div class="card"> 
 

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


                <a 
                    href="product.php?id=<?= $p['id'] ?>" 
                    style=" 
                        text-decoration:none; 
                        display:block; 
                    " 
                > 

                    <img 
                        src="/ice_cream_shop/assets/images/<?= htmlspecialchars($p['image']) ?>" 
                        alt="<?= htmlspecialchars($p['name']) ?>" 
                        class="product-image" 
                        onerror=" 
                            this.onerror=null; 
                            this.src='assets/images/default.jpg'; 
                        " 
                    > 

                </a> 

                <h3> 

                    <a 
                        href="product.php?id=<?= $p['id'] ?>" 
                        style=" 
                            color:inherit; 
                            text-decoration:none; 
                        " 
                    > 

                        <?= htmlspecialchars($p['name']) ?> 

                    </a> 

                </h3> 


                <p> 

                    <?= htmlspecialchars($p['description']) ?> 

                </p> 


                <p> 

                    Category: 

                    <?= htmlspecialchars($p['category_name']) ?> 

                </p> 



                <p class="price"> 

                    Rs. 
                    <?= number_format($p['price'], 2) ?> 

                </p> 
 

                <div class="actions"> 

                    <?php if ((int)$p['stock_quantity'] > 0): ?> 

                        <form 
                            method="post" 
                            action="cart.php" 
                        > 

                            <input 
                                type="hidden" 
                                name="action" 
                                value="add" 
                            > 

                            <input 
                                type="hidden" 
                                name="product_id" 
                                value="<?= $p['id'] ?>" 
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
                            style=" 
                                opacity:0.5; 
                                cursor:not-allowed; 
                            " 
                        > 

                            Out of Stock 

                        </button> 

                    <?php endif; ?> 

                </div> 


            </div> 

        <?php endforeach; ?> 


    </div> 


</div> 




<footer class="footer">

    <div class="footer-container">


        <div class="footer-section">

            <h3>Bing chung Ice Cream shop 🍨</h3>

            <p>
                Ice Cream Shop E-Commerce Management System
            </p>

            <p>
                Fresh and delicious ice cream,
                available to order online.
            </p>

        </div>


        <div class="footer-section">

            <h3>Contact Us</h3>

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

            <h3>Opening Hours</h3>

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