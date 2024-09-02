<?php
session_start();
require 'PHP/Connection.php';

$user_id = $_SESSION['UserID'];

if (!isset($_SESSION['UserID'])) {
    header("Location: LoginPage.php");
    exit();
}

// Fetch user address details
$query = "SELECT Region, Province, City, Barangay, Street FROM users WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_address_result = $stmt->get_result()->fetch_assoc();

// Fetch cart details
// Fetch cart details
$query = "SELECT ci.CartID, ci.ProductID, p.ProductName, p.Price, ci.Quantity, (p.Price * ci.Quantity) AS Total
          FROM cartitems ci
          INNER JOIN products p ON ci.ProductID = p.ProductID
          WHERE ci.CartID IN (SELECT CartID FROM carts WHERE UserID = ?)
          AND p.ProductStatus != 'Hidden'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();

$subtotal = 0;
$cart_items = [];
while ($row = $cart_result->fetch_assoc()) {
    $subtotal += $row['Total'];
    $cart_items[] = $row;
}

$shipping_fee = 70;
$total = $subtotal + $shipping_fee;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/PaymentPage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   
    <title>Checkout</title>
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
<form id="orderForm" action="PHP/PlaceOrder.php" method="POST">
    <section class="payment-method">
        <h2>Select Payment Method</h2>
        <div class="payment-method-options">
            <label>
                <input type="radio" name="payment_method" value="COD" required>
                Cash On Delivery
            </label>
            <label>
                <input type="radio" name="payment_method" value="GCASH" required>
                GCASH
            </label>
        </div>
    </section>
    <section class="contact-info">
        <h2>Address</h2>
        <div class="user-address">
            <p><?php echo $user_address_result['Street']; ?>, <?php echo $user_address_result['Barangay']; ?>, <?php echo $user_address_result['City']; ?>, <?php echo $user_address_result['Province']; ?>, <?php echo $user_address_result['Region']; ?></p>
        </div>
    </section>
    <section class="order-summary">
        <h2>Order Summary</h2>
        <div class="summary-item">
            <p>Subtotal (<?php echo count($cart_items); ?> Items)</p>
            <p>₱<?php echo number_format($subtotal, 2); ?></p>
        </div>
        <div class="summary-item">
            <p>Shipping Fee</p>
            <p>₱<?php echo number_format($shipping_fee, 2); ?></p>
        </div>
        <div class="summary-total">
            <p>Total:</p>
            <p>₱<?php echo number_format($total, 2); ?></p>
        </div>
        <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
        <input type="hidden" name="shipping_fee" value="<?php echo $shipping_fee; ?>">
        <input type="hidden" name="total" value="<?php echo $total; ?>">
        <input type="hidden" name="cart_items" value="<?php echo htmlspecialchars(json_encode($cart_items)); ?>">
        <button type="submit" id="placeOrderBtn">PLACE ORDER NOW</button>
    </section>
</form>

</main>

<script>
document.getElementById("placeOrderBtn").addEventListener("click", function(event) {
    event.preventDefault();

    var paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    if (!paymentMethod) {
        alert("Please select a payment method.");
        return; // Prevent further execution
    }

    // Disable the button to prevent multiple clicks
    this.disabled = true;

    // Submit the form asynchronously
    var form = document.getElementById("orderForm");
    var formData = new FormData(form);
    
    var xhr = new XMLHttpRequest();
xhr.open("POST", "PHP/PlaceOrder.php", true);
xhr.responseType = 'blob'; // Set the response type to 'blob' for downloading files
xhr.onload = function() {
    if (xhr.status === 200) {
        // Check if the response contains a PDF file
        var contentType = xhr.getResponseHeader("Content-Type");
        if (contentType === "application/pdf") {
            // Create a blob URL for the PDF
            var blob = new Blob([xhr.response], { type: contentType });
            var url = URL.createObjectURL(blob);
            
            // Create a temporary link element to trigger the download
            var a = document.createElement('a');
            a.href = url;
            a.download = 'order_receipt.pdf'; // Set the filename
            document.body.appendChild(a);
            a.click(); // Simulate a click to trigger the download
            
            // Clean up the temporary link element
            URL.revokeObjectURL(url);
            
            // Display "Order Success" alert after the PDF download completes
            alert("Order Success!");
            
            // Redirect to "Cart.php" after a delay
            setTimeout(function() {
                window.location.href = "Cart.php";
            }, 2000); // 2000 milliseconds = 2 seconds
        } else {
            // If the response is not a PDF, handle the error
            console.error("Error: PDF not received.");
        }
    } else {
        // Re-enable the button if there's an error
        document.getElementById("placeOrderBtn").disabled = false;
        // Handle error if any
        console.error("Error occurred while submitting the form.");
    }
};
xhr.send(formData);


    xhr.onerror = function() {
        // Re-enable the button if there's a network error
        document.getElementById("placeOrderBtn").disabled = false;
        // Handle network errors
        console.error("Network error occurred while submitting the form.");
    };
    xhr.send(formData);
});
</script>

</body>
</html>
