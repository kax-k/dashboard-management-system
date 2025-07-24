<?php
require('fpdf.php');
include('../Backend/Auth/Auth.php');
requireLogin();
require '../Backend/connect.php';

// Company details
$companyName = "Namu Africa Ltd";
$country = "Rwanda";
$reportDate = date('Y-m-d');


// Date filter (optional)
$dateFilter = isset($_GET['dateEpx']) && !empty($_GET['dateEpx']) 
    ? mysqli_real_escape_string($conn, $_GET['dateEpx']) 
    : date('Y-m-d');

// Fetch filtered expenses
$getExpenses = mysqli_query($conn, "
    SELECT * FROM expenses 
    WHERE DATE(expense_date) = '$dateFilter' 
    ORDER BY created_at DESC
");

// Fetch total expenses
$totalQuery = mysqli_query($conn, "
    SELECT SUM(amount) as total_amount 
    FROM expenses 
    WHERE DATE(expense_date) = '$dateFilter'
");


$totalData = mysqli_fetch_assoc($totalQuery);
$totalAmount = $totalData['total_amount'] ?? 0;

// Start PDF
$pdf = new FPDF();
$pdf->AddPage();

// Title
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Expenses Report',0,1,'C');

// Company Info
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,"Company: $companyName",0,1,'L');
$pdf->Cell(0,8,"Country: $country",0,1,'L');
$pdf->Cell(0,8,"Report Date: $reportDate",0,1,'L');
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial','B',11);
$pdf->Cell(10,8,'#',1);
$pdf->Cell(50,8,'Expense Name',1);
$pdf->Cell(30,8,'Amount',1);
$pdf->Cell(50,8,'Description',1);
$pdf->Cell(25,8,'Exp. Date',1);
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial','',10);
$i = 1;
while($row = mysqli_fetch_assoc($getExpenses)) {
    $pdf->Cell(10,8,$i++,1);
    $pdf->Cell(50,8,substr($row['expense_name'], 0, 25),1);
    $pdf->Cell(30,8,number_format($row['amount'],2).' RWF',1);
    $pdf->Cell(50,8,substr($row['description'], 0, 28),1);
    $pdf->Cell(25,8,date('Y-m-d', strtotime($row['expense_date'])),1);
    $pdf->Ln();
}

// Totals
$pdf->Ln(6);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(60,10,'Total Expenses:',1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(60,10,number_format($totalAmount, 2).' RWF',1);
$pdf->Ln();

// Output
$pdf->Output('I', 'ExpenseReport_' . $dateFilter . '.pdf');
?>
