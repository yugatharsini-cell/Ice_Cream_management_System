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


// Same function with camelCase
// This supports pages that use requireAdmin()
function requireAdmin()
{
    require_admin();
}


/**
 * Resolves the user's product image URL or path.
 * Strictly uses whatever image URL or file the user provided.
 * 
 * Handles:
 * 1. Full Web URLs (https://... or http://...)
 * 2. Paths with assets/ (assets/images/...)
 * 3. File names in assets/images/ (ClassicVanilla.jpg)
 * 4. Automatic subdirectory prefix (admin/ vs root)
 */
function get_image_url($image, $name = '')
{
    $image = trim($image ?? '');

    // 1. If empty, fallback to local filename in assets/images/ matching product name
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

    // 2. Full web URL or data URI -> return directly as-is
    if (preg_match('#^(https?:)?//#i', $image) || strpos($image, 'data:image') === 0) {
        return $image;
    }

    // 3. Normalize Windows backslashes
    $image = str_replace('\\', '/', $image);

    // 4. Check if calling script is in a subdirectory (admin, customer)
    $isSubdir = false;
    if (isset($_SERVER['SCRIPT_FILENAME'])) {
        $scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
        $isSubdir = (basename($scriptDir) !== 'ice_cream_shop');
    }
    $basePrefix = $isSubdir ? '../' : '';

    // 5. If it starts with /ice_cream_shop/
    if (strpos($image, '/ice_cream_shop/') === 0) {
        $image = substr($image, strlen('/ice_cream_shop/'));
    }

    // 6. If it already starts with assets/
    if (strpos($image, 'assets/') === 0) {
        return $basePrefix . $image;
    }
    if (strpos($image, '/assets/') === 0) {
        return $basePrefix . ltrim($image, '/');
    }

    // 7. If it's just a file name (e.g. ClassicVanilla.jpg)
    return $basePrefix . 'assets/images/' . ltrim($image, '/');
}

?>