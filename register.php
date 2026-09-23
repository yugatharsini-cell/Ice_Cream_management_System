<?php

require_once "config/db.php";
require_once "config/auth.php";

$error = "";


// ===============================
// Handle Registration
// ===============================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';


    // ===============================
    // Validate Input
    // ===============================

    if (
        $name === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 6
    ) {

        $error =
            "Enter a valid name, email, and password of at least 6 characters.";

    } else {


        // ===============================
        // Check Existing Email
        // ===============================

        $check = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email = ?
        ");

        $check->execute([$email]);


        if ($check->fetch()) {

            $error = "Email already exists.";

        } else {


            // ===============================
            // Create Customer Account
            // ===============================

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


            // ===============================
            // Redirect to Login
            // ===============================

            header(
                "Location: login.php?registered=1"
            );

            exit;
        }
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

    <title>Register</title>

    <link
        rel="stylesheet"
        href="assets/style.css"
    >

</head>

<body>


    <!-- Navigation -->

    <nav class="navbar">

        <div class="logo">
            🍦 Ice Cream Shop
        </div>

        <a href="index.php">
            Home
        </a>

    </nav>


    <!-- Registration Form -->

    <div class="form-card">

        <h2>
            Create Account
        </h2>


        <!-- Error Message -->

        <?php if ($error): ?>

            <div class="error">

                <?= e($error) ?>

            </div>

        <?php endif; ?>


        <!-- Registration Form -->

        <form method="post">


            <!-- Name -->

            <label>
                Name
            </label>

            <input
                type="text"
                name="name"
                required
            >


            <!-- Email -->

            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                required
            >


            <!-- Password -->

            <label>
                Password
            </label>

            <input
                type="password"
                name="password"
                required
            >


            <br>
            <br>


            <!-- Register Button -->

            <button
                type="submit"
                class="btn"
            >
                Register
            </button>


        </form>


        <!-- Login Link -->

        <p>

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </p>


    </div>


</body>

</html>