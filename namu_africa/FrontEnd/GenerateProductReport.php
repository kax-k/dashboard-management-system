<?php
require('fpdf.php');
require '../Backend/connect.php';
// Check if user is logged in
include('../Backend/Auth/Auth.php');
requireLogin();

// Company details
$companyName = "Namu Africa Ltd";
$country = "Rwanda";
$reportDate = date('Y-m-d');

// Get filter date or default to today
$dateIn = isset($_GET['dateIn']) && !empty($_GET['dateIn']) 
    ? mysqli_real_escape_string($conn, $_GET['dateIn']) 
    : date('Y-m-d');

// Fetch product rows
$getProducts = mysqli_query($conn, "SELECT * FROM products WHERE DATE(created_at) = '$dateIn' ORDER BY created_at DESC");

// Fetch total values
$totalsQuery = mysqli_query($conn, "
    SELECT  
        SUM(unit_price * quantity_in_stock) AS total_sales,
        SUM(cost_price * quantity_in_stock) AS total_cost,
        SUM(reorder_level + quantity_in_stock) AS total_order
    FROM products 
    WHERE DATE(created_at) = '$dateIn'
");
$totals = mysqli_fetch_assoc($totalsQuery);

// Calculate overall profit
$total_profit = $totals['total_sales'] - $totals['total_cost'];

$pdf = new FPDF();
$pdf->AddPage();

// Report Title
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Products Report',0,1,'C');

// Company Info
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,"Company: $companyName",0,1,'L');
$pdf->Cell(0,8,"Country: $country",0,1,'L');
$pdf->Cell(0,8,"Report Date: $reportDate",0,1,'L');
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial','B',11);
$pdf->Cell(10,8,'#',1);
$pdf->Cell(30,8,'Product Name',1);
$pdf->Cell(15,8,'Qty',1);
$pdf->Cell(30,8,'Unit Price',1);
$pdf->Cell(25,8,'Cost Price',1);
$pdf->Cell(25,8,'Profit',1);
$pdf->Cell(35,8,'Description',1);
$pdf->Cell(25,8,'Date',1);
$pdf->Ln();

// Product Rows
$pdf->SetFont('Arial','',10);
$i = 1;


while($row = mysqli_fetch_assoc($getProducts)) {
    $product_profit = $row['unit_price'] - $row['cost_price'];
    $total_product_profits = $product_profit * $row['quantity_in_stock'];

    $pdf->Cell(10,8,$i++,1);
    $pdf->Cell(30,8,substr($row['product_name'], 0, 15),1);
    $pdf->Cell(15,8,$row['quantity_in_stock'],1);
    $pdf->Cell(30,8,number_format($row['unit_price'],2).' RWF',1);
    $pdf->Cell(25,8,number_format($row['cost_price'],2).' RWF',1);
    $pdf->Cell(25,8,number_format($total_product_profits, 2).' RWF',1);
    $pdf->Cell(35,8,substr($row['description'], 0, 20),1);
    $pdf->Cell(25,8,date('Y-m-d', strtotime($row['created_at'])),1);
    $pdf->Ln();
}

// Totals Section
$pdf->Ln(8);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(48,10,'Total Sales Value',1);
$pdf->Cell(48,10,'Total Cost Value',1);
$pdf->Cell(48,10,'Total quantity Stock',1);
$pdf->Cell(48,10,'Total Profit Expected',1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);
$pdf->Cell(48,10,number_format($totals['total_sales'],2).' RWF',1);
$pdf->Cell(48,10,number_format($totals['total_cost'],2).' RWF',1);
$pdf->Cell(48,10,number_format($totals['total_order'],0),1);
$pdf->Cell(48,10,number_format($total_product_profits,2).' RWF',1);
$pdf->Ln();

$pdf->Output('I', 'ProductReport_' . $dateIn . '.pdf');
?>
