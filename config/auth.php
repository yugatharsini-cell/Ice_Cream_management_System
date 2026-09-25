<?php

session_start();


// Escape HTML output
function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}


// Check if user is logged in
function is_logged_in()
{
    return isset($_SESSION['user']);
}


// Check if logged-in user is admin
function is_admin()
{
    return isset($_SESSION['user'])
        && ($_SESSION['user']['role'] ?? '') === 'admin';
}


// Require login
function requireLogin()
{
    if (!is_logged_in()) {
        header("Location: /ice_cream_shop/login.php");
        exit();
    }
}


// Require admin
function require_admin()
{
    if (!is_logged_in()) {
        header("Location: /ice_cream_shop/login.php");
        exit();
    }

    if (!is_admin()) {
        header("Location: /ice_cream_shop/index.php");
        exit();
    }
}


// Admin function alias
function requireAdmin()
{
    require_admin();
}


// Get image URL
function get_image_url($image, $name = '')
{
    $image = trim($image ?? '');

    // Default image mapping
    if ($image === '') {
        $nameMap = [
            'Classic Vanilla'  => 'ClassicVanilla.jpg',
            'Chocolate Dream'  => 'ChocolateDream.jpg',
            'Strawberry Bliss' => 'StrawberryBliss.jpg',
            'Mango Magic'      => 'MangoMagic.webp',
            'Royal Sundae'     => 'RoyalSundae.jpg',
        ];

        $image = $nameMap[$name] ?? '';

        if ($image === '') {
            return '';
        }
    }


    // Return URL or base64 image directly
    if (
        preg_match('#^(https?:)?//#i', $image)
        || strpos($image, 'data:image') === 0
    ) {
        return $image;
    }


    // Convert Windows backslashes to forward slashes
    $image = str_replace('\\', '/', $image);


    // Check whether current PHP file is inside a subdirectory
    $isSubdir = false;

    if (isset($_SERVER['SCRIPT_FILENAME'])) {
        $scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
        $isSubdir = (basename($scriptDir) !== 'ice_cream_shop');
    }

    $basePrefix = $isSubdir ? '../' : '';


    // Remove project folder from image path
    if (strpos($image, '/ice_cream_shop/') === 0) {
        $image = substr($image, strlen('/ice_cream_shop/'));
    }


    // If path already starts with assets/
    if (strpos($image, 'assets/') === 0) {
        return $basePrefix . $image;
    }


    // If path starts with /assets/
    if (strpos($image, '/assets/') === 0) {
        return $basePrefix . ltrim($image, '/');
    }


    // Default image location
    return $basePrefix . 'assets/images/' . ltrim($image, '/');
}
?>

