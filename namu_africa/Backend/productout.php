<?php

require 'connect.php';
if(isset($_POST['addProductOut'])) {
    $productName = $_POST['productName'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $dateOut = $_POST['dateOut'];
    $totalPrice = $quantity * $unit;
    $product_name =mysqli_query($conn, "SELECT * FROM products WHERE product_id ='$productName'");
$product_name = mysqli_fetch_assoc($product_name);
$profit = $unit - $product_name['cost_price'];
$totalProfit = $profit * $quantity;

    $addProductOut = mysqli_query($conn, "INSERT INTO salesorderitems(product_id, quantity, unit_price,total_price,profit, created_at) VALUES ('$productName', '$quantity', '$unit', '$totalPrice', '$totalProfit', '$dateOut')");

    if ($addProductOut) {
        echo "<script>alert('Product out added successfully!'); window.location.href = '../FrontEnd/productout.php';</script>";
        exit();
    } else {
        header("Location: ../FrontEnd/productout.php?error=1");
        exit();
    }
}


if(isset($_POST['deleteProductOut'])) {
    $productId = $_POST['productId'];
    $deleteProductOut = mysqli_query($conn, "DELETE FROM salesorderitems WHERE sales_item_id = '$productId'");

    if ($deleteProductOut) {
        echo "<script>alert('Product out deleted successfully!'); window.location.href = '../FrontEnd/productout.php';</script>";
        exit();
    } else {
        header("Location: ../FrontEnd/productout.php?error=1");
        exit();
    }
}

?>