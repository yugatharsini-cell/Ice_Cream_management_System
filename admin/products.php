<?php
require_once "../config/db.php";
require_once "../config/auth.php";

requireAdmin();

/* =========================
   DELETE PRODUCT
========================= */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $st = $pdo->prepare("DELETE FROM products WHERE id=?");

    try {
        $st->execute([$id]);
    } catch (Exception $e) {
        // Ignore delete errors
    }

    header("Location: products.php");
    exit;
}


/* =========================
   ADD / UPDATE PRODUCT
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id    = (int)($_POST['id'] ?? 0);
    $name  = trim($_POST['name'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $cat   = (int)($_POST['category_id'] ?? 0);
    $img   = trim($_POST['image'] ?? '');
    $av    = isset($_POST['availability']) ? 1 : 0;

    /* Stock Quantity */
    $stock = (int)($_POST['stock_quantity'] ?? 0);

    /* Prevent negative stock */
    if ($stock < 0) {
        $stock = 0;
    }


    /* =========================
       UPDATE PRODUCT
    ========================= */
    if ($id) {

        $st = $pdo->prepare("
            UPDATE products
            SET
                name=?,
                description=?,
                price=?,
                category_id=?,
                image=?,
                availability=?,
                stock_quantity=?
            WHERE id=?
        ");

        $st->execute([
            $name,
            $desc,
            $price,
            $cat,
            $img,
            $av,
            $stock,
            $id
        ]);

    }


    /* =========================
       INSERT NEW PRODUCT
    ========================= */
    else {

        $st = $pdo->prepare("
            INSERT INTO products
            (
                name,
                description,
                price,
                category_id,
                image,
                availability,
                stock_quantity
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $st->execute([
            $name,
            $desc,
            $price,
            $cat,
            $img,
            $av,
            $stock
        ]);
    }

    header("Location: products.php");
    exit;
}


/* =========================
   GET PRODUCT FOR EDIT
========================= */
$edit = null;

if (isset($_GET['edit'])) {

    $st = $pdo->prepare("
        SELECT *
        FROM products
        WHERE id=?
    ");

    $st->execute([
        (int)$_GET['edit']
    ]);

    $edit = $st->fetch();
}


/* =========================
   GET CATEGORIES
========================= */
$cats = $pdo->query("
    SELECT *
    FROM categories
    ORDER BY name
")->fetchAll();


/* =========================
   GET PRODUCTS
========================= */
$products = $pdo->query("
    SELECT
        p.*,
        c.name AS category_name
    FROM products p
    JOIN categories c
        ON c.id = p.category_id
    ORDER BY p.id DESC
")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Products Management</title>

    <link rel="stylesheet" href="../assets/style.css">

</head>

<body>


<!-- =========================
     NAVIGATION
========================= -->

<nav class="navbar">

    <div class="logo">
        🍦 Products Management
    </div>

    <div class="nav-links">

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="../index.php">
            View Shop
        </a>

        <a href="../logout.php">
            Logout
        </a>

    </div>

</nav>


<div class="container">


<!-- =========================
     PRODUCT FORM
========================= -->

<div class="form-card">

    <h2>
        <?= $edit ? 'Update' : 'Add' ?> Product
    </h2>


    <form method="post">

        <!-- Product ID -->

        <input
            type="hidden"
            name="id"
            value="<?= e($edit['id'] ?? 0) ?>"
        >


        <!-- NAME -->

        <label>
            Name
        </label>

        <input
            name="name"
            required
            value="<?= e($edit['name'] ?? '') ?>"
            placeholder="e.g. Classic Vanilla"
        >


        <!-- DESCRIPTION -->

        <label>
            Description
        </label>

        <textarea
            name="description"
            placeholder="Product description..."
        ><?= e($edit['description'] ?? '') ?></textarea>


        <!-- IMAGE -->

        <label>
            Image (URL or Filename)
        </label>

        <input
            name="image"
            value="<?= e($edit['image'] ?? '') ?>"
            placeholder="e.g. ClassicVanilla.jpg"
        >


        <!-- IMAGE PREVIEW -->

        <?php if (!empty($edit['image'])): ?>

            <div style="margin:10px 0;">

                <span style="font-size:12px;color:#a89ea6;">
                    Current Preview:
                </span>

                <br>

                <img
                    src="<?= e(
                        get_image_url(
                            $edit['image'] ?? '',
                            $edit['name'] ?? ''
                        )
                    ) ?>"
                    alt="Preview"
                    style="
                        width:80px;
                        height:80px;
                        object-fit:cover;
                        border-radius:8px;
                        margin-top:6px;
                        border:1px solid #443743;
                    "
                    onerror="
                        this.onerror=null;
                        this.src='../assets/images/default.jpg';
                    "
                >

            </div>

        <?php endif; ?>


        <!-- PRICE -->

        <label>
            Price (Rs.)
        </label>

        <input
            type="number"
            step="0.01"
            min="0"
            name="price"
            required
            value="<?= e($edit['price'] ?? '') ?>"
            placeholder="450.00"
        >


        <!-- CATEGORY -->

        <label>
            Category
        </label>

        <select name="category_id" required>

            <?php foreach ($cats as $c): ?>

                <option
                    value="<?= $c['id'] ?>"
                    <?= isset($edit) &&
                        $edit['category_id'] == $c['id']
                        ? 'selected'
                        : ''
                    ?>
                >

                    <?= e($c['name']) ?>

                </option>

            <?php endforeach; ?>

        </select>


        <!-- =========================
             STOCK QUANTITY
        ========================== -->

        <label>
            Stock Quantity
        </label>

        <input
            type="number"
            name="stock_quantity"
            min="0"
            step="1"
            required
            value="<?= e($edit['stock_quantity'] ?? 0) ?>"
            placeholder="e.g. 20"
        >

        <small style="
            display:block;
            margin-top:5px;
            color:#a89ea6;
        ">
            Enter the number of ice creams currently available.
        </small>


        <!-- AVAILABILITY -->

        <label
            style="
                display:flex;
                align-items:center;
                gap:8px;
                cursor:pointer;
                margin-top:15px;
            "
        >

            <input
                type="checkbox"
                name="availability"
                <?= !isset($edit) ||
                    $edit['availability']
                    ? 'checked'
                    : ''
                ?>
            >

            Available for ordering

        </label>


        <!-- SAVE BUTTON -->

        <button
            type="submit"
            class="btn"
            style="margin-top:15px;"
        >

            <?= $edit
                ? 'Update Product'
                : 'Save Product'
            ?>

        </button>


        <!-- CANCEL -->

        <?php if ($edit): ?>

            <a
                href="products.php"
                class="btn secondary"
                style="
                    margin-top:15px;
                    margin-left:10px;
                "
            >
                Cancel
            </a>

        <?php endif; ?>

    </form>

</div>



<!-- =========================
     PRODUCT TABLE
========================= -->

<div class="table-wrap">

    <table class="table">

        <thead>

            <tr>

                <th>
                    Image
                </th>

                <th>
                    Name
                </th>

                <th>
                    Category
                </th>

                <th>
                    Price
                </th>

                <th>
                    Stock
                </th>

                <th>
                    Available
                </th>

                <th>
                    Actions
                </th>

            </tr>

        </thead>


        <tbody>

        <?php foreach ($products as $p): ?>

            <tr>


                <!-- IMAGE -->

                <td>

                    <img
                        src="<?= e(
                            get_image_url(
                                $p['image'] ?? '',
                                $p['name']
                            )
                        ) ?>"
                        alt="<?= e($p['name']) ?>"
                        style="
                            width:50px;
                            height:50px;
                            object-fit:cover;
                            border-radius:8px;
                            display:block;
                            background:#352934;
                        "
                        onerror="
                            this.onerror=null;
                            this.src='../assets/images/default.jpg';
                        "
                    >

                </td>


                <!-- NAME -->

                <td>

                    <strong>
                        <?= e($p['name']) ?>
                    </strong>

                </td>


                <!-- CATEGORY -->

                <td>

                    <?= e($p['category_name']) ?>

                </td>


                <!-- PRICE -->

                <td>

                    Rs.
                    <?= number_format(
                        $p['price'],
                        2
                    ) ?>

                </td>


                <!-- STOCK -->

                <td>

                    <?php if ((int)$p['stock_quantity'] > 0): ?>

                        <span style="
                            color:#7fe687;
                            font-weight:bold;
                        ">

                            <?= (int)$p['stock_quantity'] ?>

                        </span>

                    <?php else: ?>

                        <span style="
                            color:#e06d75;
                            font-weight:bold;
                        ">

                            Out of Stock

                        </span>

                    <?php endif; ?>

                </td>


                <!-- AVAILABILITY -->

                <td>

                    <?php if ($p['availability']): ?>

                        <span style="color:#7fe687;">
                            Yes
                        </span>

                    <?php else: ?>

                        <span style="color:#e06d75;">
                            No
                        </span>

                    <?php endif; ?>

                </td>


                <!-- ACTIONS -->

                <td>

                    <a
                        class="btn"
                        href="?edit=<?= $p['id'] ?>"
                    >
                        Edit
                    </a>


                    <a
                        class="btn danger"
                        href="?delete=<?= $p['id'] ?>"
                        onclick="
                            return confirm(
                                'Delete this product?'
                            )
                        "
                    >
                        Delete
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>


</div>

</body>

</html>

