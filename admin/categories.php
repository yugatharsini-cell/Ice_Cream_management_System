<?php

require_once "../config/db.php";
require_once "../config/auth.php";

requireAdmin();


// ===============================
// Delete Category
// ===============================

if (isset($_GET['delete'])) {

    $categoryId = (int) $_GET['delete'];

    $stmt = $pdo->prepare(
        "DELETE FROM categories WHERE id = ?"
    );

    try {
        $stmt->execute([$categoryId]);
    } catch (Exception $e) {
        // Ignore delete error
    }

    header("Location: categories.php");
    exit;
}


// ===============================
// Add Category
// ===============================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);

    if ($name) {

        $stmt = $pdo->prepare(
            "INSERT INTO categories (name) VALUES (?)"
        );

        try {
            $stmt->execute([$name]);
        } catch (Exception $e) {
            // Ignore insert error
        }
    }

    header("Location: categories.php");
    exit;
}


// ===============================
// Get Categories
// ===============================

$cats = $pdo
    ->query("SELECT * FROM categories ORDER BY name")
    ->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Categories</title>

    <link
        rel="stylesheet"
        href="../assets/style.css"
    >

</head>

<body>


    <!-- Navigation -->

    <nav class="navbar">

        <div class="logo">
            Categories
        </div>

        <a href="dashboard.php">
            Dashboard
        </a>

    </nav>


    <!-- Main Container -->

    <div class="container">


        <!-- Add Category Form -->

        <div class="form-card">

            <form method="post">

                <label>
                    Category Name
                </label>

                <input
                    type="text"
                    name="name"
                    required
                >

                <br><br>

                <button
                    type="submit"
                    class="btn"
                >
                    Add Category
                </button>

            </form>

        </div>


        <!-- Categories Table -->

        <div class="table-wrap">

            <table class="table">

                <tr>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Action</th>

                </tr>


                <?php foreach ($cats as $c): ?>

                    <tr>

                        <td>
                            <?= $c['id'] ?>
                        </td>

                        <td>
                            <?= e($c['name']) ?>
                        </td>

                        <td>

                            <a
                                class="btn danger"
                                href="?delete=<?= $c['id'] ?>"
                                onclick="return confirm('Delete category?')"
                            >
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>


            </table>

        </div>


    </div>


</body>

</html>