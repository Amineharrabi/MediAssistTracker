<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="card-title">
                        <i class="fas fa-pills me-2"></i> Medications
                    </h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicationModal">
                        <i class="fas fa-plus-circle me-1"></i> Add Medication
                    </button>
                </div>
                <p class="card-text">Manage your medications and dosage schedules.</p>
            </div>
        </div>
    </div>
</div>

<div class="row" id="medications-container">
    <?php if (empty($medications)): ?>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-prescription-bottle fa-4x mb-3 text-muted"></i>
                    <h5 class="text-muted mb-3">No medications found</h5>
                    <p class="mb-4">You haven't added any medications yet.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicationModal">
                        <i class="fas fa-plus-circle me-1"></i> Add Your First Medication
                    </button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($medications as $medication): ?>
            <div class="col-lg-4 col-md-6 mb-4 medication-card" data-id="<?php echo $medication['id']; ?>">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?php echo htmlspecialchars($medication['name']); ?></h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item edit-medication" type="button" data-id="<?php echo $medication['id']; ?>">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item delete-medication" type="button" data-id="<?php echo $medication['id']; ?>">
                                            <i class="fas fa-trash-alt me-2"></i> Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-prescription me-2 text-primary"></i>
                                <h6 class="mb-0">Dosage:</h6>
                            </div>
                            <p class="mb-0 ps-4"><?php echo htmlspecialchars($medication['dosage']); ?></p>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-sync-alt me-2 text-primary"></i>
                                <h6 class="mb-0">Frequency:</h6>
                            </div>
                            <p class="mb-0 ps-4"><?php echo htmlspecialchars($medication['frequency']); ?></p>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock me-2 text-primary"></i>
                                <h6 class="mb-0">Times:</h6>
                            </div>
                            <div class="ps-4">
                                <?php 
                                    $times = json_decode_safe($medication['time']);
                                    foreach ($times as $time): 
                                ?>
                                    <span class="badge bg-light text-dark me-1 mb-1 py-2 px-3">
                                        <i class="far fa-clock me-1"></i> <?php echo format_time($time . ':00'); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php if (!empty($medication['notes'])): ?>
                            <div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-sticky-note me-2 text-primary"></i>
                                    <h6 class="mb-0">Notes:</h6>
                                </div>
                                <p class="mb-0 ps-4"><?php echo nl2br(htmlspecialchars($medication['notes'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-muted small">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-calendar-alt me-1"></i> Added: <?php echo format_date($medication['created_at']); ?></span>
                            <?php if ($medication['updated_at'] != $medication['created_at']): ?>
                                <span><i class="fas fa-edit me-1"></i> Updated: <?php echo format_date($medication['updated_at']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Add Medication Modal -->
<div class="modal fade" id="addMedicationModal" tabindex="-1" aria-labelledby="addMedicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMedicationModalLabel">
                    <i class="fas fa-plus-circle me-2"></i> Add Medication
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="medication-form">
                    <input type="hidden" id="medication-id">
                    
                    <div class="mb-3">
                        <label for="medication-name" class="form-label">Medication Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="medication-name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="medication-dosage" class="form-label">Dosage <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="medication-dosage" placeholder="e.g., 10mg, 1 tablet" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="medication-frequency" class="form-label">Frequency <span class="text-danger">*</span></label>
                        <select class="form-select" id="medication-frequency" required>
                            <option value="">Select frequency...</option>
                            <option value="Daily">Daily</option>
                            <option value="Twice Daily">Twice Daily</option>
                            <option value="Three Times Daily">Three Times Daily</option>
                            <option value="Every Other Day">Every Other Day</option>
                            <option value="Weekly">Weekly</option>
                            <option value="As Needed">As Needed</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Time(s) <span class="text-danger">*</span></label>
                        <div id="time-slots">
                            <div class="input-group mb-2">
                                <input type="time" class="form-control medication-time" required>
                                <button type="button" class="btn btn-outline-secondary remove-time" disabled>
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary add-time-slot">
                            <i class="fas fa-plus me-1"></i> Add Time
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <label for="medication-notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="medication-notes" rows="3" placeholder="Additional notes or instructions"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-medication">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteMedicationModal" tabindex="-1" aria-labelledby="deleteMedicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMedicationModalLabel">
                    <i class="fas fa-trash-alt me-2"></i> Delete Medication
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<span id="delete-medication-name"></span>"?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-medication">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load medications on page load
        loadMedications();
        
        // Add Time Slot Button
        document.querySelector('.add-time-slot').addEventListener('click', function() {
            addTimeSlot();
        });
        
        // Save Medication Button
        document.getElementById('save-medication').addEventListener('click', function() {
            handleMedicationFormSubmit();
        });
        
        // Setup event delegation for edit and delete buttons
        document.getElementById('medications-container').addEventListener('click', function(event) {
            if (event.target.classList.contains('edit-medication') || 
                event.target.closest('.edit-medication')) {
                
                const button = event.target.classList.contains('edit-medication') ? 
                               event.target : event.target.closest('.edit-medication');
                
                const medicationId = button.getAttribute('data-id');
                openEditMedicationModal(medicationId);
            }
            
            if (event.target.classList.contains('delete-medication') || 
                event.target.closest('.delete-medication')) {
                
                const button = event.target.classList.contains('delete-medication') ? 
                               event.target : event.target.closest('.delete-medication');
                
                const medicationId = button.getAttribute('data-id');
                confirmDeleteMedication(medicationId);
            }
        });
        
        // Confirm Delete Medication Button
        document.getElementById('confirm-delete-medication').addEventListener('click', function() {
            const medicationId = this.getAttribute('data-id');
            deleteMedication(medicationId);
        });
    });
    
    /**
     * Adds a new time slot input to the form
     */
    function addTimeSlot() {
        const timeSlots = document.getElementById('time-slots');
        const newSlot = document.createElement('div');
        newSlot.className = 'input-group mb-2';
        newSlot.innerHTML = `
            <input type="time" class="form-control medication-time" required>
            <button type="button" class="btn btn-outline-secondary remove-time">
                <i class="fas fa-minus"></i>
            </button>
        `;
        
        timeSlots.appendChild(newSlot);
        
        // Enable all remove buttons if we have more than one time slot
        if (timeSlots.querySelectorAll('.input-group').length > 1) {
            timeSlots.querySelectorAll('.remove-time').forEach(button => {
                button.disabled = false;
            });
        }
        
        // Add event listener to the new remove button
        newSlot.querySelector('.remove-time').addEventListener('click', function() {
            this.closest('.input-group').remove();
            
            // Disable the last remove button if only one time slot remains
            const slots = timeSlots.querySelectorAll('.input-group');
            if (slots.length === 1) {
                slots[0].querySelector('.remove-time').disabled = true;
            }
        });
    }
    
    /**
     * Resets the medication form
     */
    function resetMedicationForm() {
        const form = document.getElementById('medication-form');
        form.reset();
        document.getElementById('medication-id').value = '';
        
        // Reset time slots to a single empty input
        const timeSlots = document.getElementById('time-slots');
        timeSlots.innerHTML = `
            <div class="input-group mb-2">
                <input type="time" class="form-control medication-time" required>
                <button type="button" class="btn btn-outline-secondary remove-time" disabled>
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        `;
        
        // Update modal title
        document.getElementById('addMedicationModalLabel').innerHTML = '<i class="fas fa-plus-circle me-2"></i> Add Medication';
    }
    
    /**
     * Opens the edit medication modal with pre-filled data
     * @param {string} medicationId - The ID of the medication to edit
     */
    function openEditMedicationModal(medicationId) {
        resetMedicationForm();
        
        // Fetch medication data
        fetch(`/api.php?endpoint=medication&id=${medicationId}`)
            .then(response => response.json())
            .then(medication => {
                document.getElementById('medication-id').value = medication.id;
                document.getElementById('medication-name').value = medication.name;
                document.getElementById('medication-dosage').value = medication.dosage;
                document.getElementById('medication-frequency').value = medication.frequency;
                document.getElementById('medication-notes').value = medication.notes || '';
                
                // Set time slots
                const times = JSON.parse(medication.time);
                const timeSlots = document.getElementById('time-slots');
                timeSlots.innerHTML = ''; // Clear existing slots
                
                times.forEach((time, index) => {
                    const newSlot = document.createElement('div');
                    newSlot.className = 'input-group mb-2';
                    newSlot.innerHTML = `
                        <input type="time" class="form-control medication-time" value="${time}" required>
                        <button type="button" class="btn btn-outline-secondary remove-time">
                            <i class="fas fa-minus"></i>
                        </button>
                    `;
                    
                    timeSlots.appendChild(newSlot);
                    
                    // Add event listener to the remove button
                    newSlot.querySelector('.remove-time').addEventListener('click', function() {
                        this.closest('.input-group').remove();
                        
                        // Disable the last remove button if only one time slot remains
                        const slots = timeSlots.querySelectorAll('.input-group');
                        if (slots.length === 1) {
                            slots[0].querySelector('.remove-time').disabled = true;
                        }
                    });
                });
                
                // Disable remove buttons if only one time slot
                const slots = timeSlots.querySelectorAll('.input-group');
                slots.forEach(slot => {
                    slot.querySelector('.remove-time').disabled = slots.length === 1;
                });
                
                // Update modal title
                document.getElementById('addMedicationModalLabel').innerHTML = '<i class="fas fa-edit me-2"></i> Edit Medication';
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('addMedicationModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching medication:', error);
                showAlert('Failed to fetch medication details.', 'danger');
            });
    }
    
    /**
     * Handles the medication form submission
     */
    function handleMedicationFormSubmit() {
        const form = document.getElementById('medication-form');
        
        // Check form validity
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form values
        const medicationId = document.getElementById('medication-id').value;
        const name = document.getElementById('medication-name').value;
        const dosage = document.getElementById('medication-dosage').value;
        const frequency = document.getElementById('medication-frequency').value;
        const notes = document.getElementById('medication-notes').value;
        
        // Get all time inputs
        const timeInputs = document.querySelectorAll('.medication-time');
        const times = Array.from(timeInputs).map(input => input.value);
        
        // Check if all times are filled
        if (times.some(time => !time)) {
            showAlert('Please fill in all time fields.', 'danger');
            return;
        }
        
        // Prepare data
        const data = {
            name,
            dosage,
            frequency,
            times,
            notes
        };
        
        // Determine if this is an add or update operation
        const isUpdate = !!medicationId;
        const method = isUpdate ? 'PUT' : 'POST';
        const url = isUpdate ? `/api.php?endpoint=medication&id=${medicationId}` : '/api.php?endpoint=medications';
        
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
                bootstrap.Modal.getInstance(document.getElementById('addMedicationModal')).hide();
                
                // Show success message
                showAlert(
                    isUpdate ? 'Medication updated successfully!' : 'Medication added successfully!', 
                    'success'
                );
                
                // Reload medications
                loadMedications();
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
     * Shows a confirmation dialog before deleting a medication
     * @param {string} medicationId - The ID of the medication to delete
     */
    function confirmDeleteMedication(medicationId) {
        // Fetch medication data to display name
        fetch(`/api.php?endpoint=medication&id=${medicationId}`)
            .then(response => response.json())
            .then(medication => {
                document.getElementById('delete-medication-name').textContent = medication.name;
                document.getElementById('confirm-delete-medication').setAttribute('data-id', medicationId);
                
                const modal = new bootstrap.Modal(document.getElementById('deleteMedicationModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching medication:', error);
                showAlert('Failed to fetch medication details.', 'danger');
            });
    }
    
    /**
     * Deletes a medication
     * @param {string} medicationId - The ID of the medication to delete
     */
    function deleteMedication(medicationId) {
        fetch(`/api.php?endpoint=medication&id=${medicationId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('deleteMedicationModal')).hide();
                
                // Show success message
                showAlert('Medication deleted successfully!', 'success');
                
                // Reload medications
                loadMedications();
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
     * Loads medications from the server
     */
    function loadMedications() {
        fetch('/api.php?endpoint=medications')
            .then(response => response.json())
            .then(medications => {
                displayMedications(medications);
            })
            .catch(error => {
                console.error('Error fetching medications:', error);
                showAlert('Failed to load medications.', 'danger');
            });
    }
    
    /**
     * Displays medications on the page
     * @param {Array} medications - Array of medication objects
     */
    function displayMedications(medications) {
        const container = document.getElementById('medications-container');
        
        if (!medications || medications.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-prescription-bottle fa-4x mb-3 text-muted"></i>
                            <h5 class="text-muted mb-3">No medications found</h5>
                            <p class="mb-4">You haven't added any medications yet.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicationModal">
                                <i class="fas fa-plus-circle me-1"></i> Add Your First Medication
                            </button>
                        </div>
                    </div>
                </div>
            `;
            return;
        }
        
        let html = '';
        
        medications.forEach(medication => {
            const times = JSON.parse(medication.time);
            const timesHtml = times.map(time => {
                const formattedTime = formatTime(time + ':00');
                return `<span class="badge bg-light text-dark me-1 mb-1 py-2 px-3">
                            <i class="far fa-clock me-1"></i> ${formattedTime}
                        </span>`;
            }).join('');
            
            html += `
                <div class="col-lg-4 col-md-6 mb-4 medication-card" data-id="${medication.id}">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">${escapeHtml(medication.name)}</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item edit-medication" type="button" data-id="${medication.id}">
                                                <i class="fas fa-edit me-2"></i> Edit
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item delete-medication" type="button" data-id="${medication.id}">
                                                <i class="fas fa-trash-alt me-2"></i> Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-prescription me-2 text-primary"></i>
                                    <h6 class="mb-0">Dosage:</h6>
                                </div>
                                <p class="mb-0 ps-4">${escapeHtml(medication.dosage)}</p>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-sync-alt me-2 text-primary"></i>
                                    <h6 class="mb-0">Frequency:</h6>
                                </div>
                                <p class="mb-0 ps-4">${escapeHtml(medication.frequency)}</p>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-clock me-2 text-primary"></i>
                                    <h6 class="mb-0">Times:</h6>
                                </div>
                                <div class="ps-4">
                                    ${timesHtml}
                                </div>
                            </div>
                            ${medication.notes ? `
                                <div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-sticky-note me-2 text-primary"></i>
                                        <h6 class="mb-0">Notes:</h6>
                                    </div>
                                    <p class="mb-0 ps-4">${escapeHtml(medication.notes).replace(/\n/g, '<br>')}</p>
                                </div>
                            ` : ''}
                        </div>
                        <div class="card-footer bg-transparent border-0 text-muted small">
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-calendar-alt me-1"></i> Added: ${formatDate(medication.created_at)}</span>
                                ${medication.updated_at !== medication.created_at ? 
                                    `<span><i class="fas fa-edit me-1"></i> Updated: ${formatDate(medication.updated_at)}</span>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    /**
     * Formats a date for display
     * @param {string} dateStr - Date string
     * @returns {string} Formatted date string
     */
    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    }
    
    /**
     * Formats a time for display
     * @param {string} timeStr - Time string (HH:MM:SS)
     * @returns {string} Formatted time string (12-hour format)
     */
    function formatTime(timeStr) {
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