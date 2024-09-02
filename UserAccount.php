<?php
session_start();
require 'PHP/Connection.php';
$user_id = $_SESSION['UserID'];
if (!isset($_SESSION['UserID'])) {
    header("Location: LoginPage.php");
    exit();
}
$query = $conn->prepare("SELECT * FROM users WHERE UserID = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

$first_name = htmlspecialchars($user['FirstName']);
$last_name = htmlspecialchars($user['LastName']);
$email = htmlspecialchars($user['Email']);
$birthday = htmlspecialchars(date("F d, Y", strtotime($user['Birthday'])));
$contact_number = htmlspecialchars($user['ContactNumber']);

$region = htmlspecialchars($user['Region']);
$province = htmlspecialchars($user['Province']);
$city = htmlspecialchars($user['City']);
$barangay = htmlspecialchars($user['Barangay']);
$street = htmlspecialchars($user['Street']);
$house_number = htmlspecialchars($user['HouseNumber']);
$address = trim($region . ', ' . $province . ', ' . $city . ', ' . $barangay . ', ' . $street . ', ' . $house_number);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/UserAccount.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
    <div class="user-information">
        <form action="PHP/UpdateProfile.php" method="POST">
            <h1>Personal Information</h1>
            <div class="name-container margin-bottom-2">
                <input type="text" name="first_name" value="<?php echo $first_name; ?>" placeholder="John Mark" disabled>
                <input type="text" name="last_name" value="<?php echo $last_name; ?>" placeholder="Pongase" disabled>
            </div>
            <div class="email-container margin-bottom-2">
                <input type="text" name="email" value="<?php echo $email; ?>" placeholder="nyancomak@gmail.com" disabled>
            </div>
            <div class="contact-container margin-bottom-2">
                <input type="text" name="contact_number" value="<?php echo $contact_number; ?>" placeholder="nyancomak@gmail.com" disabled>
            </div>
            <div class="birthday-container margin-bottom-2">
                <input type="text" value="<?php echo $birthday; ?>" placeholder="December 20, 2003" disabled>
            </div>
            <div class="button-container">
                <div class="">
                    <button type="button" id="edit-info-btn">Edit</button>
                    <button type="submit" id="save-info-btn"name="save-info" disabled>Save</button>
                </div>
                <div>
                <button type="button" id="password-update">Update Password</button>
                </div>
            </div>
            <hr>
            <div class="address-container">
                <h2>Address</h2>
                <input type="text" value="<?php echo $address; ?>" placeholder="Region Province City Barangay Street HouseNumber" disabled>
                <button type="button" id="edit-address-btn">Edit Address</button>
                <div id="address-form" style="display: none;">
                    <div id="address-container">
                        <label for="region"><b>Personal Address</b></label>
                        <select id="region">
                            <option value="" disabled selected>Select Region</option>
                        </select>
                        <input type="hidden" name="region_text" id="region-text">
                    </div>
                    <div id="province-container">
                        <select id="province">
                            <option value="" disabled selected>Select Province</option>
                        </select>
                        <input type="hidden" name="province_text" id="province-text">
                    </div>
                    <div id="city-container">
                        <select id="city">
                            <option value="" disabled selected>Select City</option>
                        </select>
                        <input type="hidden" name="city_text" id="city-text">
                    </div>
                    <div id="barangay-container">
                        <select id="barangay">
                            <option value="" disabled selected>Select Barangay</option>
                        </select>
                        <input type="hidden" name="barangay_text" id="barangay-text">
                    </div>
                    <div id="street-container">
                        <input type="text" placeholder="Street" name="street" id="street">
                    </div>
                    <div id="house-number-container">
                        <input type="text" placeholder="House Number" name="house-number" id="house-number">
                    </div>
                    <div class="form-buttons">
                        <button type="button" id="cancel-address-btn">Cancel</button>
                        <button type="submit" id="save-address-btn" name="save-address">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
<script src="JavaScript/address-selector.js"></script>

<script>
    document.getElementById('edit-info-btn').addEventListener('click', function() {
        var editButton = document.getElementById('edit-info-btn');
        var saveButton = document.getElementById('save-info-btn');
        var inputs = document.querySelectorAll('.user-information input:not([placeholder="Region Province City Barangay Street HouseNumber"], [name="street"], [name="house-number"], [placeholder="December 20, 2003"])');

        if (editButton.textContent === 'Edit') {
            inputs.forEach(function(input) {
                input.disabled = false;
            });
            saveButton.disabled = false;
            editButton.textContent = 'Cancel';
        } else {
            inputs.forEach(function(input) {
                input.disabled = true;
            });
            saveButton.disabled = true;
            editButton.textContent = 'Edit';
        }
    });

    document.getElementById('edit-address-btn').addEventListener('click', function() {
        var addressForm = document.getElementById('address-form');
        var editAddressBtn = document.getElementById('edit-address-btn');
        addressForm.style.display = 'block';
        editAddressBtn.style.display = 'none';
    });

    document.getElementById('cancel-address-btn').addEventListener('click', function() {
        var addressForm = document.getElementById('address-form');
        var editAddressBtn = document.getElementById('edit-address-btn');
        addressForm.style.display = 'none';
        editAddressBtn.style.display = 'block';
    });

    document.getElementById('save-address-btn').addEventListener('click', function() {
        // Add your save functionality here
        var addressForm = document.getElementById('address-form');
        var editAddressBtn = document.getElementById('edit-address-btn');
        addressForm.style.display = 'none';
        editAddressBtn.style.display = 'block';
    });
    document.getElementById('password-update').addEventListener('click', function() {
    window.location.href = 'UpdatePasswordPage.php';
});
</script>
</body>
</html>
