<?php
session_start();
require 'PHP/Connection.php';
if (!isset($_SESSION['UserID'])) {
  header("Location: LoginPage.php");
  exit();
}

// Fetch data from the products table
$query = "SELECT * FROM products WHERE ProductStatus NOT IN ('Deleted', 'Hidden')";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/UserHomePage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Document</title>
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
      <h1>Our Products</h1>
      <div class="grid-container">
        <?php
        // Loop through each row in the result set
        while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <article>
            <a href="ItemPage.php?ProductID=<?php echo $row['ProductID']; ?>">
                <div class="card">
                    <img src="Images/<?php echo $row['ProductImage']; ?>" alt="Product Image">
                    <div class="text-container">
                        <h4><b><?php echo $row['ProductName']; ?></b></h4>
                        <div class="p-container">
                            <p><?php echo $row['Description']; ?></p>
                        </div>
                    </a>
                </div>
            </div>
        </article>
        <?php
        }
        ?>
      </div>
    </main>
</body>
</html>
