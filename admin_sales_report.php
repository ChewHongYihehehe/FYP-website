<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Initialize filter variable
$filter = '';

// Set default date range
$start_date = date('Y-m-01');
$end_date = date('Y-m-t');

// Handle filter selection
if (isset($_POST['filter'])) {
    $filter = $_POST['filter'];

    switch ($filter) {
        case 'today':
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
            break;
        case 'yesterday':
            $start_date = date('Y-m-d', strtotime('-1 day'));
            $end_date = date('Y-m-d', strtotime('-1 day'));
            break;
        case 'last_7_days':
            $start_date = date('Y-m-d', strtotime('-6 days'));
            $end_date = date('Y-m-d');
            break;
        case 'last_30_days':
            $start_date = date('Y-m-d', strtotime('-29 days'));
            $end_date = date('Y-m-d');
            break;
        case 'this_month':
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
            break;
        case 'custom':
            if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
            }
            break;
    }
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

if (isset($_POST['export'])) {
    $export_type = $_POST['export_type'];

    if ($export_type == 'excel') {
        // Redirect to the Excel export script
        header('Location: export_sales.php?filter=' . $filter . '&start_date=' . $start_date . '&end_date=' . $end_date);
        exit();
    } elseif ($export_type == 'pdf') {
        // Redirect to the PDF generation script
        header('Location: generatePDF.php?filter=' . $filter . '&start_date=' . $start_date . '&end_date=' . $end_date);
        exit();
    } elseif ($export_type = 'csv') {
        exportToCSV($sales_data);
        exit();
    }
}
// Define the exportToCSV function
function exportToCSV($sales_data)
{
    // Set headers for the CSV file
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sales_report.csv"');

    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Output the column headings
    fputcsv($output, ['Date', 'No. Orders', 'Products Sold', 'Total Sales (RM)']);

    // Output each row of the data
    foreach ($sales_data as $data) {
        fputcsv($output, [
            $data['order_date'],
            $data['total_orders'],
            $data['total_products_sold'],
            number_format($data['total_sales'], 2)
        ]);
    }

    // Close the file pointer
    fclose($output);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="assets/css/admin_sales_report.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .filter-export-container {
            display: flex;
            /* Use flexbox to align items in a row */
            justify-content: space-between;
            /* Space between filter and export section */
            align-items: center;
            /* Center items vertically */
            margin-top: 10px;
            /* Add some space above the filter section */
        }

        .export-container {
            display: flex;
            /* Use flexbox for the export dropdown and button */
            align-items: center;
            /* Center items vertically */
        }

        .export-container select,
        .export-container button {
            margin-left: 10px;
            /* Add space between the dropdown and button */
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="container">
        <h1>Sales Report</h1>


        <form method="POST" action="" class="filter-container">
            <div class="filter-export-container">
                <div class="filter-section">
                    <select name="filter" onchange="this.form.submit()">
                        <option value="">Select Filter</option>
                        <option value="today" <?= ($start_date == date('Y-m-d') && $end_date == date('Y-m-d')) ? 'selected' : ''; ?>>Today</option>
                        <option value="yesterday" <?= ($start_date == date('Y-m-d', strtotime('-1 day')) && $end_date == date('Y-m-d', strtotime('-1 day'))) ? 'selected' : ''; ?>>Yesterday</option>
                        <option value="last_7_days" <?= ($start_date == date('Y-m-d', strtotime('-6 days')) && $end_date == date('Y-m-d')) ? 'selected' : ''; ?>>Last 7 Days</option>
                        <option value="last_30_days" <?= ($start_date == date('Y-m-d', strtotime('-29 days')) && $end_date == date('Y-m-d')) ? 'selected' : ''; ?>>Last 30 Days</option>
                        <option value="this_month" <?= ($start_date == date('Y-m-01') && $end_date == date('Y-m-t')) ? 'selected' : ''; ?>>This Month</option>
                        <option value="custom" <?= ($filter == 'custom') ? 'selected' : ''; ?>>Custom Range</option>
                    </select>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($start_date); ?>" <?= ($filter != 'custom') ? 'disabled' : ''; ?>>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($end_date); ?>" <?= ($filter != 'custom') ? 'disabled' : ''; ?>>
                    <button type="submit">Filter</button>
                </div>

                <!-- Export selection and button aligned to the right -->
                <div class="export-container">
                    <select name="export_type">
                        <option value="excel">Export to Excel</option>
                        <option value="pdf">Generate PDF</option>
                        <option value="csv">Export to CSV</option>
                    </select>
                    <button type="submit" name="export">Export</button>
                </div>
            </div>
        </form>

        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>No. Orders</th>
                        <th>Products Sold</th>
                        <th>Total Sales (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row_count = 1;
                    if (empty($sales_data)): ?>
                        <tr>
                            <td colspan="4">No sales data found for the selected date range.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sales_data as $data): ?>
                            <tr>
                                <td><?= $row_count++; ?></td>
                                <td><?= htmlspecialchars($data['order_date']); ?></td>
                                <td><?= htmlspecialchars($data['total_orders']); ?></td>
                                <td><?= htmlspecialchars($data['total_products_sold']); ?></td>
                                <td>RM <?= htmlspecialchars(number_format($data['total_sales'], 2)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#export-button').click(function() {
                // Get the filter values
                var filter = $('select[name="filter"]').val();
                var start_date = $('input[name="start_date"]').val();
                var end_date = $('input[name="end_date"]').val();

                // Create a form to send the data
                var form = $('<form>', {
                    method: 'POST',
                    action: 'export_sales.php',
                    target: '_blank' // Open in a new tab
                });

                // Append the filter and date values to the form
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'filter',
                    value: filter
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'start_date',
                    value: start_date
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'end_date',
                    value: end_date
                }));

                // Append the form to the body and submit it
                $('body').append(form);
                form.submit();
                form.remove(); // Clean up the form
            });
        });
    </script>
</body>

</html>