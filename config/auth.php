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



function requireAdmin()
{
    require_admin();
}

function get_image_url($image, $name = '')
{
    $image = trim($image ?? '');

    
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
s
    if (preg_match('#^(https?:)?//#i', $image) || strpos($image, 'data:image') === 0) {
        return $image;
    }

    
    $image = str_replace('\\', '/', $image);

    
    $isSubdir = false;
    if (isset($_SERVER['SCRIPT_FILENAME'])) {
        $scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
        $isSubdir = (basename($scriptDir) !== 'ice_cream_shop');
    }
    $basePrefix = $isSubdir ? '../' : '';

    
    if (strpos($image, '/ice_cream_shop/') === 0) {
        $image = substr($image, strlen('/ice_cream_shop/'));
    }

    if (strpos($image, 'assets/') === 0) {
        return $basePrefix . $image;
    }
    if (strpos($image, '/assets/') === 0) {
        return $basePrefix . ltrim($image, '/');
    }

    return $basePrefix . 'assets/images/' . ltrim($image, '/');
}

?>