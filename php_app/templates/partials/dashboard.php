<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </h2>
                <p class="card-text">Welcome, <strong><?php echo $_SESSION['username']; ?></strong>! Manage your medications, appointments, and more.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Upcoming Appointments -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">
                    <i class="fa-solid fa-calendar-days me-2 text-primary"></i> Upcoming Appointments
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($upcoming_appointments)): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-calendar-day fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">No upcoming appointments</p>
                        <a href="/index.php?route=appointments" class="btn btn-sm btn-primary mt-3" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;">
                            <i class="fas fa-plus-circle me-1"></i> Add Appointment
                        </a>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($upcoming_appointments as $appointment): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($appointment['title']); ?></h6>
                                        <div class="small text-muted">
                                            <i class="fas fa-calendar me-1"></i> <?php echo format_date($appointment['date']); ?>
                                            <i class="fas fa-clock ms-2 me-1"></i> <?php echo format_time($appointment['time']); ?>
                                        </div>
                                        <?php if (!empty($appointment['doctor'])): ?>
                                            <div class="small text-muted">
                                                <i class="fas fa-user-md me-1"></i> Dr. <?php echo htmlspecialchars($appointment['doctor']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['location'])): ?>
                                            <div class="small text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($appointment['location']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <a href="/index.php?route=appointments" class="btn btn-sm btn-outline-primary" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/index.php?route=appointments" class="btn btn-sm btn-primary" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;">
                            View All Appointments
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Medications -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">
                    <i class="fas fa-laptop-medical me-2 text-primary"></i> Your Medications
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($medications)): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-prescription-bottle fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">No medications added yet</p>
                        <a href="/index.php?route=medications" class="btn btn-sm btn-primary mt-3" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;">
                            <i class="fas fa-plus-circle me-1"></i> Add Medication
                        </a>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php
                        // Display only the first 5 medications
                        $count = 0;
                        foreach ($medications as $medication):
                            if ($count++ >= 5) break;
                        ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($medication['name']); ?></h6>
                                        <div class="small text-muted">
                                            <i class="fas fa-prescription me-1"></i> <?php echo htmlspecialchars($medication['dosage']); ?>
                                            <i class="fas fa-clock ms-2 me-1"></i> <?php echo htmlspecialchars($medication['frequency']); ?>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fas fa-hourglass-half me-1"></i> Times:
                                            <?php
                                            $times = json_decode_safe($medication['time']);
                                            echo implode(', ', array_map(function ($time) {
                                                return format_time($time . ':00');
                                            }, $times));
                                            ?>
                                        </div>
                                    </div>
                                    <a href="/index.php?route=medications" class="btn btn-sm btn-outline-primary" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/index.php?route=medications" class="btn btn-sm btn-primary" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;">
                            View All Medications
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Emergency Contacts -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">
                    <i class="fas fa-address-book me-2 text-primary"></i> Emergency Contacts
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($contacts)): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-user-friends fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">No emergency contacts added yet</p>
                        <a href="/index.php?route=contacts" class="btn btn-sm btn-primary mt-3" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;">
                            <i class="fas fa-plus-circle me-1"></i> Add Contact
                        </a>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php
                        // Display only the first 3 contacts
                        $count = 0;
                        foreach ($contacts as $contact):
                            if ($count++ >= 3) break;
                        ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($contact['name']); ?></h6>
                                        <?php if (!empty($contact['relationship'])): ?>
                                            <div class="small text-muted">
                                                <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($contact['relationship']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="small text-muted">
                                            <i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($contact['phone']); ?>
                                        </div>
                                        <?php if (!empty($contact['email'])): ?>
                                            <div class="small text-muted">
                                                <i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($contact['email']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <a href="/index.php?route=contacts" class="btn btn-sm btn-outline-primary" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/index.php?route=contacts" class="btn btn-sm btn-primary" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;">
                            View All Contacts
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">
                    <i class="fas fa-link me-2 text-primary"></i> Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <a href="/index.php?route=medications" class="card text-center p-3 h-100 text-decoration-none">
                            <div class="card-body p-2">
                                <i class="fas fa-prescription-bottle fa-2x mb-2 text-primary"></i>
                                <h6 class="card-title mb-0">Add Medication</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="/index.php?route=appointments" class="card text-center p-3 h-100 text-decoration-none">
                            <div class="card-body p-2">
                                <i class="fas fa-calendar-plus fa-2x mb-2 text-primary"></i>
                                <h6 class="card-title mb-0">Schedule Appointment</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="/index.php?route=prescriptions" class="card text-center p-3 h-100 text-decoration-none">
                            <div class="card-body p-2">
                                <i class="fas fa-file-medical fa-2x mb-2 text-primary"></i>
                                <h6 class="card-title mb-0">Upload Prescription</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="/index.php?route=contacts" class="card text-center p-3 h-100 text-decoration-none">
                            <div class="card-body p-2">
                                <i class="fas fa-user-plus fa-2x mb-2 text-primary"></i>
                                <h6 class="card-title mb-0">Add Contact</h6>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>