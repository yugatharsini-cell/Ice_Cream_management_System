<?php

require_once "config/db.php";
require_once "config/auth.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE email = ? LIMIT 1"
    );

    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        header("Location: index.php");
        exit();
    }

    $error = "Invalid email or password.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Ice Cream Shop</title>

    <link rel="stylesheet" href="assets/style.css">

</head>

<body>

<nav class="navbar">

    <div class="logo">
        🍦 Ice Cream Shop
    </div>

    <div class="nav-links">
        <a href="index.php">Home</a>
    </div>

</nav>


<div class="login-container">

    <div class="form-card">

        <h2>Login</h2>

        <?php if (isset($_GET['registered'])): ?>

            <div class="success">
                Registration successful. Please login.
            </div>

        <?php endif; ?>


        <?php if ($error): ?>

            <div class="error">
                <?= e($error) ?>
            </div>

        <?php endif; ?>


        <form method="post">

            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
            >


            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
            >


            <button type="submit" class="btn">
                Login
            </button>

        </form>


        <p>
            New customer?
            <a href="register.php">Register</a>
        </p>

    </div>

</div>

</body>
</html>