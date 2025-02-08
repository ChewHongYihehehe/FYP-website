<?php
include 'connect.php';

$filter = $_GET['filter'];
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];

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
// Set headers for Excel file download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=sales_report_" . date('Ymd') . ".xls");

// Output the column headings
echo "Date\tNo. Orders\tProducts Sold\tTotal Sales (RM)\n";

// Output the data
foreach ($sales_data as $data) {
    echo htmlspecialchars($data['order_date']) . "\t" .
        htmlspecialchars($data['total_orders']) . "\t" .
        htmlspecialchars($data['total_products_sold']) . "\t" .
        htmlspecialchars(number_format($data['total_sales'], 2)) . "\n";
}
exit();
