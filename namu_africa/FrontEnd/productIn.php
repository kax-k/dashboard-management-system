<?php
include('../Backend/Auth/Auth.php');
requireLogin();
require '../Backend/connect.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$date = $_GET['date'] ?? '';

$query = "SELECT * FROM products WHERE 1";

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $query .= " AND (product_name LIKE '%$search%' OR sku LIKE '%$search%')";
}

if (!empty($category)) {
    $category = mysqli_real_escape_string($conn, $category);
    $query .= " AND category_id = '$category'";
}

if (!empty($date)) {
    $date = mysqli_real_escape_string($conn, $date);
    $query .= " AND DATE(created_at) = '$date'";
}

$query .= " ORDER BY created_at DESC";
$productQuery = mysqli_query($conn, $query);
$selectCategories = mysqli_query($conn, "SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Product Dashboard</title>
    <style>
        body { background: #f0f0f0; color: #000; }
        .main-content { margin-left: 270px; padding: 2rem; }
        .blur-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(6px);
            z-index: 1040;
        }
        .popup-form {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1050;
        }
        .container-box {
            background: #fff;
            color: #000;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.3);
            padding: 2rem 2.5rem;
            max-width: 600px;
            margin: auto;
        }
        h2, h3, h5 { color: #0d6efd; font-weight: bold; }
        .btn-primary:hover { background: #003366; }
        label { color: #0d6efd; font-weight: 500; }
    </style>
</head>
<body>
<?php include('sidebar.php'); ?>
<div class="main-content">
    <div class="container-fluid">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h3>Product Overview</h3>
            </div>
            <div class="col-auto">
                <form class="d-flex" role="search" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search products..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <?php
          $totalProducts = "SELECT 
             COUNT(*) as totalCount, 
             SUM(unit_price * quantity_in_stock) as totalUnit, 
             SUM(cost_price * quantity_in_stock) as totalCost
              
          FROM products";

if (!empty($date)) {
    $safeDate = mysqli_real_escape_string($conn, $date);
    $totalProducts .= " WHERE DATE(created_at) = '$safeDate'";
}

$totalProductsResult = mysqli_query($conn, $totalProducts);

  $totalUnitPrice = mysqli_fetch_assoc($totalProductsResult);


            ?>
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body text-center">
                        <h5>Products In</h5>
                        <p><?= $totalUnitPrice['totalCount'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body text-center">
                        <h5>Total Unit Price</h5>
                        <p><?= number_format($totalUnitPrice['totalUnit'], 2) ?> RWF</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-body text-center">
                        <h5>Total Cost Price</h5>
                        <p><?= number_format($totalUnitPrice['totalCost'], 2) ?> RWF</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body text-center">
                        <h5>Total Profit</h5>
                        <p><?= number_format($totalUnitPrice['totalUnit'] - $totalUnitPrice['totalCost'], 2) ?> RWF</p>
                    </div>
                </div>
            </div>
        </div>

        <hr>
        <div class="row bg-white p-3 shadow-sm">
            <div class="col">
                <h5>Recent Products In</h5>
            </div>
            <div class="col-auto">
                <button id="openPopup" class="btn btn-primary">Add New Product In</button>
            </div>
        </div>

        <div class="mt-4 bg-white p-4">
            <form method="GET">
                <div class="row mb-3">  
                    <div class="col-md-4">
                        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="category">
                            <option value="">Filter by category</option>
                            <?php while($category = mysqli_fetch_assoc($selectCategories)): ?>
                                <option value="<?= $category['category_id'] ?>" <?= ($_GET['category'] ?? '') === $category['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['category_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Qty In</th>
                            <th>Unit Price</th>
                            <th>Cost Price</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 1;
                    while($row = mysqli_fetch_assoc($productQuery)) {
                        $totalPrice = $row['unit_price'] * $row['quantity_in_stock'];
                        $categoryQuery = mysqli_query($conn, "SELECT category_name FROM categories WHERE category_id = '{$row['category_id']}'");
                        $category = mysqli_fetch_assoc($categoryQuery);
                      ?>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= htmlspecialchars($row['sku']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= htmlspecialchars($category['category_name']) ?></td>
                            <td><?= htmlspecialchars($row['quantity_in_stock']) ?></td>
                            <td><?= number_format($row['unit_price'], 2) ?> RWF</td>
                            <td><?= number_format($row['cost_price'], 2) ?> RWF</td>
                            <td><?= number_format($totalPrice, 2) ?> RWF</td>
                            <td>
                                <button class='btn btn-sm btn-primary'>Edit</button>
                                <form action='' method='post' style='display:inline;'>
                                    <input type='hidden' name='product_id' value='<?= $row['product_id'] ?>'>
                                    <button type='submit' name='deleteProduct' onclick="return confirm('Are you sure?');" class='btn btn-sm btn-danger'>Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php
                       
                    }
                    if(isset($_POST['deleteProduct'])) {
                        $product_id = $_POST['product_id'];
                        $deleteQuery = mysqli_query($conn, "DELETE FROM products WHERE product_id = '$product_id'");
                        echo $deleteQuery ? "<script>alert('Deleted successfully');</script>" : "<script>alert('Delete failed');</script>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <a href="GenerateProductReport.php?dateIn=<?= $date ?>" class="btn btn-primary">Download PDF Report</a>
        </div>
    </div>
</div>

<!-- Product In Popup -->
<div class="blur-overlay"></div>
<div style="display-flex" class="popup-form  justify-content-center align-items-center">
    <div class="container-box position-relative">
        <h2>Product In</h2>
        <button id="closePopup" type="button" style="position:absolute;top:20px;right:20px;color:red;background:none;border:none;font-size:2rem;">&times;</button>
        <form action="../Backend/productIn.php" method="POST">
            <div class="row">
                <div class="col-md-6 mb-1">
                    <label for="productName" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="productName" name="product_name" required>
                </div>
                <div class="col-md-6 mb-1">
                    <label for="sku" class="form-label">SKU</label>
                    <input type="text" class="form-control" id="sku" name="sku" required>
                </div>
            </div>
            <div class="mb-1">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="2" required></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-1">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php mysqli_data_seek($selectCategories, 0); while($category = mysqli_fetch_assoc($selectCategories)): ?>
                            <option value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-1">
                    <label for="unitPrice" class="form-label">Unit Price</label>
                    <input type="number" class="form-control" id="unitPrice" name="unit_price" required min="0" step="0.01">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-1">
                    <label for="costPrice" class="form-label">Cost Price</label>
                    <input type="number" class="form-control" id="costPrice" name="cost_price" required min="0" step="0.01">
                </div>
                <div class="col-md-6 mb-1">
                    <label for="reorderLevel" class="form-label">Reorder Level</label>
                    <input type="number" class="form-control" id="reorderLevel" name="reorder_level" required min="0">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-1">
                    <label for="quantityInStock" class="form-label">Quantity In Stock</label>
                    <input type="number" class="form-control" id="quantityInStock" name="quantity_in_stock" required min="0">
                </div>
                <div class="col-md-6 mb-1">
                    <label for="createdAt" class="form-label">Created At</label>
                    <input type="date" class="form-control" id="createdAt" name="created_at" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100" name="addProductIn">Add Product</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('openPopup').onclick = function() {
        document.querySelector('.popup-form').style.display = 'flex';
        document.querySelector('.blur-overlay').style.display = 'block';
    };
    document.getElementById('closePopup').onclick = function() {
        document.querySelector('.popup-form').style.display = 'none';
        document.querySelector('.blur-overlay').style.display = 'none';
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
