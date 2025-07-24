<?php

include('../Backend/Auth/Auth.php');
requireLogin();
require '../Backend/connect.php';

$search = $_GET['search'] ?? '';
$date = $_GET['date'] ?? '';

$query = "SELECT * FROM salesorderitems WHERE 1";

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $query .= " AND product_name LIKE '%$search%'";
}

if (!empty($date)) {
    $date = mysqli_real_escape_string($conn, $date);
    $query .= " AND DATE(created_at) = '$date'";
}

$query .= " ORDER BY created_at DESC";
$getProducts = mysqli_query($conn, $query);
 
                        $total = mysqli_query($conn, "SELECT SUM(total_price) as total, SUM(quantity) as quantity, SUM(unit_price) as unit_price, SUM(profit) as profit FROM salesorderitems WHERE DATE(created_at) = '$date'");
                        $totalData = mysqli_fetch_assoc($total);
                      
                    
$i = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Product Out | Namu Africa</title>
    <style>
        body {
            background: #fff;
            color: #000;
        }
        .main-content {
            margin-left: 270px;
            padding: 2rem;
        }
        .container-box {
            background: #fff;
            color: #000;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.3);
            padding: 2rem 2.5rem;
            margin-top: 3rem;
            max-width: 600px;
        }
        h2, h3, h5 {
            color: #0d6efd;
            font-weight: bold;
        }
        .btn-primary {
            background: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background: #003366;
        }
        label {
            color: #0d6efd;
            font-weight: 500;
        }
        .blur-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(6px);
            z-index: 1040;
            display: none;
        }
        .popup-form {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1050;
            display: none;
            width: 100%;
            max-width: 500px;
        }
    </style>
</head>
<body>
<?php include('sidebar.php'); ?>
<div class="main-content">
    <div class="container-fluid">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h3 class="fw-bold text-primary">Product Out Overview</h3>
            </div>
            <div class="col-auto">
                <form class="d-flex" role="search" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search product..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </form>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body text-center">
                        <h5 class="card-title">Products Out</h5>
                        <p class="card-text">12</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-body text-center">
                        <h5 class="card-title">Categories</h5>
                        <p class="card-text">6</p>
                    </div>
                </div>
            </div>
             <div class="col-md-3">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Profit</h5>
                        <p class="card-text"><?= number_format($totalData['profit'], 2) ?> RWF</p>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row mb-3">
            <div class="col">
                <h5 class="fw-bold text-primary">Recent Products Out</h5>
            </div>
            <div class="col-auto">
                <button id="openPopup" class="btn btn-primary">Add New Product Out</button>
            </div>
        </div>

        <form method="GET" class="row mb-4">
            <div class="col-md-4 ">
                <input type="date" class="form-control w-[530px]" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
            </div>
        
            <div class="col-md-4">
                <button class="btn btn-primary w-100">Apply Filters</button>
            </div>
        </form>

        <div class="container bg-white p-4 rounded">
            <div class="table-responsive">
            <table class="table table-striped table-bordered text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Quantity Out</th>
                        <th>Unit Price</th>
                        <th>Total Price</th>
                        <th>profit</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($getProducts)): ?>
                        <?php
    $product_name =mysqli_query($conn, "SELECT * FROM products WHERE product_id ='{$row['product_id']}'");
    $product_name = mysqli_fetch_assoc($product_name);
?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($product_name['product_name']) ?></td>
                            <td><?= htmlspecialchars($row['quantity']) ?></td>
                            <td><?= number_format($row['unit_price'], 2) ?> RWF</td>
                            <td><?= number_format($row['total_price'], 2) ?> RWF</td>
                            <td><?= number_format($row['profit'], 2) ?> RWF</td>
                            <td>
                                <form action="../Backend/productOut.php" method="post">
                                    <input type="hidden" name="productId" value="<?= $row['sales_item_id'] ?>">
                                    <button type="submit" class="btn btn-primary" name="editProductOut">Edit</button>
                                    <button type="submit" class="btn btn-danger" name="deleteProductOut">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                
                    <tr class="fw-bold">
                        <td colspan="2">Total</td>
                        <td><?= $totalData['quantity'] ?></td>
                        <td><?= number_format($totalData['unit_price'], 2) ?> RWF</td>
                        <td><?= number_format($totalData['total'], 2) ?> RWF</td>
                       <td><?= number_format($totalData['profit'], 2) ?> RWF</td>

                        <td>-</td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div class="row">
                <div class="col">
                    <a href="GenerateProductOutReport.php?dateOut=<?= $date ?>" class="btn btn-primary">Download PDF Report</a>
                </div>
            </div>
        </div>

        <div class="blur-overlay"></div>
        <div class="popup-form">
            <div class="container-box">
                <h2>Add Product Out</h2>
                <button id="closePopup" type="button" class="btn btn-close bg-danger  close-btn"></button>
                <form action="../Backend/productOut.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                          <select name="productName" id="productName" class="form-select select2" required>
    <option value="">Choose product name</option>
    <?php
    $productQuery = mysqli_query($conn, "SELECT * FROM products");
    while($product = mysqli_fetch_assoc($productQuery)): ?>
        <option value="<?= $product['product_id'] ?>"><?= htmlspecialchars($product['product_name']) ?></option>
    <?php endwhile; ?>
</select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">Quantity Out</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="unit" class="form-label">Unit Price</label>
                            <input type="number" class="form-control" id="unit" name="unit" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dateOut" class="form-label">Date Out</label>
                            <input type="date" class="form-control" id="dateOut" name="dateOut" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" name='addProductOut'>Add Product Out</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('openPopup').onclick = function() {
        document.querySelector('.popup-form').style.display = 'block';
        document.querySelector('.blur-overlay').style.display = 'block';
    };
    document.getElementById('closePopup').onclick = function() {
        document.querySelector('.popup-form').style.display = 'none';
        document.querySelector('.blur-overlay').style.display = 'none';
    };
     $(document).ready(function() {
    $('.select2').select2({
      placeholder: "Search or choose product",
      width: '100%'
    });
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
