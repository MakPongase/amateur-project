<?php
session_start();
require 'PHP/Connection.php';
// $userID = $_SESSION['UserID'];
$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/AddItemsPage.css">
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
        <h1>Manage Items</h1>
        <nav class="main-nav">
        <button id="additem" onclick="window.location.href='ManageItems.php'">Return</button>
        </nav>
        <form action="PHP/AddItem.php" method="POST"  enctype="multipart/form-data">
        <div class="form-group">
                <label for="product-image">Product Image:</label>
                <input type="file" id="product-image" name="image" accept="image/*" required>
                <div id="image-preview"></div>
            </div>
            <div class="form-group">
                <label for="product-name">Product Name:</label>
                <input type="text" id="product-name" name="product-name" required>
            </div>
            <div class="form-group">
                <label for="product-description">Product Description:</label>
                <textarea id="product-description" name="product-description" required></textarea>
            </div>
            <div class="form-group">
                <label for="product-price">Product Price:</label>
                <input type="number" id="product-price" name="product-price" step="0.01" required>
            </div>
        
            <div class="form-group">
                <button type="submit">Submit</button>
            </div>
        </form>

    </main>
    <script>
    document.getElementById('product-image').addEventListener('change', function(event) {
        var file = event.target.files[0];
        var reader = new FileReader();
        
        reader.onload = function(e) {
            var img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '200px';
            document.getElementById('image-preview').innerHTML = '';
            document.getElementById('image-preview').appendChild(img);
        }
        
        reader.readAsDataURL(file);
    });
    <?php
    if (isset($_SESSION['success_add'])) {
        echo "alert('Item added successfuly!');";
        unset($_SESSION['success_add']);
    }
    if (isset($_SESSION['failed_add'])) {
        echo "alert('Failed to add  item..');";
        unset($_SESSION['failed_add']);
    }
    ?>
</script>

</body>
</html>
