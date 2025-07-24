<?php
require 'connect.php';
if (isset($_POST['add_category'])) {
    $categoryName = mysqli_real_escape_string($conn, $_POST['categoryName']);
    $categoryDesc = mysqli_real_escape_string($conn, $_POST['categoryDesc']);

    $query = "INSERT INTO categories (category_name, description) VALUES ('$categoryName', '$categoryDesc')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Category added successfully!');
        window.location.href = '../FrontEnd/categories.php';
        </script>";
    } else {
        echo "<script>alert('Error adding category: " . mysqli_error($conn) . "');
        window.location.href = '../FrontEnd/categories.php';
        </script>";
    }
}

?>