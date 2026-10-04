<?php

require_once "config/db.php";
require_once "config/auth.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (
        $name === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 6
    ) {
        $error = "Enter a valid name, email, and password of at least 6 characters.";
    } else {

        $check = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email = ?
        ");

        $check->execute([$email]);

        if ($check->fetch()) {

            $error = "Email already exists.";

        } else {

            $hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare("
                INSERT INTO users
                    (name, email, password, role)
                VALUES
                    (?, ?, ?, 'customer')
            ");

            $stmt->execute([
                $name,
                $email,
                $hash
            ]);

            header("Location: login.php?registered=1");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account | Ice Cream Shop</title>

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

    <nav class="navbar">

        <div class="logo">
            🍦 Ice Cream Shop
        </div>

        <a href="index.php">
            Home
        </a>

    </nav>


    <main class="register-container">

        <div class="form-card">

            <h2>Create Account</h2>

            <?php if ($error): ?>

                <div class="error">
                    <?= e($error) ?>
                </div>

            <?php endif; ?>


            <form method="post">

                <label for="name">
                    Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your name"
                    value="<?= e($_POST['name'] ?? '') ?>"
                    required
                >


                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?= e($_POST['email'] ?? '') ?>"
                    required
                >


                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter at least 6 characters"
                    required
                >


                <button
                    type="submit"
                    class="btn"
                >
                    Register
                </button>

            </form>


            <p>
                Already have an account?
                <a href="login.php">Login</a>
            </p>

        </div>

    </main>

</body>

</html>