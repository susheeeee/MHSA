let calendar;
let currentAppointmentId = 0;

document.addEventListener('DOMContentLoaded', function () {
    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'timeGridWeek',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        height: 'auto',
        slotMinTime: '08:00:00',
        slotMaxTime: '18:00:00',
        editable: true,
        events: [
            { id: '1', title: 'Maria Clara Santos - Anxiety', start: '2025-11-18T10:30:00', color: '#9b59b6' },
            { id: '2', title: 'Juan Luna Reyes - Academic Stress', start: '2025-11-18T14:00:00', color: '#e67e22' },
            { id: '3', title: 'Anna Sofia Lim - Peer Conflict', start: '2025-11-18T16:00:00', color: '#e74c3c' }
        ],
        eventClick: function (info) {
            openEditModal(info.event);
        },
        eventDrop: function (info) {
            const id = info.event.id;
            const newTime = info.event.start.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            const timeEl = document.getElementById('time-' + id);
            if (timeEl) timeEl.textContent = newTime;
            showToast('Rescheduled via drag-drop!', 'info');
        }
    });
    calendar.render();
});

// EDIT APPOINTMENT
function openEditModal(event) {
    currentAppointmentId = event.id;
    const [name, concern] = event.title.split(' - ');

    document.getElementById('modalTitle').textContent = 'Edit Appointment';
    document.getElementById('studentNameInput').value = name || '';
    document.getElementById('concernInput').value = concern || '';
    document.getElementById('datetimeInput').value = event.start.toISOString().slice(0, 16);

    document.getElementById('appointmentAction').onclick = () => updateAppointment(event);
    document.getElementById('extraActions').style.display = 'flex';
    document.getElementById('appointmentModal').style.display = 'flex';
}

// RESCHEDULE FROM LIST
function openRescheduleFromList(id, name) {
    currentAppointmentId = id;
    document.getElementById('rescheduleStudentName').textContent = name;
    const event = calendar.getEventById(id);
    if (event) {
        document.getElementById('newDateTime').value = event.start.toISOString().slice(0, 16);
    }
    document.getElementById('rescheduleModal').style.display = 'flex';
}

function confirmRescheduleFromList() {
    const newDateTime = document.getElementById('newDateTime').value;
    if (!newDateTime) return showToast('Please select a new date and time', 'error');

    const event = calendar.getEventById(currentAppointmentId);
    if (event) {
        event.setStart(newDateTime + ':00'); 

        // Update time in Today's Schedule
        const timeStr = new Date(newDateTime).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        const timeEl = document.getElementById('time-' + currentAppointmentId);
        if (timeEl) timeEl.textContent = timeStr;
    }

    showToast('Appointment rescheduled!', 'success');
    closeModal('rescheduleModal');
}

// UPDATE APPOINTMENT
function updateAppointment(event) {
    const name = document.getElementById('studentNameInput').value.trim();
    const concern = document.getElementById('concernInput').value.trim();
    if (!name || !concern) return showToast('Please fill all fields', 'error');

    event.setProp('title', `${name} - ${concern}`);
    showToast('Appointment updated!', 'success');
    closeModal('appointmentModal');
}

function deleteAppointment(event) {
    if (confirm('Delete this appointment permanently?')) {
        event.remove();
        showToast('Appointment deleted', 'warning');
        closeModal('appointmentModal');
    }
}

function markStatus(id, status) {
    const el = document.getElementById('status-' + id);
    if (el) {
        el.textContent = status === 'done' ? 'Done' : 'Monitoring';
        el.style.color = status === 'done' ? '#27ae60' : '#e67e22';
        el.style.fontWeight = 'bold';
    }
    showToast(`Marked as ${status === 'done' ? 'Done' : 'Monitoring'}`, 'success');
}

// UNIVERSAL CLOSE MODAL FUNCTION
function closeModal(modalId = null) {
    const modals = modalId ? [modalId] : ['appointmentModal', 'rescheduleModal', 'chatModal'];
    modals.forEach(id => {
        const modal = document.getElementById(id);
        if (modal) modal.style.display = 'none';
    });
    const extra = document.getElementById('extraActions');
    if (extra) extra.style.display = 'none';
}

// CHAT
function openChatModal(name, id) {
    document.getElementById('chatStudentName').textContent = name;
    document.getElementById('chatStudentId').textContent = id;
    document.getElementById('chatMessages').innerHTML = `
        <div class="msg student">Hi po, medyo nahihirapan po ako ngayon...</div>
        <div class="msg counselor">Salamat sa pag-share. Ano po ang nangyari?</div>
    `;
    document.getElementById('chatModal').style.display = 'flex';
}

function sendMessage() {
    const input = document.getElementById('chatInput');
    const msg = input.value.trim();
    if (!msg) return;
    document.getElementById('chatMessages').innerHTML += `<div class="msg counselor">${msg}</div>`;
    document.getElementById('chatMessages').scrollTop = document.getElementById('chatMessages').scrollHeight;
    input.value = '';
}

// TOAST
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position:fixed;top:20px;right:20px;z-index:9999;padding:16px 30px;
        background:${type==='error'?'#e74c3c':type==='warning'?'#f39c12':type==='info'?'#3498db':'#27ae60'};
        color:white;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.3);
        font-size:15px;font-weight:600;animation:slideIn 0.4s ease;
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

// CLOSE MODAL WHEN CLICKING OUTSIDE
window.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal')) {
        closeModal(e.target.id);
    }
});