<?php
    require('PHP/Connection.php');
    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    //Load Composer's autoloader
    require 'PHPMailer/PHPMailer/vendor/autoload.php';

    session_start();
    $email = $_SESSION['email'];
    $last_name = $_SESSION['lastname'];


    if (isset($_POST["verifynow"])) {
        //Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Enable verbose debug output
            $mail->SMTPDebug = 0;//SMTP::DEBUG_SERVER;

            //Send using SMTP
            $mail->isSMTP();

            //Set the SMTP server to send through
            $mail->Host = 'smtp.gmail.com';

            //Enable SMTP authentication
            $mail->SMTPAuth = true;

            //SMTP username
            $mail->Username = 'makmakomakaroni@gmail.com';

            //SMTP password
            $mail->Password = 'ptvs qtbj oaao rktm';

            //Enable TLS encryption;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('makmakomakaroni@gmail.com', 'Pisces Farm');

            //Add a recipient
            $mail->addAddress($email, $fname . " " . $lname);

            //Set email format to HTML
            $mail->isHTML(true);

            $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

            $mail->Subject = 'Email Verification Code';
            $mail->Body    = '<p style="font-size: 20px">Your verification code is: <br><b style="font-size: 50px;">' . $verification_code . '</b></p>';

            $mail->send();
            // echo 'Message has been sent';

            $encrypted_password = password_hash($pass, PASSWORD_DEFAULT);

            // Connect to the database using PDO
            $dsn = "mysql:host=localhost;port=3306;dbname=pisces;charset=utf8mb4";
            $db_username = "root";
            $db_password = "";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, $db_username, $db_password, $options);

            // Prepare SQL statement
            $stmt = $pdo->prepare("UPDATE users SET user_verification_code = ? WHERE Email = ?");
            $stmt->execute([$verification_code, $email]);

            header("Location: VerficationPage.php");
            exit();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }else {

    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Successfully Created</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Archivo+Black&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap');

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            height: 100vh;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 400px;
            width: 100%;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .container img {
            height: 100px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 1.7em;
            margin-bottom: 10px;
            font-weight: 600;
        }
        p {
            font-size: 0.95em;
            color: #6c757d;
            margin-bottom: 20px;
        }
        .btn-custom {
            font-size: 1em;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
        }
        .btn-primary-custom {
            background-color: #007bff;
            color: #fff;
        }
        .btn-primary-custom:hover {
            background-color: #0056b3;
            color: #fff;
        }
        .btn-secondary-custom {
            background-color: #6c757d;
            color: #fff;
        }
        .btn-secondary-custom:hover {
            background-color: #5a6268;
            color: #fff;
        }
        img {
            height: 200px;
        }
        #skip {
            cursor:pointer;
        }
        .resend a {
            color: #007bff;
            text-decoration: none;
            cursor:pointer;
        }

        .resend a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <form class="container" method="post">
        <h1>Welcome to Pisces Farm, <?php echo htmlspecialchars($_SESSION['lastname']); ?>!</h1>
        <!-- <h1>Welcome to Shepherds Palace, Natalia!</h1> -->
        <p>Your account has been successfully created but you need to verify your account to use our system.</p>
        <div class="d-flex justify-content-center">
            <button class="btn btn-primary-custom btn-custom mx-2" id="verifynow" name="verifynow">Verify Now</a>
        </div>
        <a href="Index.html" class="resend" id="skip" name="skip" onclick="return confirmSkip();">Skip for Now</a>
    </form>
    <script>
        function confirmSkip() {
            return confirm("Please note that skipping for now will not make you able to login to our system. You will need to verify first before you log in.");
        }
        </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>