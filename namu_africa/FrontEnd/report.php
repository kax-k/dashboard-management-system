<?php
include('../Backend/Auth/Auth.php');
require '../Backend/connect.php';
requireLogin();

$from = $_GET['fromDate'] ?? '';
$to = $_GET['toDate'] ?? '';
$dateFilter = "";

if (!empty($from) && !empty($to)) {
    $dateFilter = "WHERE DATE(created_at) BETWEEN '$from' AND '$to'";
}

function getResults($conn, $query) {
    return mysqli_query($conn, $query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports | Namu Africa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f0f0; color: #000; }
        .main-content { margin-left: 270px; padding: 2rem; }
        .report-section { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.1); padding: 2rem; }
        h2, h4 { color: #0d6efd; font-weight: bold; }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            h2, h4 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
<?php include('sidebar.php'); ?>
<div class="main-content">
    <div class="container-fluid">
        <div class="report-section mb-4">
            <h2>Business Reports</h2>

            <!-- Filters -->
            <form class="row g-3 mb-4" method="GET" action="">
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control" name="fromDate" value="<?= $from ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control" name="toDate" value="<?= $to ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="generateReport.php?fromDate=<?= $from ?>&toDate=<?= $to ?>" class="btn btn-success w-100">Download PDF</a>
                </div>
            </form>

            <!-- Products Out -->
            <h4>Products Out</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th><th>Product Name</th><th>Quantity</th><th>Unit Price</th><th>Total Price</th><th>Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $outData = getResults($conn, "SELECT * FROM salesorderitems $dateFilter ORDER BY created_at DESC");
                        $i = 1; $totalOut = 0; $totalProfit = 0;
                        while ($row = mysqli_fetch_assoc($outData)) {
                            $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT product_name FROM products WHERE product_id = '{$row['product_id']}'"));
                            $totalOut += $row['total_price'];
                            $totalProfit += $row['profit'];
                       ?>
                                <td><?= $i++ ?></td>
                                <td><?= $product['product_name'] ?></td>
                                <td><?= $row['quantity'] ?></td>
                                <td><?= number_format($row['unit_price'], 2) ?> RWF</td>
                                <td><?= number_format($row['total_price'], 2) ?> RWF</td>
                                <td><?= number_format($row['profit'], 2) ?> RWF</td>
                            </tr>
                           <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Products In -->
            <h4 class="mt-5">Products In</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th><th>Name</th><th>SKU</th><th>Qty</th><th>Unit Price</th><th>Cost</th><th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $productIn = getResults($conn, "SELECT * FROM products $dateFilter ORDER BY created_at DESC");
                        $i = 1; $totalIn = 0;
                        while ($row = mysqli_fetch_assoc($productIn)) {
                            $rowTotal = $row['quantity_in_stock'] * $row['cost_price'];
                            $totalIn += $rowTotal;
                        ?>
                                <td><?= $i++ ?></td>
                                <td><?= $row['product_name'] ?></td>
                                <td><?= $row['sku'] ?></td>
                                <td><?= $row['quantity_in_stock'] ?></td>
                                <td><?= number_format($row['unit_price'], 2) ?> RWF</td>
                                <td><?= number_format($row['cost_price'], 2) ?> RWF</td>
                                <td><?= number_format($rowTotal, 2) ?> RWF</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Expenses -->
            <h4 class="mt-5">Expenses</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>Amount</th><th>Description</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $expenses = getResults($conn, "SELECT * FROM expenses $dateFilter ORDER BY created_at DESC");
                        $i = 1; $totalExpenses = 0;
                        while ($row = mysqli_fetch_assoc($expenses)) {
                            $totalExpenses += $row['amount'];
                           ?>
                           <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $row['expense_name'] ?></td>
                                <td><?= number_format($row['amount'], 2) ?> RWF</td>
                                <td><?= $row['description'] ?></td>
                                <td><?= $row['expense_date'] ?></td>
                            </tr>
                            <?php
                          
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Summary Totals -->
            <h4 class="mt-5">Summary</h4>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Total Product Out</th>
                            <th>Total Product In</th>
                            <th>Total Expenses</th>
                            <th>Profit</th>
                            <th>Net Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= number_format($totalOut, 2) ?> RWF</td>
                            <td><?= number_format($totalIn, 2) ?> RWF</td>
                            <td><?= number_format($totalExpenses, 2) ?> RWF</td>
                            <td><?= number_format($totalProfit, 2) ?> RWF</td>
                            <td><?= number_format($totalProfit - $totalExpenses, 2) ?> RWF</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
</body>
</html>
