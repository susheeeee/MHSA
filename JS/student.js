
let selectedDateTime = null;

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '17:00:00',
        height: 'auto',
        selectable: true,
        selectOverlap: false,
        select: function (info) {
            selectedDateTime = info.startStr;
            document.getElementById('bookingDateTime').value = info.startStr.slice(0, 16);
            document.getElementById('bookingModal').style.display = 'flex';
        },
        events: [
            { title: 'You - Academic Stress', start: '2025-11-20T10:00:00', color: '#27ae60' },
            { title: 'You - Family Concerns', start: '2025-11-25T14:30:00', color: '#f39c12' }
        ]
    });
    calendar.render();
});

function closeBookingModal() {
    document.getElementById('bookingModal').style.display = 'none';
}

function submitBooking() {
    const counselor = document.getElementById('counselorSelect').value;
    const duration = document.getElementById('durationSelect').value;
    const reason = document.getElementById('visitReason').value.trim();

    if (!reason) {
        alert('Please enter your reason for visit.');
        return;
    }

    alert(`Appointment Request Sent!\n\nCounselor: ${counselor}\nDate & Time: ${document.getElementById('bookingDateTime').value.replace('T', ' ')}\nDuration: ${duration}\nReason: ${reason}\n\nYou will receive a confirmation soon.`);

    // Visual feedback: add to calendar
    const calendarApi = document.querySelector('#calendar')._calendarApi;
    calendarApi.addEvent({
        title: `You - ${reason.substring(0, 15)}${reason.length > 15 ? '...' : ''}`,
        start: selectedDateTime,
        color: '#f39c12'
    });

    closeBookingModal();
    document.getElementById('visitReason').value = '';
}