<?php
require('fpdf.php');
require '../Backend/connect.php';
// Ensure user is logged in
include('../Backend/Auth/Auth.php');
requireLogin();

// Company Info
$companyName = "Namu Africa Ltd";
$country = "Rwanda";
$reportDate = date('Y-m-d');

// Date Filter
$dateOut = isset($_GET['dateOut']) && !empty($_GET['dateOut']) ? mysqli_real_escape_string($conn, $_GET['dateOut']) : '';

// Fetch records with optional date filter
$whereClause = "";
if ($dateOut) {
    $whereClause = "WHERE DATE(salesorderitems.created_at) = '$dateOut'";
}

// Query for detailed product rows
$productQuery = mysqli_query($conn, "
    SELECT 
        salesorderitems.product_id,
        salesorderitems.quantity,
        salesorderitems.unit_price,
        salesorderitems.total_price,
        salesorderitems.created_at,
        (salesorderitems.unit_price - products.cost_price) AS profit,
        products.product_name
    FROM salesorderitems
    JOIN products ON salesorderitems.product_id = products.product_id
    $whereClause
    ORDER BY salesorderitems.created_at DESC
");

// Query for totals
$totalQuery = mysqli_query($conn, "
    SELECT 
        SUM(salesorderitems.quantity) AS total_quantity,
        SUM(salesorderitems.total_price) AS total_price,
        SUM((salesorderitems.unit_price - products.cost_price) * salesorderitems.quantity) AS total_profit
    FROM salesorderitems
    JOIN products ON salesorderitems.product_id = products.product_id
    $whereClause
");

$totals = mysqli_fetch_assoc($totalQuery);

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Products Out Report', 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, "Company: $companyName", 0, 1, 'L');
$pdf->Cell(0, 8, "Country: $country", 0, 1, 'L');
$pdf->Cell(0, 8, "Report Date: $reportDate", 0, 1, 'L');
if ($dateOut) {
    $pdf->Cell(0, 8, "Filtered by: $dateOut", 0, 1, 'L');
}
$pdf->Ln(5);

// Table Headers
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 8, '#', 1);
$pdf->Cell(35, 8, 'Product Name', 1);
$pdf->Cell(25, 8, 'Qty Out', 1);
$pdf->Cell(30, 8, 'Unit Price', 1);
$pdf->Cell(30, 8, 'Total Price', 1);
$pdf->Cell(35, 8, 'Profit', 1);
$pdf->Cell(30, 8, 'Date', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$i = 1;

// Loop through rows
while ($row = mysqli_fetch_assoc($productQuery)) {
    $pdf->Cell(10, 8, $i++, 1);
    $pdf->Cell(35, 8, $row['product_name'], 1);
    $pdf->Cell(25, 8, $row['quantity'], 1);
    $pdf->Cell(30, 8, number_format($row['unit_price'], 2) . ' RWF', 1);
    $pdf->Cell(30, 8, number_format($row['total_price'], 2) . ' RWF', 1);
    $pdf->Cell(35, 8, number_format($row['profit'] * $row['quantity'], 2) . ' RWF', 1);
    $pdf->Cell(30, 8, date('Y-m-d', strtotime($row['created_at'])), 1);
    $pdf->Ln();
}

// Totals Row
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(45, 8, 'Total', 1);
$pdf->Cell(25, 8, $totals['total_quantity'], 1);
$pdf->Cell(30, 8, '-', 1);
$pdf->Cell(30, 8, number_format($totals['total_price'], 2) . ' RWF', 1);
$pdf->Cell(35, 8, number_format($totals['total_profit'], 2) . ' RWF', 1);
$pdf->Cell(30, 8, '-', 1);
$pdf->Ln();

// Output PDF
$pdf->Output('I', 'ProductOutReport_' . date('Y-m-d') . '.pdf');
