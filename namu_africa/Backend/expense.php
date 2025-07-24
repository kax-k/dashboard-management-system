<?php
if(isset($_POST['addexpense'])){
    require 'connect.php';
    $expname = $_POST['expenseName'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $expense_date = $_POST['expense_date'];

    $insert_query = mysqli_query($conn, "INSERT INTO `expenses`( `expense_name`, `amount`, `description`, `expense_date`, `created_at`) VALUES ('$expname','$amount','$description','$description','$expense_date')");
    if($insert_query){
        echo("<script>alert('expenses added')</script>");
        header("Location: ../FrontEnd/expenses.php");
    }else{
        echo("<script>alert('expenses failed')</script>");
        header("Location: ../FrontEnd/expenses.php");
    }
}

if(isset($_POST['editexpense'])){
    require 'connect.php';
    $id = $_POST['expense_id'];
    $expname = $_POST['expenseName'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $expense_date = $_POST['expense_date'];

    $update_query = mysqli_query($conn, "UPDATE `expenses` SET `expense_name`='$expname', `amount`='$amount', `description`='$description', `expense_date`='$expense_date' WHERE `expense_id`='$id'");
    if($update_query){
        echo("<script>alert('expenses updated')</script>");
        header("Location: ../../FrontEnd/expenses.php");
    }else{
        echo("<script>alert('expenses update failed')</script>");
        header("Location: ../../FrontEnd/expenses.php");
    }
}

?>