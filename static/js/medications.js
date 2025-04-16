/**
 * MediAssist - Medications Module
 * Handles medication management functionality
 */

// Initialize when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load medications
    loadMedications();
    
    // Setup form event listeners
    const medicationForm = document.getElementById('medication-form');
    if (medicationForm) {
        medicationForm.addEventListener('submit', handleMedicationFormSubmit);
    }
    
    // Setup add time button
    const addTimeBtn = document.getElementById('add-time-btn');
    if (addTimeBtn) {
        addTimeBtn.addEventListener('click', addTimeSlot);
    }
});

/**
 * Loads medications from the server and displays them
 */
function loadMedications() {
    const medicationsList = document.getElementById('medications-list');
    if (!medicationsList) return;
    
    // Show loading state
    medicationsList.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    fetch('/api/medications')
        .then(response => response.json())
        .then(medications => {
            displayMedications(medications);
        })
        .catch(error => {
            console.error('Error loading medications:', error);
            medicationsList.innerHTML = '<div class="alert alert-danger">Failed to load medications. Please try again.</div>';
        });
}

/**
 * Displays medications in the UI
 * @param {Array} medications - Array of medication objects
 */
function displayMedications(medications) {
    const medicationsList = document.getElementById('medications-list');
    if (!medicationsList) return;
    
    // Clear loading state
    medicationsList.innerHTML = '';
    
    if (medications.length === 0) {
        medicationsList.innerHTML = `
            <div class="text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-pills fa-3x text-muted"></i>
                </div>
                <h5>No medications added yet</h5>
                <p class="text-muted">Add your first medication to get started</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-medication-modal">
                    <i class="fas fa-plus"></i> Add Medication
                </button>
            </div>
        `;
        return;
    }
    
    // Create row
    const row = document.createElement('div');
    row.className = 'row';
    
    // Add each medication card
    medications.forEach(medication => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4 mb-4';
        
        // Parse time array from JSON string
        let timeArray = [];
        try {
            timeArray = JSON.parse(medication.time);
        } catch (e) {
            console.error('Error parsing time:', e);
            timeArray = [];
        }
        
        // Create time badges
        let timeBadges = '';
        timeArray.forEach(time => {
            timeBadges += `<span class="time-badge">${formatTime(time)}</span>`;
        });
        
        col.innerHTML = `
            <div class="card medication-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title">${medication.name}</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item edit-medication" href="#" data-id="${medication.id}"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item delete-medication" href="#" data-id="${medication.id}"><i class="fas fa-trash me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                    </div>
                    <p class="dosage mb-2">${medication.dosage}</p>
                    <div class="mb-3">
                        <span class="badge bg-secondary">${medication.frequency}</span>
                    </div>
                    <div class="time-slots mb-3">
                        ${timeBadges || '<span class="text-muted">No specific times set</span>'}
                    </div>
                    ${medication.notes ? `<p class="notes text-muted small">${medication.notes}</p>` : ''}
                </div>
            </div>
        `;
        
        row.appendChild(col);
        
        // Add event listeners for edit and delete
        setTimeout(() => {
            const editBtn = col.querySelector('.edit-medication');
            const deleteBtn = col.querySelector('.delete-medication');
            
            if (editBtn) {
                editBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    openEditMedicationModal(medication);
                });
            }
            
            if (deleteBtn) {
                deleteBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    confirmDeleteMedication(medication);
                });
            }
        }, 0);
    });
    
    medicationsList.appendChild(row);
}

/**
 * Handles the medication form submission (add/edit)
 * @param {Event} event - The form submit event
 */
function handleMedicationFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const medicationId = form.dataset.medicationId;
    const isEdit = !!medicationId;
    
    // Validate form
    const nameInput = form.querySelector('#medication-name');
    const dosageInput = form.querySelector('#medication-dosage');
    const frequencyInput = form.querySelector('#medication-frequency');
    
    const inputs = [nameInput, dosageInput, frequencyInput];
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateInput(input)) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        return false;
    }
    
    // Collect time slots
    const timeContainer = form.querySelector('#time-slots-container');
    const timeInputs = timeContainer.querySelectorAll('input[type="time"]');
    const times = [];
    
    timeInputs.forEach(input => {
        if (input.value) {
            times.push(input.value);
        }
    });
    
    // Prepare data
    const medicationData = {
        name: nameInput.value.trim(),
        dosage: dosageInput.value.trim(),
        frequency: frequencyInput.value,
        time: times,
        notes: form.querySelector('#medication-notes').value.trim()
    };
    
    // Send request to server
    const url = isEdit ? `/api/medications/${medicationId}` : '/api/medications';
    const method = isEdit ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(medicationData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to save medication');
        }
        return response.json();
    })
    .then(data => {
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('add-medication-modal'));
        modal.hide();
        
        // Show success message
        showAlert(
            isEdit ? 'Medication updated successfully!' : 'New medication added successfully!',
            'success'
        );
        
        // Reload medications
        loadMedications();
        
        // Reset form
        form.reset();
        resetTimeSlots();
        form.removeAttribute('data-medication-id');
        document.getElementById('medication-modal-title').textContent = 'Add New Medication';
    })
    .catch(error => {
        console.error('Error saving medication:', error);
        showAlert('Failed to save medication. Please try again.', 'danger');
    });
}

/**
 * Opens the edit medication modal with pre-filled data
 * @param {Object} medication - The medication object to edit
 */
function openEditMedicationModal(medication) {
    const form = document.getElementById('medication-form');
    if (!form) return;
    
    // Set form data
    form.dataset.medicationId = medication.id;
    form.querySelector('#medication-name').value = medication.name;
    form.querySelector('#medication-dosage').value = medication.dosage;
    form.querySelector('#medication-frequency').value = medication.frequency;
    form.querySelector('#medication-notes').value = medication.notes || '';
    
    // Set times
    resetTimeSlots();
    let timeArray = [];
    try {
        timeArray = JSON.parse(medication.time);
    } catch (e) {
        console.error('Error parsing time:', e);
        timeArray = [];
    }
    
    if (timeArray.length > 0) {
        // Set first time in the default slot
        const firstTimeInput = document.querySelector('#time-slots-container input[type="time"]');
        if (firstTimeInput) {
            firstTimeInput.value = timeArray[0];
        }
        
        // Add additional time slots
        for (let i = 1; i < timeArray.length; i++) {
            addTimeSlot();
            const timeInputs = document.querySelectorAll('#time-slots-container input[type="time"]');
            timeInputs[i].value = timeArray[i];
        }
    }
    
    // Update modal title
    document.getElementById('medication-modal-title').textContent = 'Edit Medication';
    
    // Open modal
    const modal = new bootstrap.Modal(document.getElementById('add-medication-modal'));
    modal.show();
}

/**
 * Shows a confirmation dialog before deleting a medication
 * @param {Object} medication - The medication object to delete
 */
function confirmDeleteMedication(medication) {
    if (confirm(`Are you sure you want to delete "${medication.name}"?`)) {
        deleteMedication(medication.id);
    }
}

/**
 * Deletes a medication from the server
 * @param {number} id - The ID of the medication to delete
 */
function deleteMedication(id) {
    fetch(`/api/medications/${id}`, {
        method: 'DELETE'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to delete medication');
        }
        return response.json();
    })
    .then(data => {
        showAlert('Medication deleted successfully!', 'success');
        loadMedications();
    })
    .catch(error => {
        console.error('Error deleting medication:', error);
        showAlert('Failed to delete medication. Please try again.', 'danger');
    });
}

/**
 * Adds a new time slot input to the form
 */
function addTimeSlot() {
    const container = document.getElementById('time-slots-container');
    if (!container) return;
    
    const timeSlotCount = container.querySelectorAll('.time-slot').length;
    
    // Limit to 5 time slots
    if (timeSlotCount >= 5) {
        showAlert('Maximum of 5 time slots allowed', 'warning');
        return;
    }
    
    const timeSlot = document.createElement('div');
    timeSlot.className = 'time-slot input-group mb-2';
    
    timeSlot.innerHTML = `
        <input type="time" class="form-control" name="time[]">
        <button type="button" class="btn btn-outline-danger remove-time-btn">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(timeSlot);
    
    // Add event listener to remove button
    const removeBtn = timeSlot.querySelector('.remove-time-btn');
    removeBtn.addEventListener('click', function() {
        container.removeChild(timeSlot);
    });
}

/**
 * Resets the time slots to a single empty input
 */
function resetTimeSlots() {
    const container = document.getElementById('time-slots-container');
    if (!container) return;
    
    // Remove all time slots
    container.innerHTML = `
        <div class="time-slot input-group mb-2">
            <input type="time" class="form-control" name="time[]">
            <button type="button" class="btn btn-outline-secondary" id="add-time-btn">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    `;
    
    // Re-attach event listener to add button
    const addTimeBtn = document.getElementById('add-time-btn');
    if (addTimeBtn) {
        addTimeBtn.addEventListener('click', addTimeSlot);
    }
}
