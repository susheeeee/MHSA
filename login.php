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
    <div class="login-right">
        <div class="hotline" role="note" aria-label="Crisis hotline">
            <a class="hotline-card" href="tel:09175584673" aria-label="Call Hopeline PH 0917-558-4673">
                <!-- inline phone SVG for crisp icon without external deps -->
                <span class="hotline-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 3 5.18 2 2 0 0 1 5 3h3a2 2 0 0 1 2 1.72c.07.78.24 1.53.5 2.24a2 2 0 0 1-.45 2.11L9.91 10.91a16 16 0 0 0 6.18 6.18l1.84-1.18a2 2 0 0 1 2.11-.45c.71.26 1.46.43 2.24.5A2 2 0 0 1 22 16.92z"/>
                    </svg>
                </span>
                <span class="hotline-text">
                    <strong>In Crisis?</strong>
                    <small> Call Hopeline PH</small>
                </span>
                <span class="hotline-number">0917-558-4673</span>
            </a>
        </div>
    </div>
</div>

</body>
</html>