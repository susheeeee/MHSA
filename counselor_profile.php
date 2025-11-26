<?php
session_start();
require_once 'db/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'counselor') {
    header("Location: login.php");
    exit;
}

$counselor_id = $_SESSION['user_id'];
$message = '';

// Fetch current profile from DB
$stmt = $conn->prepare("SELECT * FROM counselor WHERE counselor_id = ?");
$stmt->execute([$counselor_id]);
$counselor = $stmt->fetch();

if (!$counselor) {
    die("Counselor not found.");
}

// Save updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title     = trim($_POST['title'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $bio       = trim($_POST['bio'] ?? '');
    $department = trim($_POST['department'] ?? $counselor['department']); // optional

    // Optional: validate email/phone format if you want

    $update = $conn->prepare("
        UPDATE counselor 
        SET department = ?, 
            email = ?, 
            phone = ?, 
            bio = ?
        WHERE counselor_id = ?
    ");

    $update->execute([$department, $email, $phone, $bio, $counselor_id]);
    $message = "Profile updated successfully!";

    // Refresh data
    $stmt->execute([$counselor_id]);
    $counselor = $stmt->fetch();
}

// Helper to get value or default
function val($field) {
    global $counselor;
    return htmlspecialchars($counselor[$field] ?? '');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>My Profile • <?= val('fname') . ' ' . val('lname') ?></title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #6f2b8a 0%, #8e44ad 100%);
            color: white;
            padding: 60px 40px;
            border-radius: 24px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 0;
        }

        .profile-header-content {
            position: relative;
            z-index: 1;
            display: flex;
            gap: 32px;
            align-items: flex-start;
        }

        .profile-avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 64px;
            flex-shrink: 0;
            border: 4px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .profile-header-info {
            flex: 1;
        }

        .profile-header-info h1 {
            font-size: 36px;
            margin: 0 0 12px 0;
            font-weight: 700;
        }

        .profile-header-info .title {
            font-size: 18px;
            font-weight: 600;
            opacity: 0.95;
            margin-bottom: 8px;
        }

        .profile-header-info .department {
            font-size: 14px;
            opacity: 0.85;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(39, 174, 96, 0.2);
            color: #27ae60;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 12px;
        }

        .profile-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .form-section {
            background: white;
            padding: 32px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .form-section h3 {
            font-size: 20px;
            color: #4b2b63;
            margin: 0 0 24px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
        }

        .form-section h3 i {
            color: #8e44ad;
            font-size: 22px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .form-row:last-child {
            margin-bottom: 0;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #4b2b63;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group textarea {
            padding: 14px 16px;
            border: 2px solid #e8dff5;
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: 0.3s;
            background: #fafbfc;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #8e44ad;
            background: white;
            box-shadow: 0 0 0 4px rgba(142, 68, 173, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 140px;
        }

        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 32px;
        }

        .btn-primary {
            padding: 14px 32px;
            background: linear-gradient(135deg, #8e44ad, #6f2b8a);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
            max-width: 200px;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(142, 68, 173, 0.3);
        }

        .btn-secondary {
            padding: 14px 32px;
            background: #f5f1ff;
            color: #8e44ad;
            border: 2px solid #e8dff5;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            max-width: 200px;
        }

        .btn-secondary:hover {
            background: #e8dff5;
            border-color: #8e44ad;
        }

        .success-message {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border: 2px solid #27ae60;
            color: #155724;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 32px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.4s ease;
        }

        .success-message i {
            color: #27ae60;
            font-size: 18px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 2px solid #f0e6ff;
        }

        .info-item {
            padding: 16px;
            background: #f9f7ff;
            border-radius: 12px;
            border-left: 4px solid #8e44ad;
        }

        .info-item-label {
            font-size: 12px;
            color: #8e44ad;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .info-item-value {
            font-size: 16px;
            color: #4b2b63;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .profile-header {
                padding: 40px 24px;
            }

            .profile-header-content {
                flex-direction: column;
                gap: 24px;
                align-items: center;
                text-align: center;
            }

            .profile-avatar {
                width: 120px;
                height: 120px;
                font-size: 56px;
            }

            .profile-header-info h1 {
                font-size: 28px;
            }

            .form-section {
                padding: 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary {
                max-width: none;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="left-group" style="display:flex;align-items:center;gap:12px">
        <button id="profileBtn" class="profile-btn" onclick="toggleProfileDropdown(event)">
            <span class="avatar"><i class="fa-solid fa-user"></i></span>
        </button>
        <div id="profileDropdown" class="profile-dropdown" aria-hidden="true">
            <div class="profile-row" style="padding:12px;">
                <div class="avatar" style="width:48px;height:48px;border-radius:8px;background:linear-gradient(135deg,#D8BEE5,#b88ed9);display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="info">
                    <div style="font-weight:700"><?= val('fname') . ' ' . val('lname') ?></div>
                    <small style="color:#8e44ad"><?= val('department') ?: 'Counselor' ?></small>
                </div>
            </div>
            <a href="counselor_dashboard.php" class="profile-item">Dashboard</a>
            <a href="logout.php" class="profile-item">Logout</a>
        </div>
        <div class="logo">My Profile</div>
    </div>
</div>

<div class="dashboard-content">
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-avatar">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div class="profile-header-info">
                <h1><?= val('fname') . ' ' . (val('mi') ? val('mi').'. ' : '') . val('lname') ?></h1>
                <div class="title"><?= val('title') ?: 'Professional Counselor' ?></div>
                <div class="department">
                    <i class="fa-solid fa-building"></i>
                    <?= val('department') ?: 'Guidance Office' ?>
                </div>
                <div class="status-badge">
                    <i class="fa-solid fa-circle" style="font-size: 8px;"></i>
                    Active & Available
                </div>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="success-message">
            <i class="fa-solid fa-check-circle"></i>
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="profile-form">
        <form method="POST">
            <!-- Contact Information Section -->
            <div class="form-section">
                <h3>
                    <i class="fa-solid fa-envelope"></i>
                    Contact Information
                </h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?= val('email') ?>" placeholder="counselor@school.edu.ph">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="<?= val('phone') ?>" placeholder="+63 917 123 4567">
                    </div>
                </div>
            </div>

            <!-- Professional Information Section -->
            <div class="form-section">
                <h3>
                    <i class="fa-solid fa-briefcase"></i>
                    Professional Information
                </h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Counselor Title</label>
                        <input type="text" id="title" name="title" value="<?= val('title') ?>" placeholder="e.g. Senior Guidance Counselor">
                    </div>
                    <div class="form-group">
                        <label for="department">Department / Office</label>
                        <input type="text" id="department" name="department" value="<?= val('department') ?>" placeholder="e.g. Guidance Office">
                    </div>
                </div>
            </div>

            <!-- Bio Section -->
            <div class="form-section">
                <h3>
                    <i class="fa-solid fa-pen-fancy"></i>
                    Professional Bio
                </h3>
                <div class="form-group">
                    <label for="bio">About You</label>
                    <textarea id="bio" name="bio" placeholder="Share your approach, experience, specializations, credentials, or areas of expertise..."><?= val('bio') ?></textarea>
                </div>
                <p style="font-size: 13px; color: #8e44ad; margin-top: 8px;">
                    <i class="fa-solid fa-circle-info"></i>
                    This bio will be visible to students when they book a session with you.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-save" style="margin-right: 8px;"></i>
                    Save Changes
                </button>
                <a href="counselor_dashboard.php" class="btn-secondary">
                    <i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i>
                    Back to Dashboard
                </a>
            </div>
        </form>
    </div>
</div>

<script>

function toggleProfileDropdown(e) {
    e.stopPropagation();
    const dd = document.getElementById('profileDropdown');
    const hidden = dd.getAttribute('aria-hidden') === 'true';
    dd.setAttribute('aria-hidden', !hidden);
}
document.addEventListener('click', (e) => {
    const dd = document.getElementById('profileDropdown');
    const btn = document.getElementById('profileBtn');
    if (dd && dd.getAttribute('aria-hidden') === 'false' && !dd.contains(e.target) && !btn.contains(e.target)) {
        dd.setAttribute('aria-hidden', 'true');
    }
});
</script>

</body>
</html>