<?php
require 'connect.php';
if(isset($_POST['addProductIn'])) {
   $product_name = $_POST['product_name'];
   $sku = $_POST['sku'];
   $description = $_POST['description'];
   $category_id = $_POST['category_id'];
   $unit_price = $_POST['unit_price'];
   $cost_price = $_POST['cost_price'];
   $reorder_level = $_POST['reorder_level'];
   $quantity_in_stock = $_POST['quantity_in_stock'];
   $created_at = $_POST['created_at'];

   $addProduct = mysqli_query($conn, "INSERT INTO products (product_name, sku, description, category_id, unit_price, cost_price, reorder_level, quantity_in_stock, created_at) VALUES ('$product_name', '$sku', '$description', '$category_id', '$unit_price', '$cost_price', '$reorder_level', '$quantity_in_stock', '$created_at')");
   if($addProduct) {
    echo( "<script>alert('Product added successfully!');
    window.location.href = '../FrontEnd/productIn.php';</script>" .
        "<div class='popup-form' style='display: block;'>
            <div class='blur-overlay' style='display: block;'></div>
            <div class='form-container'>
                <button id='closePopup' class='close-btn'>&times;</button>
                <h2>Product Added Successfully</h2>
                <p>The product has been added to the inventory.</p>
            </div>
        </div>
        <script>
            document.getElementById('closePopup').onclick = function() {
                document.querySelector('.popup-form').style.display = 'none';
                document.querySelector('.blur-overlay').style.display = 'none';
            };
    </script>"
            
);

       exit();
   } else {
       header("Location: ../FrontEnd/productIn.php?error=1");
       exit();
   }
   header("Location: ../FrontEnd/addProductIn.php?success=1");
   exit();
}

?>