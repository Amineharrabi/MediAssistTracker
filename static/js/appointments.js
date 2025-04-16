/**
 * MediAssist - Appointments Module
 * Handles appointment management functionality
 */

// Initialize when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load appointments
    loadAppointments();
    
    // Setup form event listeners
    const appointmentForm = document.getElementById('appointment-form');
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', handleAppointmentFormSubmit);
    }
    
    // Setup calendar view if present
    initializeCalendar();
});

/**
 * Loads appointments from the server and displays them
 */
function loadAppointments() {
    const appointmentsList = document.getElementById('appointments-list');
    if (!appointmentsList) return;
    
    // Show loading state
    appointmentsList.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    fetch('/api/appointments')
        .then(response => response.json())
        .then(appointments => {
            // Sort appointments by date/time
            appointments.sort((a, b) => {
                const dateA = new Date(`${a.date}T${a.time}`);
                const dateB = new Date(`${b.date}T${b.time}`);
                return dateA - dateB;
            });
            
            displayAppointments(appointments);
            
            // If calendar view is active, update it
            updateCalendarWithAppointments(appointments);
        })
        .catch(error => {
            console.error('Error loading appointments:', error);
            appointmentsList.innerHTML = '<div class="alert alert-danger">Failed to load appointments. Please try again.</div>';
        });
}

/**
 * Displays appointments in the UI
 * @param {Array} appointments - Array of appointment objects
 */
function displayAppointments(appointments) {
    const appointmentsList = document.getElementById('appointments-list');
    if (!appointmentsList) return;
    
    // Clear loading state
    appointmentsList.innerHTML = '';
    
    if (appointments.length === 0) {
        appointmentsList.innerHTML = `
            <div class="text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                </div>
                <h5>No appointments scheduled</h5>
                <p class="text-muted">Schedule your first appointment to get started</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-appointment-modal">
                    <i class="fas fa-plus"></i> Schedule Appointment
                </button>
            </div>
        `;
        return;
    }
    
    // Group appointments by date
    const groupedAppointments = {};
    const today = new Date().setHours(0, 0, 0, 0);
    
    appointments.forEach(appointment => {
        const appointmentDate = new Date(appointment.date).setHours(0, 0, 0, 0);
        const isToday = appointmentDate === today;
        const isPast = appointmentDate < today;
        
        const groupKey = isToday ? 'today' : isPast ? 'past' : 'upcoming';
        
        if (!groupedAppointments[groupKey]) {
            groupedAppointments[groupKey] = [];
        }
        
        groupedAppointments[groupKey].push(appointment);
    });
    
    // Create sections for today, upcoming, and past appointments
    const sections = [
        { key: 'today', title: 'Today', icon: 'calendar-day' },
        { key: 'upcoming', title: 'Upcoming Appointments', icon: 'calendar-alt' },
        { key: 'past', title: 'Past Appointments', icon: 'calendar-check' }
    ];
    
    sections.forEach(section => {
        if (!groupedAppointments[section.key] || groupedAppointments[section.key].length === 0) {
            return;
        }
        
        const sectionDiv = document.createElement('div');
        sectionDiv.className = 'appointment-section mb-4';
        
        sectionDiv.innerHTML = `
            <h4 class="section-title">
                <i class="fas fa-${section.icon} me-2"></i> ${section.title}
            </h4>
        `;
        
        const apptList = document.createElement('div');
        apptList.className = 'list-group';
        
        groupedAppointments[section.key].forEach(appointment => {
            const apptItem = document.createElement('div');
            apptItem.className = 'list-group-item appointment-item';
            
            const dateObj = new Date(appointment.date);
            const formattedDate = formatDate(appointment.date);
            const formattedTime = formatTime(appointment.time);
            
            apptItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-1">${appointment.title}</h5>
                        <p class="date-time mb-1">
                            <i class="far fa-calendar me-1"></i> ${formattedDate} at ${formattedTime}
                        </p>
                        ${appointment.doctor ? `<p class="mb-1"><i class="fas fa-user-md me-1"></i> ${appointment.doctor}</p>` : ''}
                        ${appointment.location ? `<p class="mb-1"><i class="fas fa-map-marker-alt me-1"></i> ${appointment.location}</p>` : ''}
                        ${appointment.notes ? `<p class="notes text-muted small mt-2">${appointment.notes}</p>` : ''}
                    </div>
                    <div class="ms-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item edit-appointment" href="#" data-id="${appointment.id}"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item delete-appointment" href="#" data-id="${appointment.id}"><i class="fas fa-trash me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            `;
            
            apptList.appendChild(apptItem);
            
            // Add event listeners for edit and delete
            setTimeout(() => {
                const editBtn = apptItem.querySelector('.edit-appointment');
                const deleteBtn = apptItem.querySelector('.delete-appointment');
                
                if (editBtn) {
                    editBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        openEditAppointmentModal(appointment);
                    });
                }
                
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        confirmDeleteAppointment(appointment);
                    });
                }
            }, 0);
        });
        
        sectionDiv.appendChild(apptList);
        appointmentsList.appendChild(sectionDiv);
    });
    
    // Add button to add new appointment
    const addBtnContainer = document.createElement('div');
    addBtnContainer.className = 'text-center mt-4';
    addBtnContainer.innerHTML = `
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-appointment-modal">
            <i class="fas fa-plus"></i> Schedule Appointment
        </button>
    `;
    
    appointmentsList.appendChild(addBtnContainer);
}

/**
 * Handles the appointment form submission (add/edit)
 * @param {Event} event - The form submit event
 */
function handleAppointmentFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const appointmentId = form.dataset.appointmentId;
    const isEdit = !!appointmentId;
    
    // Validate form
    const titleInput = form.querySelector('#appointment-title');
    const dateInput = form.querySelector('#appointment-date');
    const timeInput = form.querySelector('#appointment-time');
    
    const inputs = [titleInput, dateInput, timeInput];
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateInput(input)) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        return false;
    }
    
    // Prepare data
    const appointmentData = {
        title: titleInput.value.trim(),
        doctor: form.querySelector('#appointment-doctor').value.trim(),
        location: form.querySelector('#appointment-location').value.trim(),
        date: dateInput.value,
        time: timeInput.value,
        notes: form.querySelector('#appointment-notes').value.trim()
    };
    
    // Send request to server
    const url = isEdit ? `/api/appointments/${appointmentId}` : '/api/appointments';
    const method = isEdit ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(appointmentData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to save appointment');
        }
        return response.json();
    })
    .then(data => {
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('add-appointment-modal'));
        modal.hide();
        
        // Show success message
        showAlert(
            isEdit ? 'Appointment updated successfully!' : 'New appointment scheduled successfully!',
            'success'
        );
        
        // Reload appointments
        loadAppointments();
        
        // Reset form
        form.reset();
        form.removeAttribute('data-appointment-id');
        document.getElementById('appointment-modal-title').textContent = 'Schedule New Appointment';
    })
    .catch(error => {
        console.error('Error saving appointment:', error);
        showAlert('Failed to save appointment. Please try again.', 'danger');
    });
}

/**
 * Opens the edit appointment modal with pre-filled data
 * @param {Object} appointment - The appointment object to edit
 */
function openEditAppointmentModal(appointment) {
    const form = document.getElementById('appointment-form');
    if (!form) return;
    
    // Set form data
    form.dataset.appointmentId = appointment.id;
    form.querySelector('#appointment-title').value = appointment.title;
    form.querySelector('#appointment-doctor').value = appointment.doctor || '';
    form.querySelector('#appointment-location').value = appointment.location || '';
    form.querySelector('#appointment-date').value = appointment.date;
    form.querySelector('#appointment-time').value = appointment.time;
    form.querySelector('#appointment-notes').value = appointment.notes || '';
    
    // Update modal title
    document.getElementById('appointment-modal-title').textContent = 'Edit Appointment';
    
    // Open modal
    const modal = new bootstrap.Modal(document.getElementById('add-appointment-modal'));
    modal.show();
}

/**
 * Shows a confirmation dialog before deleting an appointment
 * @param {Object} appointment - The appointment object to delete
 */
function confirmDeleteAppointment(appointment) {
    if (confirm(`Are you sure you want to delete "${appointment.title}"?`)) {
        deleteAppointment(appointment.id);
    }
}

/**
 * Deletes an appointment from the server
 * @param {number} id - The ID of the appointment to delete
 */
function deleteAppointment(id) {
    fetch(`/api/appointments/${id}`, {
        method: 'DELETE'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to delete appointment');
        }
        return response.json();
    })
    .then(data => {
        showAlert('Appointment deleted successfully!', 'success');
        loadAppointments();
    })
    .catch(error => {
        console.error('Error deleting appointment:', error);
        showAlert('Failed to delete appointment. Please try again.', 'danger');
    });
}

/**
 * Initializes the calendar view if present
 */
function initializeCalendar() {
    const calendarView = document.getElementById('calendar-view');
    if (!calendarView) return;
    
    const today = new Date();
    const currentMonth = today.getMonth();
    const currentYear = today.getFullYear();
    
    // Set current month and year
    const monthYearText = document.getElementById('current-month-year');
    if (monthYearText) {
        monthYearText.textContent = new Date(currentYear, currentMonth, 1).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    }
    
    // Generate calendar
    generateCalendar(currentMonth, currentYear);
    
    // Add event listeners for navigation
    const prevMonthBtn = document.getElementById('prev-month');
    const nextMonthBtn = document.getElementById('next-month');
    
    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', function() {
            navigateMonth(-1);
        });
    }
    
    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', function() {
            navigateMonth(1);
        });
    }
}

/**
 * Generates the calendar for a specific month and year
 * @param {number} month - The month (0-11)
 * @param {number} year - The year
 */
function generateCalendar(month, year) {
    const calendarBody = document.getElementById('calendar-body');
    if (!calendarBody) return;
    
    // Clear previous content
    calendarBody.innerHTML = '';
    
    // Store current month and year
    calendarBody.dataset.month = month;
    calendarBody.dataset.year = year;
    
    // Update header
    const monthYearText = document.getElementById('current-month-year');
    if (monthYearText) {
        monthYearText.textContent = new Date(year, month, 1).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    }
    
    // Get first day of month and total days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    // Get current date for highlighting today
    const today = new Date();
    const currentDate = today.getDate();
    const currentMonth = today.getMonth();
    const currentYear = today.getFullYear();
    
    let date = 1;
    
    // Create calendar rows
    for (let i = 0; i < 6; i++) {
        // Break if we've already used all days
        if (date > daysInMonth) break;
        
        const row = document.createElement('tr');
        
        // Create cells for each day
        for (let j = 0; j < 7; j++) {
            const cell = document.createElement('td');
            cell.className = 'calendar-day';
            
            if (i === 0 && j < firstDay) {
                // Empty cells before the first day
                cell.innerHTML = '';
            } else if (date > daysInMonth) {
                // Empty cells after the last day
                cell.innerHTML = '';
            } else {
                // Regular day cells
                cell.innerHTML = `<div class="day-number">${date}</div><div class="day-events"></div>`;
                cell.dataset.date = `${year}-${(month + 1).toString().padStart(2, '0')}-${date.toString().padStart(2, '0')}`;
                
                // Highlight today
                if (date === currentDate && month === currentMonth && year === currentYear) {
                    cell.classList.add('today');
                }
                
                date++;
            }
            
            row.appendChild(cell);
        }
        
        calendarBody.appendChild(row);
    }
    
    // Reload appointments to update calendar
    fetch('/api/appointments')
        .then(response => response.json())
        .then(appointments => {
            updateCalendarWithAppointments(appointments);
        })
        .catch(error => {
            console.error('Error loading appointments for calendar:', error);
        });
}

/**
 * Updates the calendar with appointment data
 * @param {Array} appointments - Array of appointment objects
 */
function updateCalendarWithAppointments(appointments) {
    if (!appointments || !appointments.length) return;
    
    // Clear previous event markers
    document.querySelectorAll('.calendar-day').forEach(day => {
        const eventsContainer = day.querySelector('.day-events');
        if (eventsContainer) {
            eventsContainer.innerHTML = '';
        }
        day.classList.remove('has-events');
    });
    
    // Add appointments to calendar
    appointments.forEach(appointment => {
        const day = document.querySelector(`.calendar-day[data-date="${appointment.date}"]`);
        if (day) {
            day.classList.add('has-events');
            
            const eventsContainer = day.querySelector('.day-events');
            if (eventsContainer) {
                const eventElement = document.createElement('div');
                eventElement.className = 'calendar-event';
                eventElement.innerHTML = appointment.title;
                eventElement.title = `${appointment.title} at ${formatTime(appointment.time)}`;
                eventElement.dataset.id = appointment.id;
                
                // Add click event to show details
                eventElement.addEventListener('click', function() {
                    showAppointmentDetails(appointment);
                });
                
                eventsContainer.appendChild(eventElement);
            }
        }
    });
}

/**
 * Shows appointment details in a modal
 * @param {Object} appointment - The appointment object
 */
function showAppointmentDetails(appointment) {
    const detailsModal = document.getElementById('appointment-details-modal');
    if (!detailsModal) return;
    
    // Format date and time
    const formattedDate = formatDate(appointment.date);
    const formattedTime = formatTime(appointment.time);
    
    // Update modal content
    detailsModal.querySelector('#appointment-details-title').textContent = appointment.title;
    detailsModal.querySelector('#appointment-details-date').textContent = formattedDate;
    detailsModal.querySelector('#appointment-details-time').textContent = formattedTime;
    
    const doctorElement = detailsModal.querySelector('#appointment-details-doctor');
    doctorElement.textContent = appointment.doctor || 'Not specified';
    doctorElement.parentElement.style.display = appointment.doctor ? 'block' : 'none';
    
    const locationElement = detailsModal.querySelector('#appointment-details-location');
    locationElement.textContent = appointment.location || 'Not specified';
    locationElement.parentElement.style.display = appointment.location ? 'block' : 'none';
    
    const notesElement = detailsModal.querySelector('#appointment-details-notes');
    notesElement.textContent = appointment.notes || 'No notes';
    notesElement.parentElement.style.display = appointment.notes ? 'block' : 'none';
    
    // Setup edit and delete buttons
    const editBtn = detailsModal.querySelector('#edit-appointment-btn');
    if (editBtn) {
        editBtn.onclick = function() {
            // Close details modal
            const detailsModalInstance = bootstrap.Modal.getInstance(detailsModal);
            detailsModalInstance.hide();
            
            // Open edit modal
            setTimeout(() => {
                openEditAppointmentModal(appointment);
            }, 500);
        };
    }
    
    const deleteBtn = detailsModal.querySelector('#delete-appointment-btn');
    if (deleteBtn) {
        deleteBtn.onclick = function() {
            // Close details modal
            const detailsModalInstance = bootstrap.Modal.getInstance(detailsModal);
            detailsModalInstance.hide();
            
            // Confirm and delete
            setTimeout(() => {
                confirmDeleteAppointment(appointment);
            }, 500);
        };
    }
    
    // Show modal
    const modal = new bootstrap.Modal(detailsModal);
    modal.show();
}

/**
 * Navigates to the previous or next month
 * @param {number} direction - -1 for previous, 1 for next
 */
function navigateMonth(direction) {
    const calendarBody = document.getElementById('calendar-body');
    if (!calendarBody) return;
    
    let month = parseInt(calendarBody.dataset.month);
    let year = parseInt(calendarBody.dataset.year);
    
    month += direction;
    
    if (month < 0) {
        month = 11;
        year--;
    } else if (month > 11) {
        month = 0;
        year++;
    }
    
    generateCalendar(month, year);
}
