<?php
session_start();
if(isset($_POST['login'])){
    require 'connect.php';
    $username = $_POST['username'];
    $password = $_POST['password'];

    $login_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if(mysqli_num_rows($login_query) > 0){
        $user = mysqli_fetch_assoc($login_query);
        if(password_verify($password, $user['pswd'])){
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $username;
            $_SESSION['loggedin'] = true;
            header("Location: ../FrontEnd/dashboard.php");
            exit();
        } else {
            echo "<script>alert('Login failed! Incorrect password.'); window.location.href='../FrontEnd/login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Login failed! User not found.'); window.location.href='../FrontEnd/login.php';</script>";
        exit();
    }
}
?>