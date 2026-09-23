<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function cartCount() {
    return array_sum($_SESSION['cart'] ?? []);
}

function clearCart() {
    $_SESSION['cart'] = [];
}
?>
