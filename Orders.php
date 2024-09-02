<?php
session_start();
require 'PHP/Connection.php';
$user_id = $_SESSION['UserID'];
if (!isset($_SESSION['UserID'])) {
    header("Location: LoginPage.php");
    exit();
}

// Get the order status from the query parameter
$order_status = isset($_GET['status']) ? $_GET['status'] : 'Pending';

// Fetch user orders based on the selected status
$query = "SELECT OrderID, TimeOfPay, OrderStatus, TotalPaid, OrderSummary 
          FROM orders 
          WHERE UserID = ? AND OrderStatus = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $user_id, $order_status);
$stmt->execute();
$order_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/Orders.css">
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>User Account</title>
</head>
<body>
<header>
    <nav class="header-nav">
        <img src="Images/PiscesFarmLogo.png" alt="">
        <div class="links-container">
            <div class="nav-button-container">
                <a href="UserHomePage.php" id="Store">Store</a>
                <a href="Orders.php" id="Orders">Orders</a>
                <a href="Cart.php" id="Cart">Cart</a>
                <a href="UserAccount.php" id="Account">Account</a>
                <a onclick="location.href='PHP/Logout.php'">Log out</a>
            </div>
        </div>
    </nav>
</header>
<main>
    <h1>Your Orders</h1>
    <div class="status-buttons">
        <button type="button" data-status="Pending" class="<?= $order_status == 'Pending' ? 'active' : '' ?>">Pending</button>
        <button type="button" data-status="Delivering" class="<?= $order_status == 'Delivering' ? 'active' : '' ?>">Delivering</button>
        <button type="button" data-status="Success" class="<?= $order_status == 'Success' ? 'active' : '' ?>">Success</button>
        <button type="button" data-status="Cancelled" class="<?= $order_status == 'Cancelled' ? 'active' : '' ?>">Cancelled</button>
    </div>
    <table>
        <tr>
            <th>Order Date</th>
            <th>Order Status</th>
            <th>Order Total</th>
            <th>Order Summary</th>
        </tr>
        <?php while ($order = $order_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $order['TimeOfPay']; ?></td>
            <td><?php echo $order['OrderStatus']; ?></td>
            <td>₱<?php echo number_format($order['TotalPaid'], 2); ?></td>
            <td><?php echo htmlspecialchars($order['OrderSummary']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('button[data-status]');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            window.location.href = `Orders.php?status=${status}`;
        });
    });
});
</script>
</body>
</html>