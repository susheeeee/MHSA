<?php
session_start();

if (isset($_POST['login'])) {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    // Sample accounts
    $accounts = [
        // Counselors
        "joshua"  => ["pass" => "bday",       "role" => "counselor", "name" => "Joshua Cruz"],
        "marissa" => ["pass" => "guidance123","role" => "counselor", "name" => "Marissa Tan"],
        
        // Students
        "2021001" => ["pass" => "student123", "role" => "student", "name" => "Maria Clara Santos"],
        "2021002" => ["pass" => "student123", "role" => "student", "name" => "Juan Luna Reyes"],
        "2021003" => ["pass" => "student123", "role" => "student", "name" => "Anna Sofia Lim"],
    ];

    if (isset($accounts[$user]) && $accounts[$user]['pass'] === $pass) {
        $_SESSION['user'] = $accounts[$user]['name'];
        $_SESSION['username'] = $user;
        $_SESSION['role'] = $accounts[$user]['role'];

        if ($accounts[$user]['role'] === "counselor") {
            header("Location: counselor_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Guidance Office Login</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-left">
        <div class="login-box">
            <div class="system-title">
                <h1>Guidance Office</h1>
                <p>Student Mental Health & Appointment System</p>
            </div>

            <form method="POST">
                <div class="input-field">
                    <label>Username / Student ID</label>
                    <input type="text" name="username" required placeholder="ex. joshua or 2021001">
                </div>
                <div class="input-field">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" name="login">Sign In</button>

                <?php if(isset($error)) echo "<div class='error-msg'>$error</div>"; ?>
            </form>

            <div class="sample-accounts">
                <h4>Sample Logins</h4>
                <strong>Counselor:</strong> joshua → bday<br>
                <strong>Student:</strong> 2021001 → student123<br>
                <small>Use any from the list</small>
            </div>
        </div>
    </div>
    <div class="login-right"></div>
</div>

</body>
</html>