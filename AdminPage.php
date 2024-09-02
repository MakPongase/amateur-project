<?php
session_start();
require 'PHP/Connection.php';

// Fetch total number of users
$queryUsers = "SELECT COUNT(UserID) AS TotalUsers FROM users";
$resultUsers = $conn->query($queryUsers);
$totalUsers = $resultUsers->fetch_assoc()['TotalUsers'];

// Initialize arrays to hold product data
$productSales = [];

// Fetch all orders to parse OrderSummary for top-selling products
$queryOrders = "SELECT OrderSummary, TotalPaid, TimeOfPay FROM orders WHERE OrderStatus = 'Success'";
$resultOrders = $conn->query($queryOrders);

while ($order = $resultOrders->fetch_assoc()) {
    $orderSummary = $order['OrderSummary'];
    // Remove any extra whitespace and split the order summary by commas
    $products = array_map('trim', explode(',', $orderSummary));

    foreach ($products as $productDetail) {
        // Extract product name and quantity using regular expression
        if (preg_match('/^(.*?)\s*\((\d+)x\s*-\s*₱([\d,]+(?:\.\d+)?)\)$/', $productDetail, $matches)) {
            $productName = trim($matches[1]);
            $quantity = (int) $matches[2];
            
            if (!isset($productSales[$productName])) {
                $productSales[$productName] = 0;
            }
            // Increment the quantity sold for the product
            $productSales[$productName] += $quantity;
        }
    }
}

// Sort the products by sales quantity in descending order
arsort($productSales);
// Get the top 5 selling products
$topProducts = array_slice($productSales, 0, 5, true);

// Fetch monthly sales
$queryMonthlySales = "
SELECT DATE_FORMAT(TimeOfPay, '%Y-%m') AS OrderMonth, SUM(TotalPaid) AS TotalSales
FROM orders
WHERE OrderStatus = 'Success'
GROUP BY OrderMonth
ORDER BY OrderMonth";
$resultMonthlySales = $conn->query($queryMonthlySales);
$monthlySales = [];
while ($row = $resultMonthlySales->fetch_assoc()) {
    $monthlySales[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/AdminPage.css">
    <title>Admin Page</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav>
        <ul>
            <li><a href="AdminPage.php">Reports</a></li>
            <li><a href="AdminLog.php">Logs</a></li>
            <li><a href="AdminOrders.php">Orders</a></li>
            <li><a href="ManageItems.php">Manage Items</a></li>
        </ul>
        <button onclick="location.href='PHP/Logout.php'">Logout</button>
    </nav>
    <main>
    <h1>Welcome Admin, Here are the reports.</h1>
    <div class="main-container">
        <div class="chart-container">
            <canvas id="totalUsersChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="topProductsChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="monthlySalesChart"></canvas>
        </div>
    </div>
</main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Total Users Chart
            var totalUsersCtx = document.getElementById('totalUsersChart').getContext('2d');
            var totalUsersChart = new Chart(totalUsersCtx, {
                type: 'bar',
                data: {
                    labels: ['Total Users'],
                    datasets: [{
                        label: 'Users',
                        data: [<?php echo $totalUsers; ?>],
                        backgroundColor: ['rgba(54, 162, 235, 0.2)'],
                        borderColor: ['rgba(54, 162, 235, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Top Products Chart
            var topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
            var topProductsData = {
                labels: [<?php foreach ($topProducts as $product => $quantity) { echo '"' . $product . '", '; } ?>],
                datasets: [{
                    label: 'Top Selling Products',
                    data: [<?php foreach ($topProducts as $product => $quantity) { echo $quantity . ', '; } ?>],
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }]
            };
            var topProductsChart = new Chart(topProductsCtx, {
                type: 'bar',
                data: topProductsData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Monthly Sales Chart
            var monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
            var monthlySalesData = {
                labels: [<?php foreach ($monthlySales as $sale) { echo '"' . $sale['OrderMonth'] . '", '; } ?>],
                datasets: [{
                    label: 'Monthly Sales',
                    data: [<?php foreach ($monthlySales as $sale) { echo $sale['TotalSales'] . ', '; } ?>],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };
            var monthlySalesChart = new Chart(monthlySalesCtx, {
                type: 'bar',
                data: monthlySalesData,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>

