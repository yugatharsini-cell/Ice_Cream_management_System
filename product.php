
<?php

require_once "config/db.php";
require_once "config/auth.php";
require_once "config/cart.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT 
        p.*, 
        c.name AS category_name 
    FROM products p 
    JOIN categories c 
        ON p.category_id = c.id 
    WHERE p.id = ? 
    AND p.availability = 1
");

$stmt->execute([$id]);

$p = $stmt->fetch();

if (!$p) {
    die("Product not found.");
}

$imageUrl = get_image_url($p['image'] ?? '', $p['name']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= e($p['name']) ?>
    </title>

    <link rel="stylesheet" href="assets/style.css">

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

        </div>

    </nav>



    <div class="container">

        <div class="card product-details">



            <img
                src="<?= e($imageUrl) ?>"
                alt="<?= e($p['name']) ?>"
                class="product-image"
            >



            <h1>
                <?= e($p['name']) ?>
            </h1>

            <p>
                <?= e($p['description']) ?>
            </p>


            <p>
                Category:
                <?= e($p['category_name']) ?>
            </p>


            <p class="price">
                Rs. <?= number_format($p['price'], 2) ?>
            </p>


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
                    class="btn"
                >
                    Add to Cart
                </button>

            </form>

        </div>

    </div>

</body>

</html>

