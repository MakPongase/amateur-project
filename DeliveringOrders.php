<?php
session_start();
require 'PHP/Connection.php';

// Get the order status from the query parameter
$order_status = isset($_GET['status']) ? $_GET['status'] : 'Delivering';

// Fetch orders based on the selected status
$query = "SELECT o.OrderID, o.TimeOfPay, o.OrderStatus, o.OrderSummary, o.TotalPaid, u.LastName, o.OrderAddress
          FROM orders AS o 
          JOIN users AS u ON o.UserID = u.UserID 
          WHERE o.OrderStatus = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $order_status);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/DeliveringOrders.css">
    <title>Admin Page</title>
</head>
<body>
    <nav class="header-nav">
        <ul>
            <li><a href="AdminPage.php">Reports</a></li>
            <li><a href="AdminLog.php">Logs</a></li>
            <li><a href="AdminOrders.php">Orders</a></li>
            <li><a href="ManageItems.php">Manage Items</a></li>
        </ul>
        <button onclick="location.href='PHP/Logout.php'">Logout</button>
    </nav>
    <main>
        <h1>Manage Orders</h1>
        <nav class="main-nav">
            <ul>
                <li><a href="AdminOrders.php" id="pending">Pending Orders</a></li>
                <li><a href="DeliveringOrders.php" id="delivered">Delivering Orders</a></li>
                <li><a href="SuccessOrders.php" id="success">Success Orders</a></li>
                <li><a href="CancelledOrders.php" id="cancelled">Cancelled Orders</a></li>
            </ul>
        </nav>
        <div class="main-container">
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-container">
                        <h3><?php echo $order['LastName']; ?>'s Order (Order #<?php echo $order['OrderID']; ?>)</h3>
                        <table>
                            <tr>
                                <th>Order Date</th>
                                <th>Order Status</th>
                                <th>Order Summary</th>
                                <th>Total Price</th>
                            </tr>
                            <tr>
                                <td><?php echo $order['TimeOfPay']; ?></td>
                                <td><?php echo $order['OrderStatus']; ?></td>
                                <td><?php echo $order['OrderSummary']; ?></td>
                                <td>₱<?php echo number_format($order['TotalPaid'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td colspan="3"><?php echo $order['OrderAddress']; ?></td>
                            </tr>              
                        </table>
                        <form method="POST" action="PHP/UpdateOrderStatus2.php">
                            <input type="hidden" name="order_id" value="<?php echo $order['OrderID']; ?>">
                            <button type="submit" name="action" value="Success" class="order-success">Order Success</button>
                            <button type="submit" name="action" value="Cancelled" class="order-cancelled">Order Cancelled</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No orders found for the selected status.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
