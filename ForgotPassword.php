<?php
session_start();

if (isset($_SESSION['UserID'])) {
    header("Location: UserHomePage.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="CSS/ForgotPassword.css">
    <title>Login Page</title>
</head>
<body>
    <a href="Index.html"><img src="Images/PiscesFarmLogo.png" alt=""></a>

    <form action="PHP/Login.php" method="POST">
        <div class="container">
            <h1>Find your Account</h1>
            <p>Please fill in this form to find your account.</p>
            <label for="email"><b>Email</b></label>
            <input type="email" placeholder="Enter Email" name="email" required autofocus>
        <div class="button-container">
            <button type="button" onclick="window.location.href='LoginPage.php'" id="cancel-button">Cancel</button>
            <button type="submit"  id="submit-button" id="search-button">Search</button>
        </div>
            
        
        </div>
        
    </form>
    <script src="JavaScript/RememberMe.js"></script>
</body>
</html>
