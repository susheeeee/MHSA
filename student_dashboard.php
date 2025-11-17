<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}
$name = $_SESSION['user'];
$firstName = explode(' ', $name)[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal • <?= htmlspecialchars($name) ?></title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <style>
        .modal {
            display: none;
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.65); justify-content: center; align-items: center;
            z-index: 9999;
        }
        .modal.active { display: flex; }

        .chat-header { 
            display: flex; align-items: center; gap: 15px; 
            padding: 15px; border-bottom: 1px solid #eee; background: white;
        }
        .counselor-avatar { 
            width: 55px; height: 55px; background: #D8BEE5; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center;
        }
        .counselor-info h4 { margin: 0; font-size: 18px; color: #4b2b63; }
        .counselor-info small { color: #8e44ad; font-weight: 600; }

        .chat-container { 
            height: 380px; overflow-y: auto; padding: 15px; background: #f9f5ff; border-radius: 16px;
        }
        .chat-message { 
            max-width: 80%; margin: 12px 0; padding: 12px 16px; border-radius: 18px; 
            line-height: 1.5; font-size: 15px; clear: both;
        }
        .chat-message.counselor { 
            background: white; border: 1px solid #e0d4f5; 
            border-radius: 18px 18px 18px 4px; float: left;
        }
        .chat-message.student { 
            background: linear-gradient(135deg, #D8BEE5, #b88ed9); color: white;
            border-radius: 18px 18px 4px 18px; float: right;
        }
        .close-modal { font-size: 32px; cursor: pointer; color: #aaa; }
        .close-modal:hover { color: #000; }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">Student Portal • <?= htmlspecialchars($name) ?></div>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="dashboard-content">
    <div class="page-title">
        <h1>Hello, <?= $firstName ?>!</h1>
        <p>We're here to support you anytime</p>
    </div>

    <div class="action-cards">
        <div class="action-card" onclick="openBookingModal()">
            <i class="fa-solid fa-calendar-plus"></i>
            <h3>Book Appointment</h3>
            <p>Schedule a confidential session</p>
        </div>
        <div class="action-card" onclick="openEmergencyChat()">
            <i class="fa-solid fa-comment-medical"></i>
            <h3>I'm Not Okay</h3>
            <p>Instant private chat with a counselor</p>
        </div>
        <div class="action-card" onclick="location.href='resources.php'">
            <i class="fa-solid fa-heart"></i>
            <h3>Mental Health Resources</h3>
            <p>Tips, hotlines & support</p>
        </div>
    </div>

    <div class="card-grid">
        <!-- FULLY INTERACTIVE CALENDAR -->
        <div class="widget">
            <h3>My Schedule</h3>
            <div id="calendar"></div>
            <p style="margin-top:10px; font-size:14px; color:#8e44ad;">
                Click any available time slot to book
            </p>
        </div>

        <!-- Upcoming Appointments -->
        <div class="widget">
            <h3>My Upcoming Appointments</h3>
            <div style="margin-top:15px;">
                <div class="appointment-item" style="background:#f8f5ff; border-radius:12px; padding:15px; margin-bottom:15px;">
                    <div>
                        <strong>Nov 20, 2025 • 10:00 AM</strong><br>
                        <small>Dr. Joshua Cruz • Academic Stress</small>
                    </div>
                    <div>
                        <span style="color:#27ae60; font-weight:bold;">Confirmed</span><br>
                        <button onclick="openAppointmentChat('Dr. Joshua Cruz', 'Senior Counselor', 'joshua')" 
                                style="margin-top:8px; padding:8px 16px; background:#8e44ad; color:white; border:none; border-radius:10px; font-size:13px; cursor:pointer;">
                            Open Chat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="emergency">
        <i class="fa-solid fa-phone"></i> In Crisis? Call Hopeline PH: <strong>0917-558-4673</strong>
    </div>
</div>

<!-- BOOKING MODAL -->
<div class="modal" id="bookingModal">
    <div class="modal-content" style="max-width:520px;" onclick="event.stopPropagation()">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3>Guidance Counseling Appointment</h3>
            <span class="close-modal" onclick="closeModal('bookingModal')">×</span>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:25px; margin-bottom:25px;">
            <div>
                <strong>Your Counselor</strong><br><br>
                <div style="display:flex; align-items:center; gap:15px;">
                    <div class="counselor-avatar">
                        <i class="fa-solid fa-user-tie" style="font-size:32px; color:#4b2b63;"></i>
                    </div>
                    <div>
                        <strong>Dr. Emily Carter</strong><br>
                        <small style="color:#6f4f88;">Senior Counselor<br>20+ years experience</small>
                    </div>
                </div>
            </div>
            <div>
                <label><strong>Counselor</strong></label>
                <select id="counselorSelect" style="width:100%; padding:12px; margin-top:8px; border-radius:10px; border:1px solid #D8BEE5;">
                    <option>Dr. Emily Carter</option>
                    <option>Dr. Joshua Cruz</option>
                    <option>Ms. Marissa Tan</option>
                </select>

                <label style="margin-top:15px;"><strong>Date & Time</strong></label>
                <input type="datetime-local" id="bookingDateTime" style="width:100%; padding:12px; margin-top:8px; border-radius:10px; border:1px solid #D8BEE5;" required>

                <label style="margin-top:15px;"><strong>Duration</strong></label>
                <select id="durationSelect" style="width:100%; padding:12px; margin-top:8px; border-radius:10px; border:1px solid #D8BEE5;">
                    <option>30 minutes</option>
                    <option>45 minutes</option>
                    <option>60 minutes</option>
                </select>
            </div>
        </div>

        <div class="input-field">
            <label><strong>Reason for Visit</strong></label>
            <textarea id="visitReason" rows="4" placeholder="Briefly share what's on your mind..." style="width:100%; padding:14px; border-radius:10px; border:1px solid #D8BEE5; margin-top:8px; resize:none;"></textarea>
        </div>

        <button onclick="submitBooking()" style="margin-top:25px; width:100%; padding:16px; background:linear-gradient(135deg,#b88ed9,#D8BEE5); border:none; border-radius:12px; color:white; font-size:17px; font-weight:bold; cursor:pointer;">
            Book Appointment
        </button>
    </div>
</div>

<!-- EMERGENCY CHAT -->
<div class="modal" id="emergencyChatModal">
    <div class="modal-content" style="max-width:520px;" onclick="event.stopPropagation()">
        <div class="chat-header">
            <div class="counselor-avatar">
                <i class="fa-solid fa-user-tie" style="font-size:28px; color:#4b2b63;"></i>
            </div>
            <div class="counselor-info">
                <h4>Dr. Emily Carter</h4>
                <small>Senior Counselor • Available Now</small>
            </div>
            <span class="close-modal" onclick="closeModal('emergencyChatModal')">×</span>
        </div>
        <div id="emergencyMessages" class="chat-container"></div>
        <div style="display:flex; gap:10px; padding:15px; background:white;">
            <input type="text" id="emergencyInput" placeholder="Type your message..." onkeypress="if(event.key==='Enter') sendEmergencyMessage()" style="flex:1; padding:14px; border-radius:12px; border:1px solid #D8BEE5;">
            <button onclick="sendEmergencyMessage()" style="padding:14px 20px; background:#8e44ad; color:white; border:none; border-radius:12px;">Send</button>
        </div>
    </div>
</div>

<!-- APPOINTMENT CHAT -->
<div class="modal" id="appointmentChatModal">
    <div class="modal-content" style="max-width:520px;" onclick="event.stopPropagation()">
        <div class="chat-header">
            <div class="counselor-avatar">
                <i class="fa-solid fa-user-tie" style="font-size:28px; color:#4b2b63;"></i>
            </div>
            <div class="counselor-info">
                <h4 id="chatCounselorName">Dr. Joshua Cruz</h4>
                <small id="chatCounselorTitle">Senior Counselor</small>
            </div>
            <span class="close-modal" onclick="closeModal('appointmentChatModal')">×</span>
        </div>
        <div id="appointmentMessages" class="chat-container"></div>
        <div style="display:flex; gap:10px; padding:15px; background:white;">
            <input type="text" id="appointmentInput" placeholder="Type your message..." onkeypress="if(event.key==='Enter') sendAppointmentMessage()" style="flex:1; padding:14px; border-radius:12px; border:1px solid #D8BEE5;">
            <button onclick="sendAppointmentMessage()" style="padding:14px 20px; background:#8e44ad; color:white; border:none; border-radius:12px;">Send</button>
        </div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>

// GLOBAL MODAL FUNCTIONS
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}
function openModal(id) {
    document.getElementById(id).classList.add('active');
}

// Close modal when clicking outside
window.onclick = function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
}

// interactable calendaaar
let calendar;
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        slotMinTime: '08:00:00',
        slotMaxTime: '17:00:00',
        selectable: true,
        selectOverlap: false,
        height: 'auto',
        events: [
            { title: 'Academic Stress', start: '2025-11-20T10:00:00', color: '#27ae60' },
            { title: 'Family Concerns', start: '2025-11-25T14:30:00', color: '#f39c12' }
        ],
        select: function(info) {
            document.getElementById('bookingDateTime').value = info.startStr.slice(0,16);
            openModal('bookingModal');
        }
    });
    calendar.render();
});

function openBookingModal() {
    openModal('bookingModal');
}
function submitBooking() {
    alert('Appointment request sent! A counselor will confirm soon.');
    closeModal('bookingModal');
}

// EMERGENCY CHAT
function openEmergencyChat() {
    openModal('emergencyChatModal');
    document.getElementById('emergencyMessages').innerHTML = `
        <div class="chat-message counselor">Hi <?= $firstName ?>, I'm Dr. Emily Carter. You're safe here. How are you feeling right now?</div>
    `;
}
function sendEmergencyMessage() {
    const input = document.getElementById('emergencyInput');
    const msg = input.value.trim();
    if (!msg) return;
    const container = document.getElementById('emergencyMessages');
    container.innerHTML += `<div class="chat-message student">${msg}</div>`;
    container.innerHTML += `<div class="chat-message counselor">Thank you for sharing that with me. I'm here to help however I can.</div>`;
    container.scrollTop = container.scrollHeight;
    input.value = '';
}

// APPOINTMENT CHAT
function openAppointmentChat(name, title) {
    document.getElementById('chatCounselorName').textContent = name;
    document.getElementById('chatCounselorTitle').textContent = title;
    openModal('appointmentChatModal');
    document.getElementById('appointmentMessages').innerHTML = `
        <div class="chat-message counselor">Hi <?= $firstName ?>! How can I support you today?</div>
    `;
}
function sendAppointmentMessage() {
    const input = document.getElementById('appointmentInput');
    const msg = input.value.trim();
    if (!msg) return;
    const container = document.getElementById('appointmentMessages');
    container.innerHTML += `<div class="chat-message student">${msg}</div>`;
    container.scrollTop = container.scrollHeight;
    input.value = '';
}
</script>

</body>
</html>