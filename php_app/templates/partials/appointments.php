<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="card-title">
                        <i class="fas fa-calendar-alt me-2"></i> Appointments
                    </h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                        <i class="fas fa-plus-circle me-1"></i> Add Appointment
                    </button>
                </div>
                <p class="card-text">Manage your medical appointments and get reminders.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Calendar View -->
    <div class="col-lg-7 col-md-12 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar me-2 text-primary"></i> Calendar
                    </h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary me-1" id="prev-month">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="current-month-year" class="h5 mb-0"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-1" id="next-month">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="calendar-container">
                    <div class="calendar-header">
                        <div class="row text-center">
                            <div class="col weekday">Sun</div>
                            <div class="col weekday">Mon</div>
                            <div class="col weekday">Tue</div>
                            <div class="col weekday">Wed</div>
                            <div class="col weekday">Thu</div>
                            <div class="col weekday">Fri</div>
                            <div class="col weekday">Sat</div>
                        </div>
                    </div>
                    <div id="calendar-days" class="calendar-body"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Appointment List -->
    <div class="col-lg-5 col-md-12 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2 text-primary"></i> Upcoming Appointments
                </h5>
            </div>
            <div class="card-body p-0" id="appointments-container">
                <?php if (empty($appointments)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-day fa-4x mb-3 text-muted"></i>
                        <h5 class="text-muted mb-3">No appointments found</h5>
                        <p class="mb-4">You haven't added any appointments yet.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                            <i class="fas fa-plus-circle me-1"></i> Schedule Your First Appointment
                        </button>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php 
                            // Sort appointments by date
                            usort($appointments, function($a, $b) {
                                $dateA = strtotime($a['date']);
                                $dateB = strtotime($b['date']);
                                
                                if ($dateA === $dateB) {
                                    return strtotime($a['time']) - strtotime($b['time']);
                                }
                                
                                return $dateA - $dateB;
                            });
                            
                            // Get today's date for comparison
                            $today = date('Y-m-d');
                            $count = 0;
                            $hasUpcoming = false;
                            
                            // First, show upcoming appointments
                            foreach ($appointments as $appointment):
                                if ($appointment['date'] >= $today):
                                    $hasUpcoming = true;
                        ?>
                            <div class="list-group-item border-start-0 border-end-0 appointment-item" data-id="<?php echo $appointment['id']; ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($appointment['title']); ?></h6>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-3">
                                                <i class="fas fa-calendar-day me-1 text-primary"></i>
                                                <?php echo format_date($appointment['date']); ?>
                                            </div>
                                            <div>
                                                <i class="fas fa-clock me-1 text-primary"></i>
                                                <?php echo format_time($appointment['time']); ?>
                                            </div>
                                        </div>
                                        <?php if (!empty($appointment['doctor'])): ?>
                                            <div class="small text-muted mb-1">
                                                <i class="fas fa-user-md me-1"></i> Dr. <?php echo htmlspecialchars($appointment['doctor']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['location'])): ?>
                                            <div class="small text-muted mb-1">
                                                <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($appointment['location']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button class="dropdown-item view-appointment" type="button" data-id="<?php echo $appointment['id']; ?>">
                                                    <i class="fas fa-eye me-2"></i> View
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item edit-appointment" type="button" data-id="<?php echo $appointment['id']; ?>">
                                                    <i class="fas fa-edit me-2"></i> Edit
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item delete-appointment" type="button" data-id="<?php echo $appointment['id']; ?>">
                                                    <i class="fas fa-trash-alt me-2"></i> Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php 
                                endif;
                            endforeach;
                            
                            if (!$hasUpcoming):
                        ?>
                            <div class="text-center py-3">
                                <p class="text-muted mb-0">No upcoming appointments.</p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Past Appointments Header -->
                        <div class="list-group-item bg-light text-center">
                            <h6 class="mb-0">Past Appointments</h6>
                        </div>
                        
                        <?php 
                            // Then, show past appointments
                            $hasPast = false;
                            foreach ($appointments as $appointment):
                                if ($appointment['date'] < $today):
                                    $hasPast = true;
                        ?>
                            <div class="list-group-item border-start-0 border-end-0 appointment-item text-muted" data-id="<?php echo $appointment['id']; ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($appointment['title']); ?></h6>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-3">
                                                <i class="fas fa-calendar-day me-1"></i>
                                                <?php echo format_date($appointment['date']); ?>
                                            </div>
                                            <div>
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo format_time($appointment['time']); ?>
                                            </div>
                                        </div>
                                        <?php if (!empty($appointment['doctor'])): ?>
                                            <div class="small mb-1">
                                                <i class="fas fa-user-md me-1"></i> Dr. <?php echo htmlspecialchars($appointment['doctor']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['location'])): ?>
                                            <div class="small mb-1">
                                                <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($appointment['location']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button class="dropdown-item view-appointment" type="button" data-id="<?php echo $appointment['id']; ?>">
                                                    <i class="fas fa-eye me-2"></i> View
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item edit-appointment" type="button" data-id="<?php echo $appointment['id']; ?>">
                                                    <i class="fas fa-edit me-2"></i> Edit
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item delete-appointment" type="button" data-id="<?php echo $appointment['id']; ?>">
                                                    <i class="fas fa-trash-alt me-2"></i> Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php 
                                endif;
                            endforeach;
                            
                            if (!$hasPast):
                        ?>
                            <div class="text-center py-3">
                                <p class="text-muted mb-0">No past appointments.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Appointment Modal -->
<div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAppointmentModalLabel">
                    <i class="fas fa-plus-circle me-2"></i> Add Appointment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="appointment-form">
                    <input type="hidden" id="appointment-id">
                    
                    <div class="mb-3">
                        <label for="appointment-title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="appointment-title" placeholder="e.g., Annual checkup, Dentist visit" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="appointment-date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="appointment-date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="appointment-time" class="form-label">Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="appointment-time" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="appointment-doctor" class="form-label">Doctor/Provider</label>
                        <input type="text" class="form-control" id="appointment-doctor" placeholder="e.g., Dr. Smith">
                    </div>
                    
                    <div class="mb-3">
                        <label for="appointment-location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="appointment-location" placeholder="e.g., City Hospital, 123 Main St">
                    </div>
                    
                    <div class="mb-3">
                        <label for="appointment-notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="appointment-notes" rows="3" placeholder="Additional notes or instructions"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-appointment">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- View Appointment Modal -->
<div class="modal fade" id="viewAppointmentModal" tabindex="-1" aria-labelledby="viewAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAppointmentModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i> <span id="view-appointment-title"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-calendar-day fa-fw me-2 text-primary"></i>
                            <h6 class="mb-0">Date:</h6>
                        </div>
                        <p class="ms-4 mb-0" id="view-appointment-date"></p>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-clock fa-fw me-2 text-primary"></i>
                            <h6 class="mb-0">Time:</h6>
                        </div>
                        <p class="ms-4 mb-0" id="view-appointment-time"></p>
                    </div>
                </div>
                
                <div class="mb-4" id="view-doctor-container">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-user-md fa-fw me-2 text-primary"></i>
                        <h6 class="mb-0">Doctor/Provider:</h6>
                    </div>
                    <p class="ms-4 mb-0" id="view-appointment-doctor"></p>
                </div>
                
                <div class="mb-4" id="view-location-container">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-map-marker-alt fa-fw me-2 text-primary"></i>
                        <h6 class="mb-0">Location:</h6>
                    </div>
                    <p class="ms-4 mb-0" id="view-appointment-location"></p>
                </div>
                
                <div id="view-notes-container">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-sticky-note fa-fw me-2 text-primary"></i>
                        <h6 class="mb-0">Notes:</h6>
                    </div>
                    <p class="ms-4 mb-0" id="view-appointment-notes"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="edit-from-view">Edit</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteAppointmentModal" tabindex="-1" aria-labelledby="deleteAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAppointmentModalLabel">
                    <i class="fas fa-trash-alt me-2"></i> Delete Appointment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the appointment "<span id="delete-appointment-title"></span>"?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-appointment">Delete</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Calendar Styles */
    .weekday {
        font-weight: bold;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color);
    }
    
    .calendar-day {
        height: 80px;
        padding: 5px;
        border: 1px solid var(--border-color);
        position: relative;
    }
    
    .calendar-day.today {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }
    
    .calendar-day.other-month {
        opacity: 0.4;
    }
    
    .day-number {
        position: absolute;
        top: 5px;
        left: 5px;
        width: 24px;
        height: 24px;
        line-height: 24px;
        text-align: center;
        font-weight: 500;
    }
    
    .calendar-day.today .day-number {
        background-color: var(--primary-color);
        color: white;
        border-radius: 50%;
    }
    
    .appointment-dot {
        position: absolute;
        bottom: 5px;
        left: 50%;
        transform: translateX(-50%);
        height: 6px;
        width: 6px;
        background-color: var(--primary-color);
        border-radius: 50%;
        margin: 0 2px;
    }
    
    .appointment-item {
        transition: background-color 0.2s;
    }
    
    .appointment-item:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
</style>

<script>
    // Global variables
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let appointments = [];
    
    document.addEventListener('DOMContentLoaded', function() {
        // Load appointments on page load
        loadAppointments();
        
        // Initialize calendar
        initializeCalendar();
        
        // Previous month button
        document.getElementById('prev-month').addEventListener('click', function() {
            navigateMonth(-1);
        });
        
        // Next month button
        document.getElementById('next-month').addEventListener('click', function() {
            navigateMonth(1);
        });
        
        // Save appointment button
        document.getElementById('save-appointment').addEventListener('click', function() {
            handleAppointmentFormSubmit();
        });
        
        // Edit from view button
        document.getElementById('edit-from-view').addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-id');
            bootstrap.Modal.getInstance(document.getElementById('viewAppointmentModal')).hide();
            openEditAppointmentModal(appointmentId);
        });
        
        // Setup event delegation for appointment actions
        document.getElementById('appointments-container').addEventListener('click', function(event) {
            // View appointment
            if (event.target.classList.contains('view-appointment') || 
                event.target.closest('.view-appointment')) {
                
                const button = event.target.classList.contains('view-appointment') ? 
                               event.target : event.target.closest('.view-appointment');
                
                const appointmentId = button.getAttribute('data-id');
                viewAppointment(appointmentId);
            }
            
            // Edit appointment
            if (event.target.classList.contains('edit-appointment') || 
                event.target.closest('.edit-appointment')) {
                
                const button = event.target.classList.contains('edit-appointment') ? 
                               event.target : event.target.closest('.edit-appointment');
                
                const appointmentId = button.getAttribute('data-id');
                openEditAppointmentModal(appointmentId);
            }
            
            // Delete appointment
            if (event.target.classList.contains('delete-appointment') || 
                event.target.closest('.delete-appointment')) {
                
                const button = event.target.classList.contains('delete-appointment') ? 
                               event.target : event.target.closest('.delete-appointment');
                
                const appointmentId = button.getAttribute('data-id');
                confirmDeleteAppointment(appointmentId);
            }
        });
        
        // Confirm delete appointment button
        document.getElementById('confirm-delete-appointment').addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-id');
            deleteAppointment(appointmentId);
        });
        
        // Calendar day click for adding appointment
        document.getElementById('calendar-days').addEventListener('click', function(event) {
            // Only trigger if clicking directly on a calendar day, not on an appointment dot
            if (event.target.classList.contains('calendar-day')) {
                const dateAttr = event.target.getAttribute('data-date');
                if (dateAttr) {
                    resetAppointmentForm();
                    document.getElementById('appointment-date').value = dateAttr;
                    const modal = new bootstrap.Modal(document.getElementById('addAppointmentModal'));
                    modal.show();
                }
            }
        });
        
        // When modal is closed, reset the form
        document.getElementById('addAppointmentModal').addEventListener('hidden.bs.modal', function() {
            resetAppointmentForm();
        });
    });
    
    /**
     * Initializes the calendar view
     */
    function initializeCalendar() {
        generateCalendar(currentMonth, currentYear);
    }
    
    /**
     * Generates the calendar for a specific month and year
     * @param {number} month - The month (0-11)
     * @param {number} year - The year
     */
    function generateCalendar(month, year) {
        const today = new Date();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDay = firstDay.getDay(); // 0 (Sunday) to 6 (Saturday)
        
        // Update header
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        document.getElementById('current-month-year').textContent = `${monthNames[month]} ${year}`;
        
        // Generate calendar days
        const calendarDays = document.getElementById('calendar-days');
        calendarDays.innerHTML = '';
        
        // Create weeks (rows)
        let date = 1;
        for (let i = 0; i < 6; i++) { // Maximum 6 weeks in a month view
            // Break if we've gone past the number of days in the month
            if (date > daysInMonth) break;
            
            const week = document.createElement('div');
            week.className = 'row calendar-week';
            
            // Create days in the week
            for (let j = 0; j < 7; j++) {
                const dayCell = document.createElement('div');
                dayCell.className = 'col calendar-day';
                
                if (i === 0 && j < startingDay) {
                    // Previous month days
                    const prevMonthLastDay = new Date(year, month, 0).getDate();
                    const prevDate = prevMonthLastDay - (startingDay - j - 1);
                    
                    let prevMonth = month - 1;
                    let prevYear = year;
                    if (prevMonth < 0) {
                        prevMonth = 11;
                        prevYear--;
                    }
                    
                    dayCell.innerHTML = `<div class="day-number">${prevDate}</div>`;
                    dayCell.classList.add('other-month');
                    dayCell.setAttribute('data-date', `${prevYear}-${(prevMonth + 1).toString().padStart(2, '0')}-${prevDate.toString().padStart(2, '0')}`);
                } else if (date > daysInMonth) {
                    // Next month days
                    const nextDate = date - daysInMonth;
                    
                    let nextMonth = month + 1;
                    let nextYear = year;
                    if (nextMonth > 11) {
                        nextMonth = 0;
                        nextYear++;
                    }
                    
                    dayCell.innerHTML = `<div class="day-number">${nextDate}</div>`;
                    dayCell.classList.add('other-month');
                    dayCell.setAttribute('data-date', `${nextYear}-${(nextMonth + 1).toString().padStart(2, '0')}-${nextDate.toString().padStart(2, '0')}`);
                    date++;
                } else {
                    // Current month days
                    dayCell.innerHTML = `<div class="day-number">${date}</div>`;
                    
                    // Check if this day is today
                    if (date === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                        dayCell.classList.add('today');
                    }
                    
                    // Set data attribute for the date
                    dayCell.setAttribute('data-date', `${year}-${(month + 1).toString().padStart(2, '0')}-${date.toString().padStart(2, '0')}`);
                    date++;
                }
                
                week.appendChild(dayCell);
            }
            
            calendarDays.appendChild(week);
        }
        
        // Add appointment markers to the calendar
        updateCalendarWithAppointments();
    }
    
    /**
     * Updates the calendar with appointment data
     */
    function updateCalendarWithAppointments() {
        if (!appointments || !appointments.length) return;
        
        const calendarDays = document.querySelectorAll('.calendar-day');
        calendarDays.forEach(day => {
            const dateAttr = day.getAttribute('data-date');
            if (dateAttr) {
                // Find appointments for this date
                const dayAppointments = appointments.filter(app => app.date === dateAttr);
                
                // Add appointment indicators
                if (dayAppointments.length > 0) {
                    // Clear existing dots
                    day.querySelectorAll('.appointment-dot').forEach(dot => dot.remove());
                    
                    // Add new dot
                    const dot = document.createElement('div');
                    dot.className = 'appointment-dot';
                    dot.title = `${dayAppointments.length} appointment${dayAppointments.length > 1 ? 's' : ''}`;
                    day.appendChild(dot);
                    
                    // Make day clickable to show appointments
                    day.style.cursor = 'pointer';
                    
                    // Add tooltip with appointment details
                    const tooltipContent = dayAppointments.map(app => 
                        `${formatTime(app.time)} - ${app.title}`
                    ).join('<br>');
                    
                    // Use Bootstrap tooltip if available
                    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                        const tooltip = new bootstrap.Tooltip(day, {
                            title: tooltipContent,
                            html: true,
                            placement: 'top',
                            trigger: 'hover'
                        });
                    }
                }
            }
        });
    }
    
    /**
     * Navigates to the previous or next month
     * @param {number} direction - -1 for previous, 1 for next
     */
    function navigateMonth(direction) {
        currentMonth += direction;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        } else if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        
        generateCalendar(currentMonth, currentYear);
    }
    
    /**
     * Loads appointments from the server
     */
    function loadAppointments() {
        fetch('/api.php?endpoint=appointments')
            .then(response => response.json())
            .then(data => {
                appointments = data;
                displayAppointments(appointments);
                updateCalendarWithAppointments();
            })
            .catch(error => {
                console.error('Error fetching appointments:', error);
                showAlert('Failed to load appointments.', 'danger');
            });
    }
    
    /**
     * Displays appointments in the UI
     * @param {Array} appointments - Array of appointment objects
     */
    function displayAppointments(appointments) {
        const container = document.getElementById('appointments-container');
        
        if (!appointments || appointments.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-calendar-day fa-4x mb-3 text-muted"></i>
                    <h5 class="text-muted mb-3">No appointments found</h5>
                    <p class="mb-4">You haven't added any appointments yet.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                        <i class="fas fa-plus-circle me-1"></i> Schedule Your First Appointment
                    </button>
                </div>
            `;
            return;
        }
        
        // Sort appointments by date and time
        appointments.sort((a, b) => {
            if (a.date === b.date) {
                return a.time.localeCompare(b.time);
            }
            return a.date.localeCompare(b.date);
        });
        
        // Get today's date for comparison
        const today = new Date().toISOString().split('T')[0];
        
        let html = '<div class="list-group list-group-flush">';
        
        // Upcoming appointments
        const upcomingAppointments = appointments.filter(app => app.date >= today);
        
        if (upcomingAppointments.length > 0) {
            upcomingAppointments.forEach(appointment => {
                html += `
                    <div class="list-group-item border-start-0 border-end-0 appointment-item" data-id="${appointment.id}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${escapeHtml(appointment.title)}</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-3">
                                        <i class="fas fa-calendar-day me-1 text-primary"></i>
                                        ${formatDate(appointment.date)}
                                    </div>
                                    <div>
                                        <i class="fas fa-clock me-1 text-primary"></i>
                                        ${formatTime(appointment.time)}
                                    </div>
                                </div>
                                ${appointment.doctor ? `
                                    <div class="small text-muted mb-1">
                                        <i class="fas fa-user-md me-1"></i> Dr. ${escapeHtml(appointment.doctor)}
                                    </div>
                                ` : ''}
                                ${appointment.location ? `
                                    <div class="small text-muted mb-1">
                                        <i class="fas fa-map-marker-alt me-1"></i> ${escapeHtml(appointment.location)}
                                    </div>
                                ` : ''}
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item view-appointment" type="button" data-id="${appointment.id}">
                                            <i class="fas fa-eye me-2"></i> View
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item edit-appointment" type="button" data-id="${appointment.id}">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item delete-appointment" type="button" data-id="${appointment.id}">
                                            <i class="fas fa-trash-alt me-2"></i> Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html += `
                <div class="text-center py-3">
                    <p class="text-muted mb-0">No upcoming appointments.</p>
                </div>
            `;
        }
        
        // Past appointments header
        html += `
            <div class="list-group-item bg-light text-center">
                <h6 class="mb-0">Past Appointments</h6>
            </div>
        `;
        
        // Past appointments
        const pastAppointments = appointments.filter(app => app.date < today);
        
        if (pastAppointments.length > 0) {
            pastAppointments.forEach(appointment => {
                html += `
                    <div class="list-group-item border-start-0 border-end-0 appointment-item text-muted" data-id="${appointment.id}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${escapeHtml(appointment.title)}</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-3">
                                        <i class="fas fa-calendar-day me-1"></i>
                                        ${formatDate(appointment.date)}
                                    </div>
                                    <div>
                                        <i class="fas fa-clock me-1"></i>
                                        ${formatTime(appointment.time)}
                                    </div>
                                </div>
                                ${appointment.doctor ? `
                                    <div class="small mb-1">
                                        <i class="fas fa-user-md me-1"></i> Dr. ${escapeHtml(appointment.doctor)}
                                    </div>
                                ` : ''}
                                ${appointment.location ? `
                                    <div class="small mb-1">
                                        <i class="fas fa-map-marker-alt me-1"></i> ${escapeHtml(appointment.location)}
                                    </div>
                                ` : ''}
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item view-appointment" type="button" data-id="${appointment.id}">
                                            <i class="fas fa-eye me-2"></i> View
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item edit-appointment" type="button" data-id="${appointment.id}">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item delete-appointment" type="button" data-id="${appointment.id}">
                                            <i class="fas fa-trash-alt me-2"></i> Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html += `
                <div class="text-center py-3">
                    <p class="text-muted mb-0">No past appointments.</p>
                </div>
            `;
        }
        
        html += '</div>';
        container.innerHTML = html;
    }
    
    /**
     * Resets the appointment form
     */
    function resetAppointmentForm() {
        const form = document.getElementById('appointment-form');
        form.reset();
        document.getElementById('appointment-id').value = '';
        
        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('appointment-date').value = today;
        
        // Update modal title
        document.getElementById('addAppointmentModalLabel').innerHTML = '<i class="fas fa-plus-circle me-2"></i> Add Appointment';
    }
    
    /**
     * Views an appointment's details
     * @param {string} appointmentId - The ID of the appointment to view
     */
    function viewAppointment(appointmentId) {
        fetch(`/api.php?endpoint=appointment&id=${appointmentId}`)
            .then(response => response.json())
            .then(appointment => {
                document.getElementById('view-appointment-title').textContent = appointment.title;
                document.getElementById('view-appointment-date').textContent = formatDate(appointment.date);
                document.getElementById('view-appointment-time').textContent = formatTime(appointment.time);
                
                // Doctor (optional)
                const doctorContainer = document.getElementById('view-doctor-container');
                if (appointment.doctor) {
                    document.getElementById('view-appointment-doctor').textContent = appointment.doctor;
                    doctorContainer.style.display = 'block';
                } else {
                    doctorContainer.style.display = 'none';
                }
                
                // Location (optional)
                const locationContainer = document.getElementById('view-location-container');
                if (appointment.location) {
                    document.getElementById('view-appointment-location').textContent = appointment.location;
                    locationContainer.style.display = 'block';
                } else {
                    locationContainer.style.display = 'none';
                }
                
                // Notes (optional)
                const notesContainer = document.getElementById('view-notes-container');
                if (appointment.notes) {
                    document.getElementById('view-appointment-notes').innerHTML = escapeHtml(appointment.notes).replace(/\n/g, '<br>');
                    notesContainer.style.display = 'block';
                } else {
                    notesContainer.style.display = 'none';
                }
                
                // Set appointment ID for edit button
                document.getElementById('edit-from-view').setAttribute('data-id', appointment.id);
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('viewAppointmentModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching appointment:', error);
                showAlert('Failed to fetch appointment details.', 'danger');
            });
    }
    
    /**
     * Opens the edit appointment modal with pre-filled data
     * @param {string} appointmentId - The ID of the appointment to edit
     */
    function openEditAppointmentModal(appointmentId) {
        resetAppointmentForm();
        
        // Fetch appointment data
        fetch(`/api.php?endpoint=appointment&id=${appointmentId}`)
            .then(response => response.json())
            .then(appointment => {
                document.getElementById('appointment-id').value = appointment.id;
                document.getElementById('appointment-title').value = appointment.title;
                document.getElementById('appointment-date').value = appointment.date;
                document.getElementById('appointment-time').value = appointment.time;
                document.getElementById('appointment-doctor').value = appointment.doctor || '';
                document.getElementById('appointment-location').value = appointment.location || '';
                document.getElementById('appointment-notes').value = appointment.notes || '';
                
                // Update modal title
                document.getElementById('addAppointmentModalLabel').innerHTML = '<i class="fas fa-edit me-2"></i> Edit Appointment';
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('addAppointmentModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching appointment:', error);
                showAlert('Failed to fetch appointment details.', 'danger');
            });
    }
    
    /**
     * Handles the appointment form submission
     */
    function handleAppointmentFormSubmit() {
        const form = document.getElementById('appointment-form');
        
        // Check form validity
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form values
        const appointmentId = document.getElementById('appointment-id').value;
        const title = document.getElementById('appointment-title').value;
        const date = document.getElementById('appointment-date').value;
        const time = document.getElementById('appointment-time').value;
        const doctor = document.getElementById('appointment-doctor').value;
        const location = document.getElementById('appointment-location').value;
        const notes = document.getElementById('appointment-notes').value;
        
        // Prepare data
        const data = {
            title,
            date,
            time,
            doctor,
            location,
            notes
        };
        
        // Determine if this is an add or update operation
        const isUpdate = !!appointmentId;
        const method = isUpdate ? 'PUT' : 'POST';
        const url = isUpdate ? `/api.php?endpoint=appointment&id=${appointmentId}` : '/api.php?endpoint=appointments';
        
        // Send request
        fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('addAppointmentModal')).hide();
                
                // Show success message
                showAlert(
                    isUpdate ? 'Appointment updated successfully!' : 'Appointment added successfully!', 
                    'success'
                );
                
                // Reload appointments
                loadAppointments();
            } else {
                showAlert(result.error || 'An error occurred.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred. Please try again.', 'danger');
        });
    }
    
    /**
     * Shows a confirmation dialog before deleting an appointment
     * @param {string} appointmentId - The ID of the appointment to delete
     */
    function confirmDeleteAppointment(appointmentId) {
        // Fetch appointment data to display title
        fetch(`/api.php?endpoint=appointment&id=${appointmentId}`)
            .then(response => response.json())
            .then(appointment => {
                document.getElementById('delete-appointment-title').textContent = appointment.title;
                document.getElementById('confirm-delete-appointment').setAttribute('data-id', appointmentId);
                
                const modal = new bootstrap.Modal(document.getElementById('deleteAppointmentModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching appointment:', error);
                showAlert('Failed to fetch appointment details.', 'danger');
            });
    }
    
    /**
     * Deletes an appointment
     * @param {string} appointmentId - The ID of the appointment to delete
     */
    function deleteAppointment(appointmentId) {
        fetch(`/api.php?endpoint=appointment&id=${appointmentId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('deleteAppointmentModal')).hide();
                
                // Show success message
                showAlert('Appointment deleted successfully!', 'success');
                
                // Reload appointments
                loadAppointments();
            } else {
                showAlert(result.error || 'An error occurred.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred. Please try again.', 'danger');
        });
    }
    
    /**
     * Formats a date for display
     * @param {string} dateStr - Date string (YYYY-MM-DD)
     * @returns {string} Formatted date string
     */
    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }
    
    /**
     * Formats a time for display
     * @param {string} timeStr - Time string (HH:MM:SS or HH:MM)
     * @returns {string} Formatted time string (12-hour format)
     */
    function formatTime(timeStr) {
        // Make sure it has seconds if not provided
        if (timeStr.length === 5) {
            timeStr += ':00';
        }
        
        const date = new Date(`2000-01-01T${timeStr}`);
        return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    }
    
    /**
     * Escapes HTML special characters
     * @param {string} text - Text to escape
     * @returns {string} Escaped text
     */
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    /**
     * Shows an alert message
     * @param {string} message - The message to show
     * @param {string} type - The type of alert (success, danger, warning, info)
     */
    function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('flash-messages');
        
        if (!alertContainer) {
            console.error('Alert container not found');
            return;
        }
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        alertContainer.appendChild(alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => {
                alert.remove();
            }, 150);
        }, 5000);
    }
</script>