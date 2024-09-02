<?php
session_start();
require 'PHP/Connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $first_name = $conn->real_escape_string($_POST['first-name']);
    $last_name = $conn->real_escape_string($_POST['last-name']);
    $_SESSION['lastname'] = $last_name;

    $email = $conn->real_escape_string($_POST['email']);
    $_SESSION['email'] = $email;
    $password = $conn->real_escape_string($_POST['password']);
    $birthday_day = $conn->real_escape_string($_POST['day']);
    $birthday_month = $conn->real_escape_string($_POST['month']);
    $birthday_year = $conn->real_escape_string($_POST['year']);
    $contact_number = $conn->real_escape_string($_POST['contact']);
    $region = $conn->real_escape_string($_POST['region_text']);
    $province = $conn->real_escape_string($_POST['province_text']);
    $city = $conn->real_escape_string($_POST['city_text']);
    $barangay = $conn->real_escape_string($_POST['barangay_text']);
    $street = $conn->real_escape_string($_POST['street']);
    $house_number = $conn->real_escape_string($_POST['house-number']);

    $birthday = "$birthday_year-$birthday_month-$birthday_day";

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Email, Password, Birthday, ContactNumber, Region, Province, City, Barangay, Street, HouseNumber) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $first_name, $last_name, $email, $hashed_password, $birthday, $contact_number, $region, $province, $city, $barangay, $street, $house_number);
    
    try {
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // Insert into UserLogs
            $log_desc = "Account has been created!";
            $log_stmt = $conn->prepare("INSERT INTO UserLogs (UserID, LogDesc) VALUES (?, ?)");
            $log_stmt->bind_param("is", $user_id, $log_desc);
            $log_stmt->execute();
            $log_stmt->close();

            // Insert into carts table
            $cart_stmt = $conn->prepare("INSERT INTO carts (UserID) VALUES (?)");
            $cart_stmt->bind_param("i", $user_id);
            if ($cart_stmt->execute()) {
                $cart_stmt->close();
                header("Location: RegisterSuccess.php");
                exit();
            } else {
                echo "Error: " . $cart_stmt->error;
            }

        } else {
            echo "Error: " . $stmt->error;
        }
    } catch (Exception $e) {
        if ($e->getCode() == 1062) { 
            echo "<script>alert('Email is already in use');</script>";
            echo "<script>window.location.href='Register.html';</script>";
            exit();
        } else {
            echo "Error: " . $e->getMessage();
        }
    }

    $stmt->close();
}

$conn->close();
?>
