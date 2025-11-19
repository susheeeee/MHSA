<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'counselor') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counselor Dashboard • Guidance Office</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
</head>
<body>

<div class="navbar">
    <div class="logo">Counselor • <?= htmlspecialchars($_SESSION['user']) ?></div>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="dashboard-content">
    <div class="page-title">
        <h1>Welcome back, <?= explode(' ', $_SESSION['user'])[0] ?>!</h1>
        <p>Today is <strong><?= date('l, F j, Y') ?></strong></p>
    </div>

    <div class="card-grid">
        <!-- TODAY'S SCHEDULE -->
        <div class="widget">
            <h3>Today's Schedule</h3>

            <div class="appointment-item" data-id="1">
                <div>
                    <strong id="time-1">10:30 AM</strong> • Maria Clara Santos (2021001)<br>
                    <small>Anxiety Counseling</small>
                </div>
                <div class="actions">
                    <button class="action-btn done-btn" onclick="markStatus(1, 'done')">Done</button>
                    <button class="action-btn monitoring-btn" onclick="markStatus(1, 'monitoring')">Monitoring</button>
                    <button class="action-btn reschedule-btn" onclick="openFollowUpFromList(1, 'Maria Clara Santos')">Follow Up</button>
                    <button class="action-btn chat-btn" onclick="openChatModal('Maria Clara Santos', '2021001')">
                        Chat
                    </button>
                    <span id="status-1" class="status-text"></span>
                </div>
            </div>

            <div class="appointment-item" data-id="2">
                <div>
                    <strong id="time-2">02:00 PM</strong> • Juan Luna Reyes (2021002)<br>
                    <small>Academic Stress</small>
                </div>
                <div class="actions">
                    <button class="action-btn done-btn" onclick="markStatus(2, 'done')">Done</button>
                    <button class="action-btn monitoring-btn" onclick="markStatus(2, 'monitoring')">Monitoring</button>
                    <button class="action-btn reschedule-btn" onclick="openFollowUpFromList(2, 'Juan Luna Reyes')">Follow Up</button>
                    <button class="action-btn chat-btn" onclick="openChatModal('Juan Luna Reyes', '2021002')">
                        Chat
                    </button>
                    <span id="status-2" class="status-text"></span>
                </div>
            </div>

            <div class="appointment-item" data-id="3">
                <div>
                    <strong id="time-3">04:00 PM</strong> • Anna Sofia Lim (2021003)<br>
                    <small>Peer Conflict</small>
                </div>
                <div class="actions">
                    <button class="action-btn done-btn" onclick="markStatus(3, 'done')">Done</button>
                    <button class="action-btn monitoring-btn" onclick="markStatus(3, 'monitoring')">Monitoring</button>
                    <button class="action-btn reschedule-btn" onclick="openFollowUpFromList(3, 'Anna Sofia Lim')">Follow Up</button>
                    <button class="action-btn chat-btn" onclick="openChatModal('Anna Sofia Lim', '2021003')">
                        Chat
                    </button>
                    <span id="status-3" class="status-text"></span>
                </div>
            </div>
        </div>

        <!-- CALENDAR -->
        <div class="widget">
            <h3>Appointment Calendar</h3>
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- EDIT APPOINTMENT MODAL (from calendar click) -->
<div class="modal" id="appointmentModal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('appointmentModal')">×</span>
        <h3 id="modalTitle">Edit Appointment</h3>
        <div class="input-field"><label>Student Name</label><input type="text" id="studentNameInput" required></div>
        <div class="input-field"><label>Concern</label><input type="text" id="concernInput" required></div>
        <div class="input-field"><label>Date & Time</label><input type="datetime-local" id="datetimeInput" required></div>
        <button id="appointmentAction" class="btn">Update Appointment</button>
        <div id="extraActions" style="margin-top:20px;gap:10px;display:flex;flex-wrap:wrap;">
            <button class="action-btn done-btn" id="btnDone">Mark as Done</button>
            <button class="action-btn monitoring-btn" id="btnMonitoring">Mark as Monitoring</button>
            <button class="action-btn" style="background:#e74c3c;color:white;" id="btnDelete">Delete</button>
        </div>
    </div>
</div>

<!-- FOLLOW-UP MODAL (from Today's Schedule) -->
<div class="modal" id="followupModal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('followupModal')">×</span>
        <h3>Schedule Follow-Up</h3>
        <p><strong>Student:</strong> <span id="followupStudentName"></span></p>
        <div class="input-field">
            <label>Follow-Up Date & Time</label>
            <input type="datetime-local" id="followupDateTime" required>
        </div>
        <div class="input-field">
            <label>Notes (optional)</label>
            <textarea id="followupNotes" rows="3" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e9dff3;"></textarea>
        </div>
        <button class="btn" onclick="confirmFollowUpFromList()" style="margin-top:15px;">Schedule Follow-Up</button>
    </div>
</div>

<!-- CHAT MODAL -->
<div class="modal" id="chatModal">
    <div class="modal-content" style="max-width:520px;">
        <div style="background:#f8f5ff;padding:15px;border-bottom:1px solid #eee;display:flex;align-items:center;gap:15px;">
            <div style="width:50px;height:50px;background:#D8BEE5;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-user-graduate" style="font-size:28px;color:#4b2b63;"></i>
            </div>
            <div>
                <h4 style="margin:0;color:#4b2b63;" id="chatStudentName">Student</h4>
                <small style="color:#8e44ad;font-weight:600;" id="chatStudentId">ID</small>
            </div>
            <span class="close-modal" onclick="closeModal('chatModal')" style="margin-left:auto;cursor:pointer;">×</span>
        </div>
        <div id="chatMessages" style="height:380px;overflow-y:auto;padding:15px;background:white;"></div>
        <div style="display:flex;gap:10px;padding:15px;background:white;">
            <input type="text" id="chatInput" placeholder="Type your message..." onkeypress="if(event.key==='Enter') sendMessage()" style="flex:1;padding:14px;border-radius:12px;border:1px solid #D8BEE5;">
            <button onclick="sendMessage()" style="padding:14px 20px;background:#8e44ad;color:white;border:none;border-radius:12px;cursor:pointer;">Send</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="js/counselor.js"></script>

</body>
</html>