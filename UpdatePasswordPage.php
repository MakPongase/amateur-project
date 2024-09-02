<?php
session_start();
require 'PHP/Connection.php';
$user_id = $_SESSION['UserID'];
if (!isset($_SESSION['UserID'])) {
    header("Location: LoginPage.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/PasswordUpdate.css">
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
   <div class="form-container">
   <form action="PHP/UpdatePassword.php" method="POST" id="passwordForm">
    <h1>Account Reset Password</h1>
    <input type="password" placeholder="Enter Old Password" name="old_password" required id="old_password" class="password-input">
    <input type="password" placeholder="Enter New Password" name="new_password" required id="new_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}" title="Password must be at least 8 characters long and contain one uppercased letter, one lowercase letter, one digit, and one special character." class="password-input">
    <input type="password" placeholder="Re-type new Password" name="confirm_password" required id="confirm_password" class="password-input">
    <div>
        <button type="submit" name="submit" id="submitButton" disabled>Save Password</button>
        <button type="button" onclick="location.href='UserAccount.php'">Cancel</button>
    </div>
</form>
   </div>
</main>
<script>

function checkPasswords() {
    var newPassword = document.getElementById('new_password').value;
    var confirmPassword = document.getElementById('confirm_password').value;
    var oldPassword = document.getElementById('old_password').value;
    var submitButton = document.getElementById('submitButton');

    var passwordsMatch = newPassword === confirmPassword && newPassword !== '';
    var oldPasswordProvided = oldPassword !== '';

    if (passwordsMatch && oldPasswordProvided) {
        submitButton.disabled = false;
    } else {
        submitButton.disabled = true;
    }
}

function togglePasswordClass() {
    var newPassword = document.getElementById('new_password').value;
    var confirmPassword = document.getElementById('confirm_password').value;
    var confirmPasswordInput = document.getElementById('confirm_password');

    var passwordsMatch = newPassword === confirmPassword && newPassword !== '';

    if (passwordsMatch) {
        confirmPasswordInput.classList.remove('wrong');
        confirmPasswordInput.classList.add('correct');
    } else {
        confirmPasswordInput.classList.remove('correct');
        confirmPasswordInput.classList.add('wrong');
    }
}

document.getElementById('new_password').addEventListener('input', function() {
    checkPasswords();
    togglePasswordClass();
});

document.getElementById('confirm_password').addEventListener('input', function() {
    checkPasswords();
    togglePasswordClass();
});
<?php
    if (isset($_SESSION['password_change_success'])) {
        echo "alert('Password changed successfully.');";
        unset($_SESSION['password_change_success']);
    }
    if (isset($_SESSION['password_change_error'])) {
        echo "alert('Failed to change password. Please try again.');";
        unset($_SESSION['password_change_error']);
    }
    ?>
</script>
</body>
</html>
