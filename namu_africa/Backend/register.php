<?php
if(isset($_POST['register'])) {
    require 'connect.php'; 
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = $_POST['password'];

   $hashed_password = password_hash($password, PASSWORD_DEFAULT);

   $insert_query = mysqli_query($conn, "INSERT INTO users (username, role, pswd) VALUES ('$username', '$role', '$hashed_password')");
   if($insert_query) {
          echo "<script>alert('Registration successful!');</script>";
         header("Location: ..//FrontEnd/users.php");
   }else{
         echo "<script>alert('Registration failed!');</script>";
         header("Location: ..//FrontEnd/users.php");
         exit();
   }

}


?>