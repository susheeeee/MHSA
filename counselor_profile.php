<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'counselor') {
    header("Location: login.php");
    exit;
}

// initialize profile in session if missing
if (!isset($_SESSION['profile'])) {
    $_SESSION['profile'] = [
        'title' => 'Senior Counselor',
        'email' => '',
        'phone' => '',
        'bio' => ''
    ];
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save profile fields to session (demo persistence)
    $_SESSION['profile']['title'] = trim($_POST['title'] ?? '');
    $_SESSION['profile']['email'] = trim($_POST['email'] ?? '');
    $_SESSION['profile']['phone'] = trim($_POST['phone'] ?? '');
    $_SESSION['profile']['bio'] = trim($_POST['bio'] ?? '');
    $message = 'Profile saved.';
}

$profile = $_SESSION['profile'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Counselor Profile • <?= htmlspecialchars($_SESSION['user']) ?></title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="navbar">
    <div class="logo">Profile • <?= htmlspecialchars($_SESSION['user']) ?></div>
    <a href="counselor_dashboard.php" class="logout-btn">Back</a>
</div>

<div class="dashboard-content">
    <div class="page-title">
        <h1>My Profile</h1>
        <p class="muted">Manage your public counselor profile (demo)</p>
    </div>

    <?php if ($message): ?>
        <div style="background:#e6ffef;border:1px solid #b7efce;padding:12px;border-radius:8px;margin-bottom:12px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" style="max-width:760px;background:white;padding:20px;border-radius:12px;box-shadow:0 12px 30px rgba(0,0,0,0.06);">
        <div style="display:flex;gap:18px;align-items:center;margin-bottom:12px;">
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#D8BEE5,#b88ed9);display:flex;align-items:center;justify-content:center;font-size:32px;color:white;">
                <i class="fa-solid fa-user" style="font-size:34px;color:#fff;"></i>
            </div>
            <div>
                <div style="font-weight:700;font-size:18px;"><?= htmlspecialchars($_SESSION['user']) ?></div>
                <div style="color:#6f4f88;margin-top:6px;"><?= htmlspecialchars($profile['title']) ?></div>
            </div>
        </div>

        <label style="display:block;margin-top:12px;font-weight:700;">Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($profile['title']) ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #eee;">

        <label style="display:block;margin-top:12px;font-weight:700;">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #eee;">

        <label style="display:block;margin-top:12px;font-weight:700;">Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone']) ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #eee;">

        <label style="display:block;margin-top:12px;font-weight:700;">Bio</label>
        <textarea name="bio" rows="5" style="width:100%;padding:10px;border-radius:8px;border:1px solid #eee;"><?= htmlspecialchars($profile['bio']) ?></textarea>

        <div style="margin-top:18px;display:flex;gap:10px;">
            <button type="submit" class="btn">Save Profile</button>
            <a href="counselor_dashboard.php" class="btn" style="background:#f0f0f0;color:#4b2b63;">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
