<?php

require_once "../config/db.php";
require_once "../config/auth.php";

requireAdmin();


$users = $pdo
    ->query("
        SELECT
            id,
            name,
            email,
            created_at
        FROM users
        WHERE role = 'customer'
        ORDER BY id DESC
    ")
    ->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customers</title>

    <link
        rel="stylesheet"
        href="../assets/style.css"
    >

</head>

<body>


    <nav class="navbar">

        <div class="logo">
            Customers
        </div>

        <a href="dashboard.php">
            Dashboard
        </a>

    </nav>




    <div class="container">


        <div class="table-wrap">

            <table class="table">

                <tr>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Registered</th>

                </tr>


                <?php foreach ($users as $u): ?>

                    <tr>

                        <td>
                            <?= $u['id'] ?>
                        </td>

                        <td>
                            <?= e($u['name']) ?>
                        </td>

                        <td>
                            <?= e($u['email']) ?>
                        </td>

                        <td>
                            <?= e($u['created_at']) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>


            </table>

        </div>


    </div>


</body>

</html>