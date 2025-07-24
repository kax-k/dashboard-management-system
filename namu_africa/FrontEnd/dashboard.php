<?php
include('../Backend/Auth/Auth.php');
requireLogin();
require '../Backend/connect.php';

$search = $_GET['search'] ?? '';
$searchCondition = '';

if (!empty($search)) {
    $escaped = mysqli_real_escape_string($conn, $search);
    $searchCondition = "WHERE product_name LIKE '%$escaped%' OR sku LIKE '%$escaped%'";
}

$countProductIn = mysqli_query($conn, "
    SELECT COUNT(*) as total, 
           SUM(quantity_in_stock) as total_quantity, 
           SUM(unit_price) as total_value, 
           SUM(cost_price) as total_cost 
    FROM products 
    $searchCondition
");

$countProductOut = mysqli_query($conn, "
    SELECT COUNT(*) as total, SUM(total_price) as total_value 
    FROM salesorderitems 
    WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");

$countCategories = mysqli_query($conn, "SELECT COUNT(*) as total FROM categories");
$expenses = mysqli_query($conn, "
    SELECT SUM(amount) as total 
    FROM expenses 
    WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");

$expAmount = mysqli_fetch_assoc($expenses);
$category = mysqli_fetch_assoc($countCategories);
$productsIn = mysqli_fetch_assoc($countProductIn);  
$productsOut = mysqli_fetch_assoc($countProductOut);

$profitT = ($productsIn['total_value'] * $productsIn['total_quantity']) - ($productsIn['total_cost'] * $productsIn['total_quantity']);
$cost = $productsIn['total_cost'] * $productsIn['total_quantity'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard</title>
    <style>
        body {
            background: #f0f0f0;
            color: #000;
        }
        .main-content {
            margin-left: 270px;
            padding: 2rem;
        }
    </style>
</head>
<body>
<?php include('sidebar.php'); ?>
<div class="main-content">
    <div class="container-fluid">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h3 class="fw-bold text-primary">Dashboard Overview</h3>
            </div>
            <div class="col-auto">
                <form class="d-flex" role="search" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search products, categories..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </form>
            </div>
        </div>

        <?php if (!empty($search)): ?>
            <div class="alert alert-info">
                Search results for: <strong><?= htmlspecialchars($search) ?></strong>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3 shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title">Products In</h5>
                        <h2 class="display-6 fw-bold"><?= $productsIn['total']; ?></h2>
                        <p class="card-text">Total products received this month</p>
                        <a href="productIn.php" class="btn btn-light btn-sm mt-2">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3 shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title">Products Out</h5>
                        <h2 class="display-6 fw-bold"><?= $productsOut['total']; ?></h2>
                        <p class="card-text">Total products dispatched this month</p>
                        <a href="productout.php" class="btn btn-light btn-sm mt-2">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-secondary mb-3 shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title">Categories</h5>
                        <h2 class="display-6 fw-bold"><?= $category['total']; ?></h2>
                        <p class="card-text">Active product categories</p>
                        <a href="categories.php" class="btn btn-light btn-sm mt-2">View Details</a>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="row mt-4">
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Total Products In: <span class="fw-bold text-primary"><?= $productsIn['total'] ?></span></li>
                            <li class="list-group-item">Total Products Out: <span class="fw-bold text-info"><?= $productsOut['total'] ?></span></li>
                            <li class="list-group-item">Categories: <span class="fw-bold text-secondary"><?= $category['total'] ?></span></li>
                            <li class="list-group-item">Total Expected profit: <span class="fw-bold text-success"><?= number_format($profitT,2) ?> RWF</span></li>
                            <li class="list-group-item">Total Expense: <span class="fw-bold text-danger"><?= number_format($expAmount['total'], 2) ?> RWF</span></li>
                            <li class="list-group-item">Total Sold: <span class="fw-bold text-info"><?= number_format($productsOut['total_value'],2) ?> RWF</span></li>
                            <li class="list-group-item">Purchased Cost: <span class="fw-bold text-primary"><?= number_format($cost, 2) ?> RWF</span></li>
                            <li class="list-group-item">Total Profit: <span class="fw-bold text-success"><?= number_format($profitT - $expAmount['total'], 2) ?> RWF</span></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Product B dispatched on 2025-07-18</li>
                            <li class="list-group-item">Category "Food" added on 2025-07-15</li>
                            <li class="list-group-item">Stock updated for Product A</li>
                            <li class="list-group-item">Product C received on 2025-07-10</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
