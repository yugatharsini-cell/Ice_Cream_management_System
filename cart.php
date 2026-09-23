<?php

require_once "config/db.php";
require_once "config/auth.php";
require_once "config/cart.php";


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';
    $pid = (int) ($_POST['product_id'] ?? 0);



    if ($action === 'add') {

        $stmt = $pdo->prepare("
            SELECT id
            FROM products
            WHERE id = ?
              AND availability = 1
        ");

        $stmt->execute([$pid]);

        if ($stmt->fetch()) {

            $_SESSION['cart'][$pid] =
                ($_SESSION['cart'][$pid] ?? 0) + 1;
        }
    }



    elseif ($action === 'update') {

        foreach (($_POST['qty'] ?? []) as $id => $qty) {

            $id = (int) $id;
            $qty = max(0, (int) $qty);

            if ($qty === 0) {

                unset($_SESSION['cart'][$id]);

            } else {

                $_SESSION['cart'][$id] = $qty;
            }
        }
    }


    elseif ($action === 'remove') {

        unset($_SESSION['cart'][$pid]);
    }


    header("Location: cart.php");
    exit;
}


$items = [];
$total = 0;


if ($_SESSION['cart']) {

    $ids = array_keys($_SESSION['cart']);

    $placeholders = implode(
        ',',
        array_fill(0, count($ids), '?')
    );


    $stmt = $pdo->prepare("
        SELECT *
        FROM products
        WHERE id IN ($placeholders)
          AND availability = 1
    ");

    $stmt->execute($ids);


    foreach ($stmt as $product) {

        $product['qty'] =
            $_SESSION['cart'][$product['id']];

        $product['line_total'] =
            $product['qty'] * $product['price'];

        $total += $product['line_total'];

        $items[] = $product;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cart</title>

    <link
        rel="stylesheet"
        href="assets/style.css"
    >

</head>

<body>




    <nav class="navbar">

        <div class="logo">
            🍦 Ice Cream Shop
        </div>

        <a href="index.php">
            Continue Shopping
        </a>

    </nav>


   

    <div class="container">

        <h1>
            Shopping Cart
        </h1>


        <?php if (!$items): ?>

        

            <div class="card">

                <p>
                    Your cart is empty.
                </p>

            </div>


        <?php else: ?>


         

            <form method="post">

                <input
                    type="hidden"
                    name="action"
                    value="update"
                >


                <div class="table-wrap">

                    <table class="table">

                        <tr>

                            <th>
                                Item
                            </th>

                            <th>
                                Product
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Quantity
                            </th>

                            <th>
                                Total
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>


                        <?php foreach ($items as $item): ?>

                            <tr>



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
                                            width: 50px;
                                            height: 50px;
                                            object-fit: cover;
                                            border-radius: 8px;
                                            background: #352934;
                                        "
                                        onerror="
                                            this.onerror=null;
                                            this.src='assets/images/default.jpg';
                                        "
                                    >

                                </td>


                              

                                <td>

                                    <strong>
                                        <?= e($item['name']) ?>
                                    </strong>

                                </td>


                              

                                <td>

                                    Rs.
                                    <?= number_format(
                                        $item['price'],
                                        2
                                    ) ?>

                                </td>


                         

                                <td>

                                    <input
                                        style="max-width: 90px"
                                        type="number"
                                        min="0"
                                        name="qty[<?= $item['id'] ?>]"
                                        value="<?= $item['qty'] ?>"
                                    >

                                </td>



                                <td>

                                    Rs.
                                    <?= number_format(
                                        $item['line_total'],
                                        2
                                    ) ?>

                                </td>



                                <td>

                                    <button
                                        class="btn danger"
                                        formaction="cart.php"
                                        name="action"
                                        value="remove"
                                        type="submit"
                                    >
                                        Remove
                                    </button>

                                    <input
                                        type="hidden"
                                        name="product_id"
                                        value="<?= $item['id'] ?>"
                                    >

                                </td>


                            </tr>

                        <?php endforeach; ?>


                    </table>

                </div>


                <br>

                <button
                    class="btn"
                    type="submit"
                >
                    Update Cart
                </button>

            </form>


        

            <div class="cart-total">

                Total:
                Rs. <?= number_format($total, 2) ?>

            </div>


            <?php if (isset($_SESSION['user'])): ?>

                <a
                    class="btn secondary"
                    href="checkout.php"
                >
                    Checkout
                </a>

            <?php else: ?>

                <p>

                    Please
                    <a href="login.php">
                        login
                    </a>
                    to checkout.

                </p>

            <?php endif; ?>


        <?php endif; ?>


    </div>


</body>

</html>