<?php
session_start();

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    if ($user === "joshua" && $pass === "bday") {
        $_SESSION['user'] = $user;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Guidance Appointment System - Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="login-wrapper">

    <div class="login-left">
        <div class="login-box">

            <div class="system-title">
                <h1>Guidance Office</h1>
                <p>Student Appointment Management System</p>
            </div>

            <form method="POST">

                <div class="input-field">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Enter your username">
                </div>

                <div class="input-field">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>

                <button type="submit" name="login">Sign In</button>

                <?php if(isset($error)) { ?>
                    <div class="error-msg"><?= $error ?></div>
                <?php } ?>

            </form>
        </div>
    </div>

    <div class="login-right"></div>
    

</div>

</body>
</html>
