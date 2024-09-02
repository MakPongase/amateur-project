<?php
session_start();
require 'PHP/Connection.php';
$query = "SELECT * FROM products WHERE ProductStatus != 'Deleted'";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/ManageItems.css">
    <title>Admin Page</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateProductStatus(productId, status) {
            $.ajax({
                url: 'PHP/UpdateProductStatus.php',
                type: 'POST',
                data: { ProductID: productId, ProductStatus: status },
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Failed to update product status');
                }
            });
        }

        function confirmDelete(productId) {
            if (confirm("Are you sure you want to delete this product?")) {
                updateProductStatus(productId, 'Deleted');
            }
        }

        function promptUpdatePrice(productId) {
            var newPrice = prompt("Enter the new price:");
            if (newPrice !== null && !isNaN(newPrice) && newPrice >= 0) {
                updatePrice(productId, newPrice);
            } else {
                alert("Please enter a valid price.");
            }
        }

        function updatePrice(productId, price) {
            $.ajax({
                url: 'PHP/UpdatePrice.php',
                type: 'POST',
                data: { ProductID: productId, Price: price },
                success: function(response) {
                    alert('Price updated successfully');
                    location.reload();
                },
                error: function() {
                    alert('Failed to update price');
                }
            });
        }
    </script>
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
    <h1>Manage Items</h1>
    <nav class="main-nav">
        <button id="additem" onclick="window.location.href='AddItemPage.php'">Add Item</button>
    </nav>
    <table class="minimalist-table">
        <tr>
            <th>ProductID</th>
            <th>ProductImage</th>
            <th>ProductName</th>
            <th>Description</th>
            <th>Price</th>
            <th>ProductStatus</th>
            <th>Action</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['ProductID']; ?></td>
                <td><img src="Images/<?php echo $row['ProductImage']; ?>" alt="Product Image"></td>
                <td><?php echo $row['ProductName']; ?></td>
                <td><?php echo $row['Description']; ?></td>
                <td>
                    <?php echo $row['Price']; ?>
                    <button class="blue-button" onclick="promptUpdatePrice(<?php echo $row['ProductID']; ?>)">Edit Price</button>                </td>
                <td><?php echo $row['ProductStatus']; ?></td>
                <td>
                    <button class="activate-button" onclick="updateProductStatus(<?php echo $row['ProductID']; ?>, 'Active')" 
                            <?php echo $row['ProductStatus'] == 'Active' ? 'style="display:none;"' : ''; ?>>
                        Activate
                    </button>
                    <button class="deactivate-button" onclick="updateProductStatus(<?php echo $row['ProductID']; ?>, 'Hidden')" 
                            <?php echo $row['ProductStatus'] == 'Hidden' ? 'style="display:none;"' : ''; ?>>
                        Deactivate
                    </button>
                </td>
            </tr>
        <?php } ?>
    </table>
    </main>
</body>
</html>
