<?php
session_start();
require 'PHP/Connection.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: LoginPage.php");
    exit();
}

// Handle cart item removal and log the action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['removeCartItemCartID']) && isset($_POST['removeCartItemProductID'])) {
    $cartID = $_POST['removeCartItemCartID'];
    $productID = $_POST['removeCartItemProductID'];

    // Delete the cart item from the database
    $query = "DELETE FROM cartitems WHERE CartID = ? AND ProductID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $cartID, $productID);
    $stmt->execute();

    // Log the action
    $logDesc = "Removed item from cart. ProductID: $productID, CartID: $cartID";
    logUserAction($conn, $_SESSION['UserID'], $logDesc);
}

// Fetch user's address components from the database
$query = "SELECT Region, Province, City, Barangay, Street, ContactNumber FROM users WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['UserID']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Combine address components into a single string
$userAddress = $user['Street'] . ', ' . $user['Barangay'] . ', ' . $user['City'] . ', ' . $user['Province'] . ', ' . $user['Region'];
$userContactNumber = $user['ContactNumber'];

// Handle quantity increment and decrement and log the action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['changeQuantityCartID']) && isset($_POST['changeQuantityProductID']) && isset($_POST['changeQuantityAction'])) {
    $cartID = $_POST['changeQuantityCartID'];
    $productID = $_POST['changeQuantityProductID'];
    $action = $_POST['changeQuantityAction'];

    // Increment or decrement the quantity based on action
    if ($action === 'increment') {
        $query = "UPDATE cartitems SET Quantity = Quantity + 1 WHERE CartID = ? AND ProductID = ?";
        $quantityChange = 1;
    } elseif ($action === 'decrement') {
        // Check if the quantity is already 1, if not, decrement
        $query = "UPDATE cartitems SET Quantity = CASE WHEN Quantity > 1 THEN Quantity - 1 ELSE 1 END WHERE CartID = ? AND ProductID = ?";
        $quantityChange = -1;
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $cartID, $productID);
    $stmt->execute();

    // Log the action
    $logDesc = "Changed quantity of item in cart. ProductID: $productID, CartID: $cartID, Quantity change: $quantityChange";
    logUserAction($conn, $_SESSION['UserID'], $logDesc);
}

// Function to log user actions
function logUserAction($conn, $userID, $logDesc) {
    $query = "INSERT INTO userlogs (UserID, LogDesc) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $userID, $logDesc);
    $stmt->execute();
    $stmt->close();
}

// Fetch cart items for the user
$userID = $_SESSION['UserID'];

// Fetch available cart items
$queryAvailable = "SELECT ci.CartID, ci.ProductID, p.ProductName, p.Price, ci.Quantity, (p.Price * ci.Quantity) AS Total
                   FROM cartitems ci
                   INNER JOIN products p ON ci.ProductID = p.ProductID
                   WHERE ci.CartID IN (SELECT CartID FROM carts WHERE UserID = ?) AND p.ProductStatus != 'Hidden'";
$stmt = $conn->prepare($queryAvailable);
$stmt->bind_param("i", $userID);
$stmt->execute();
$resultAvailable = $stmt->get_result();
$cartEmpty = $resultAvailable->num_rows === 0;

// Fetch unavailable cart items
$queryUnavailable = "SELECT ci.CartID, ci.ProductID, p.ProductName, p.Price, ci.Quantity, (p.Price * ci.Quantity) AS Total
                     FROM cartitems ci
                     INNER JOIN products p ON ci.ProductID = p.ProductID
                     WHERE ci.CartID IN (SELECT CartID FROM carts WHERE UserID = ?) AND p.ProductStatus = 'Hidden'";
$stmt = $conn->prepare($queryUnavailable);
$stmt->bind_param("i", $userID);
$stmt->execute();
$resultUnavailable = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/Cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>User Account</title>
    <script>
        function confirmRemove(event) {
            if (!confirm("Are you sure you want to remove this item from your cart?")) {
                event.preventDefault();
            }
        }
    </script>
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
<h1>Your Cart</h1>
<section>
    <div class="large-container">
    <div class="cart-container">
    <table class="cart-table">
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th></th>
        </tr>
        <?php
        $subtotal = 0;
        while ($row = $resultAvailable->fetch_assoc()) {
            $subtotal += $row['Total'];
        ?>
        <tr>
            <td><?php echo $row['ProductName']; ?></td>
            <td><?php echo $row['Price']; ?> Pesos</td>
            <td>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <input type="hidden" name="changeQuantityCartID" value="<?php echo $row['CartID']; ?>">
                    <input type="hidden" name="changeQuantityProductID" value="<?php echo $row['ProductID']; ?>">
                    <button type="submit" name="changeQuantityAction" value="decrement" class="td-buttons">-</button>
                    <?php echo $row['Quantity']; ?>
                    <button type="submit" name="changeQuantityAction" value="increment" class="td-buttons">+</button>
                </form>
            </td>
            <td><?php echo $row['Total']; ?> Pesos</td>
            <td>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="confirmRemove(event)">
                    <input type="hidden" name="removeCartItemCartID" value="<?php echo $row['CartID']; ?>">
                    <input type="hidden" name="removeCartItemProductID" value="<?php echo $row['ProductID']; ?>">
                    <button type="submit" class="remove-button">Remove</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
    
</div>
    <div class="unavailable-items">
        <table class="unavailable-table">
            <tr>
                <th>Unavailable Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th></th>
            </tr>
            <?php
            while ($row = $resultUnavailable->fetch_assoc()) {
            ?>
            <tr>
                <td><?php echo $row['ProductName']; ?></td>
                <td><?php echo $row['Price']; ?> Pesos</td>
                <td><?php echo $row['Quantity']; ?></td>
                <td><?php echo $row['Total']; ?> Pesos</td>
                <td>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="confirmRemove(event)">
                        <input type="hidden" name="removeCartItemCartID" value="<?php echo $row['CartID']; ?>">
                        <input type="hidden" name="removeCartItemProductID" value="<?php echo $row['ProductID']; ?>">
                        <button type="submit" class="remove-button">Remove</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
    </div>

<div class="checkout-container">
    <table class="checkout-table">
        <h2>Order Summary</h2>
        <tr class="total-row">
            <td class="align-left"><b>Subtotal:</b></td>
            <td><b><?php echo $subtotal; ?> Pesos</b></td>
        </tr>
        <tr>
            <td class="align-left"><b>Shipping Fee:</b></td>
            <td>70 Pesos</td>
        </tr>
    </table>
    <hr>
    <table class="total-table">
        <tr>
            <td class="align-left"><b>Total:</b></td>
            <td class="align-right"><?php echo ($subtotal + 70); ?> Pesos</td>
        </tr>
    </table>
    <button class="checkout-button" <?php echo ($cartEmpty ? 'disabled' : ''); ?> onclick="window.location.href='PaymentPage.php'">Checkout</button>
    <div class="location-container">
        <h2>Location</h2>
        <p><?php echo $userAddress; ?></p>
        <hr>
        <p>Contact Number: <?php echo $userContactNumber; ?></p>
    </div>
</div>
</section>
</main>
</body>
</html>
