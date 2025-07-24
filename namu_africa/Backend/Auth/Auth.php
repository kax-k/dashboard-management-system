<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['loggedin']) && !empty($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../FrontEnd/login.php");
        exit();
    }
}
?>
