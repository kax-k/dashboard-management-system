<?php
session_start();
session_unset();
session_destroy();
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    unset($_SESSION['loggedin']);
    $_SESSION['loggedin'] = false;
    unset($_SESSION['username']);
    session_write_close();
}
header("Location: ../../FrontEnd/login.php");
exit();
?>