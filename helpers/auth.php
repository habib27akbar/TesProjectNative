<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLogin()
{
    return isset($_SESSION['user_id']);
}

function checkLogin()
{
    if (!isLogin()) {
        header('Location: ./index.php?page=login');
        exit;
    }
}

function checkGuest()
{
    if (isLogin()) {
        header('Location: ./index.php?page=dashboard');
        exit;
    }
}

function e($text)
{
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}
?>
