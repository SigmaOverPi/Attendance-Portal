<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $fullName = $_POST['name'];
    $email = $_POST['mail-field'];
    $password = $_POST['pass-field'];
    $role = $_POST['role'];

    if(empty($fullName) || empty($email) || empty($password) || empty($role)){
        $error_message = "All fields are required. Please fill out the entire form.";
    } else{
        require 'includes/db_connect.php';

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");

        $stmt->bind_param("ssss", $fullName, $email, $hashedPassword, $role);

        if($stmt->execute()){
            header("Location: index.php?signup=success");
            exit();
        } else{
            $error_message = "Error: Could not create account. The email might already be in use.";
        }

        $stmt->close();
        $conn->close();
    }   
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
    <title>Sign Up</title>
</head>
<body>
    <script src="script.js" defer async></script>

    <div class="login-container">
        <div class="left-side">
            <a href="index.php"><img src="resources/ashesilogo.JPG" alt="ashesi-logo"></a>
            <h1 style="margin: 0;">ATTENDANCE MADE EASY</h1>
            <p style="margin: 0;">A simple and reliable system to track, record, and manage attendance efficiently</p>
        </div>

        <div class="right-side">
            <h2>WELCOME!</h2>
            <p>Create an account</p>

            <?php
            //* Display an error mesage if one exists
            if(isset($error_message)){
                echo '<p class="error">' . htmlspecialchars($error_message) . '</p>';
            }
            ?>

            <form name="signup-form" action="signup.php" method="POST" onsubmit="return validatePassword()">
                <label for="name-field">
                    <p>Name</p>
                    <input type="text" name="name" id="name-field" placeholder="Enter your name" required>
                </label>

                <label for="email-field">
                    <p>Email</p>
                    <input type="email" name="mail-field" id="email-field" placeholder="Enter your email" aria-required="true" required>
                </label>

                <label for="password-field">
                    <p>Password</p>
                    <input type="password" name="pass-field" id="password-field" placeholder="********" aria-required="true" required>
                </label>

                <p id="password-cue"></p>

                <label for="role-field">
                    <p>Role</p>
                    <select name="role" id="role-field">
                        <option value=""></option>
                        <option value="student">Student</option>
                        <option value="faculty-intern">Faculty Intern</option>
                    </select>
                </label>

                <label for="submit-btn">
                    <input type="submit" value="Sign Up" id="submit-btn">
                </label>

                <p>Already have an account? <a href="index.php">Login</a></p>
            </form>
        </div>
    </div>
</body>
</html>