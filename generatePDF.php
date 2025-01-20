<?php
require('./fpdf186/fpdf.php');
include 'connect.php';

// Initialize filter variable
$filter = '';

// Set default date range
$start_date = date('Y-m-01');
$end_date = date('Y-m-t');

// Check if filter parameters are passed via GET
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

// Fetch sales data
$sales_data = [];
$stmt = $conn->prepare("SELECT 
                            DATE(o.placed_on) AS order_date,
                            COUNT(o.id) AS total_orders,
                            SUM(oi.quantity) AS total_products_sold,
                            SUM(o.total_price) AS total_sales
                        FROM 
                            orders o
                        JOIN 
                            order_items oi ON o.id = oi.order_id
                        WHERE 
                            o.placed_on BETWEEN :start_date AND :end_date
                        GROUP BY 
                            DATE(o.placed_on)
                        ORDER BY 
                            DATE(o.placed_on) ASC");
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Add logo
$pdf->Image('assets/image/logo.png', 10, 10, 30);

// Set title font and position
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetXY(10, 30);


$pdf->SetDrawColor(0, 0, 0);
$pdf->Rect(10, 30, 190, 20);

// Title inside the rectangle
$pdf->Cell(0, 10, 'Sales Report', 0, 1, 'C');
$pdf->Ln(10);

// Table header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Date', 1);
$pdf->Cell(40, 10, 'No. Orders', 1);
$pdf->Cell(60, 10, 'Products Sold', 1);
$pdf->Cell(50, 10, 'Total Sales (RM)', 1);
$pdf->Ln();

// Table data
$pdf->SetFont('Arial', '', 12);
foreach ($sales_data as $data) {
    $pdf->Cell(40, 10, htmlspecialchars($data['order_date']), 1);
    $pdf->Cell(40, 10, htmlspecialchars($data['total_orders']), 1);
    $pdf->Cell(60, 10, htmlspecialchars($data['total_products_sold']), 1);
    $pdf->Cell(50, 10, htmlspecialchars(number_format($data['total_sales'], 2)), 1);
    $pdf->Ln();
}

$pdf->Output();
exit();
