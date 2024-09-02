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
    <link rel="stylesheet" type="text/css" href="CSS/Login.css">
    <title>Login Page</title>
</head>
<body>
    <a href="Index.html"><img src="Images/PiscesFarmLogo.png" alt=""></a>

    <form action="PHP/Login.php" method="POST">
        <div class="container">
            <h1>Login</h1>
            <p>Please fill in this form to login.</p>
            <label for="email"><b>Email</b></label>
            <input type="email" placeholder="Enter Email" name="email" required autofocus>
            <label for="password"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="password" required>
            <div class="checkbox-span-container">
                <label>
                    <input type="checkbox" checked="checked" name="remember"> Remember me
                </label>
                <span class="psw"><a href="ForgotPassword.php">Forgot password?</a></span>
            </div>
            <button type="submit" class="loginbtn">Login</button>
        </div>
        <div class="form-footer">
            <p>Dont have an account? <a href="Register.html">Register Instead!</a></p>
        </div>
    </form>
    <script src="JavaScript/RememberMe.js"></script>
</body>
</html>
