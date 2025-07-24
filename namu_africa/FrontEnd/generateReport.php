<?php
require('fpdf.php');
require '../Backend/connect.php';

// Check if user is logged in
include('../Backend/Auth/Auth.php');
requireLogin();

// Get date filters
$fromDate = $_GET['fromDate'] ?? ($_GET['toDate'] ? date('Y-m-01') : date('Y-m-d')); // Start of current month
$toDate = $_GET['toDate'] ?? ($_GET['fromDate'] ? date('Y-m-d') : date('Y-m-d'));      // Today


// Company details
$companyName = "Namu Africa Ltd";
$country = "Rwanda";
$reportDate = date('Y-m-d');

// PDF Setup
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, "BUSINESS REPORT", 0, 1, 'C');
$pdf->Ln(5);

// Header
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, "Company: $companyName", 0, 1);
$pdf->Cell(0, 8, "Country: $country", 0, 1);
$pdf->Cell(0, 8, "From: $fromDate To: $toDate", 0, 1);
$pdf->Ln(8);

// ===== PRODUCTS OUT =====
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, "Products Out", 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 8, '#', 1);
$pdf->Cell(40, 8, 'Product Name', 1);
$pdf->Cell(20, 8, 'Qty', 1);
$pdf->Cell(30, 8, 'Unit Price', 1);
$pdf->Cell(30, 8, 'Total', 1);
$pdf->Cell(30, 8, 'Profit', 1);
$pdf->Ln();

$queryOut = mysqli_query($conn, "SELECT * FROM salesorderitems WHERE DATE(created_at) BETWEEN '$fromDate' AND '$toDate'");
$i = 1;
$totalOut = 0;
$totalProfit = 0;
$pdf->SetFont('Arial', '', 10);
while ($row = mysqli_fetch_assoc($queryOut)) {
    $productName = mysqli_fetch_assoc(mysqli_query($conn, "SELECT product_name FROM products WHERE product_id = '{$row['product_id']}'"))['product_name'];
    $pdf->Cell(10, 8, $i++, 1);
    $pdf->Cell(40, 8, substr($productName, 0, 20), 1);
    $pdf->Cell(20, 8, $row['quantity'], 1);
    $pdf->Cell(30, 8, number_format($row['unit_price'], 2), 1);
    $pdf->Cell(30, 8, number_format($row['total_price'], 2), 1);
    $pdf->Cell(30, 8, number_format($row['profit'], 2), 1);
    $pdf->Ln();
    $totalOut += $row['total_price'];
    $totalProfit += $row['profit'];
}
$pdf->Ln(5);

// ===== PRODUCTS IN =====
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, "Products In", 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 8, '#', 1);
$pdf->Cell(40, 8, 'Product Name', 1);
$pdf->Cell(20, 8, 'Qty', 1);
$pdf->Cell(30, 8, 'Unit Price', 1);
$pdf->Cell(30, 8, 'Cost Price', 1);
$pdf->Cell(30, 8, 'Total In', 1);
$pdf->Ln();

$queryIn = mysqli_query($conn, "SELECT * FROM products WHERE DATE(created_at) BETWEEN '$fromDate' AND '$toDate'");
$i = 1;
$totalIn = 0;
$pdf->SetFont('Arial', '', 10);
while ($row = mysqli_fetch_assoc($queryIn)) {
    $rowTotal = $row['quantity_in_stock'] * $row['cost_price'];
    $pdf->Cell(10, 8, $i++, 1);
    $pdf->Cell(40, 8, substr($row['product_name'], 0, 20), 1);
    $pdf->Cell(20, 8, $row['quantity_in_stock'], 1);
    $pdf->Cell(30, 8, number_format($row['unit_price'], 2), 1);
    $pdf->Cell(30, 8, number_format($row['cost_price'], 2), 1);
    $pdf->Cell(30, 8, number_format($rowTotal, 2), 1);
    $pdf->Ln();
    $totalIn += $rowTotal;
}
$pdf->Ln(5);

// ===== EXPENSES =====
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, "Expenses", 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 8, '#', 1);
$pdf->Cell(40, 8, 'Name', 1);
$pdf->Cell(30, 8, 'Amount', 1);
$pdf->Cell(90, 8, 'Description', 1);
$pdf->Ln();

$queryExpenses = mysqli_query($conn, "SELECT * FROM expenses WHERE DATE(created_at) BETWEEN '$fromDate' AND '$toDate'");
$i = 1;
$totalExpenses = 0;
$pdf->SetFont('Arial', '', 10);
while ($row = mysqli_fetch_assoc($queryExpenses)) {
    $pdf->Cell(10, 8, $i++, 1);
    $pdf->Cell(40, 8, substr($row['expense_name'], 0, 20), 1);
    $pdf->Cell(30, 8, number_format($row['amount'], 2), 1);
    $pdf->Cell(90, 8, substr($row['description'], 0, 60), 1);
    $pdf->Ln();
    $totalExpenses += $row['amount'];
}
$pdf->Ln(8);

// ===== SUMMARY =====
$netProfit = $totalProfit - $totalExpenses;

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, "Summary Totals", 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(60, 8, "Total Product Out:", 1);
$pdf->Cell(60, 8, number_format($totalOut, 2) . " RWF", 1);
$pdf->Ln();
$pdf->Cell(60, 8, "Total Product In:", 1);
$pdf->Cell(60, 8, number_format($totalIn, 2) . " RWF", 1);
$pdf->Ln();
$pdf->Cell(60, 8, "Total Expenses:", 1);
$pdf->Cell(60, 8, number_format($totalExpenses, 2) . " RWF", 1);
$pdf->Ln();
$pdf->Cell(60, 8, "Total Profit:", 1);
$pdf->Cell(60, 8, number_format($totalProfit, 2) . " RWF", 1);
$pdf->Ln();
$pdf->Cell(60, 8, "Net Profit:", 1);
$pdf->Cell(60, 8, number_format($netProfit, 2) . " RWF", 1);
$pdf->Ln();

$pdf->Output('I', 'Business_Report_' . date('Ymd') . '.pdf');
?>
