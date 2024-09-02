<?php
session_start();
require 'PHP/Connection.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: LoginPage.php");
    exit();
}

if (isset($_GET['ProductID'])) {
    $productID = $_GET['ProductID'];

    $query = "SELECT * FROM products WHERE ProductID = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $productID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
        } else {
            echo "Product not found.";
            exit();
        }
    } else {
        echo "Failed to prepare the statement.";
        exit();
    }
} else {
    echo "ProductID not set.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/ItemPage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title><?php echo htmlspecialchars($product['ProductName']); ?></title>
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
        <div class="product-container">
            <img src="Images/<?php echo htmlspecialchars($product['ProductImage']); ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
            <div class="image-text-container">
                <div>
                    <h1><?php echo htmlspecialchars($product['ProductName']); ?> <span class="price">Php <?php echo htmlspecialchars($product['Price']); ?></span></h1>
                    <div class="description">
                        <p><?php echo nl2br(htmlspecialchars($product['Description'])); ?></p>
                    </div>
                </div>
                <hr>
                <div>
                    <label for="quantity">Quantity: </label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1">
                </div>
                <div class="button-container">
                    <button id="addcart">Add to Cart</button>
                </div>
            </div>
        </div>
    </main>
    <script>
        document.getElementById('addcart').addEventListener('click', function() {
            var quantity = document.getElementById('quantity').value;
            var productID = <?php echo $productID; ?>;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "PHP/AddToCart.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        alert(xhr.responseText);
                    } else {
                        alert("Error adding product to cart.");
                    }
                }
            };
            xhr.send("ProductID=" + productID + "&Quantity=" + quantity);
        });
    </script>
</body>
</html>
