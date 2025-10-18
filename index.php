<?php
//* Start session to store user info
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    require 'includes/db_connect.php';

    //* Get data from form using 'name' attributes

    $email = trim($_POST['mail-field']);
    $password = $_POST['pass-field'];

    $stmt = $conn->prepare('SELECT user_id, password FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1){
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['user_id'];
            header("Location: dashboard.php");
            exit();
        } else{
            $error_message = "The email or password you entered is incorrect.";
        }
    } else{
        $error_message = "The email or password you entered is incorrect";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cabin:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="styles.css">
    <title>Login</title>
</head>
<body>
    <script src="script.js" defer async></script>

    <div class="login-container">
        <div class="left-side" >
            <a href="index.php"><img src="resources/ashesilogo.JPG" alt="ashesi-logo"></a>
            <h1 style="margin: 0;">ATTENDANCE MADE EASY</h1>
            <p style="margin: 0;">A simple and reliable system to track, record, and manage attendance efficiently</p>
        </div>


        <div class="right-side">
            <h2>WELCOME BACK!</h2>
            <p>Log into your account</p>

            <?php
            if(isset($error_message)){
                echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
            }
            ?>

            <form name="signup-form" action="index.php" method="POST">
                <label for="email-field">
                    <p>Email</p>
                    <input type="email" name="mail-field" id="email-field" placeholder="Enter your email" aria-required="true" required>
                </label>

                <label for="password-field">
                    <p>Password</p>
                    <input type="password" name="pass-field" id="password-field" placeholder="********" aria-required="true" required>
                </label>

                <p id="password-cue"></p>

                <label for="submit-btn">
                    <input type="submit" value="Login" id="submit-btn">
                </label>

                <p>Don't have an account? <a href="signup.php">Sign up</a></p>
            </form>
        </div>
    </div>
</body>
</html>