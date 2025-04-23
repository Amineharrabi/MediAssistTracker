/**
 * MediAssist - Prescriptions Module
 * Handles prescription management functionality
 */

// Utility function to escape HTML special characters
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Utility function to format dates
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Initialize when the DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    // Load prescriptions
    loadPrescriptions();

    // Setup form event listeners
    const prescriptionForm = document.getElementById('prescription-form');
    if (prescriptionForm) {
        prescriptionForm.addEventListener('submit', handlePrescriptionFormSubmit);
    }

    // Setup image preview
    const prescriptionImage = document.getElementById('prescription-image');
    if (prescriptionImage) {
        prescriptionImage.addEventListener('change', previewImage);
    }
});

/**
 * Loads prescriptions from the server and displays them
 */
function loadPrescriptions() {
    const prescriptionsList = document.getElementById('prescriptions-list');
    if (!prescriptionsList) return;

    // Show loading state
    prescriptionsList.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

    fetch('/api/prescriptions')
        .then(response => response.json())
        .then(prescriptions => {
            // Sort prescriptions by date (newest first)
            prescriptions.sort((a, b) => {
                return new Date(b.date) - new Date(a.date);
            });

            displayPrescriptions(prescriptions);
        })
        .catch(error => {
            console.error('Error loading prescriptions:', error);
            prescriptionsList.innerHTML = '<div class="alert alert-danger">Failed to load prescriptions. Please try again.</div>';
        });
}

/**
 * Displays prescriptions in the UI
 * @param {Array} prescriptions - Array of prescription objects
 */
function displayPrescriptions(prescriptions) {
    const prescriptionsList = document.getElementById('prescriptions-list');
    if (!prescriptionsList) return;

    // Clear loading state
    prescriptionsList.innerHTML = '';

    if (prescriptions.length === 0) {
        prescriptionsList.innerHTML = `
            <div class="text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-file-medical fa-3x text-muted"></i>
                </div>
                <h5>No prescriptions added yet</h5>
                <p class="text-muted">Add your first prescription to get started</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-prescription-modal">
                    <i class="fas fa-plus"></i> Add Prescription
                </button>
            </div>
        `;
        return;
    }

    // Create row
    const row = document.createElement('div');
    row.className = 'row';

    // Add each prescription card
    prescriptions.forEach(prescription => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4 mb-4';

        col.innerHTML = `
            <div class="card prescription-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title">${prescription.title}</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item view-prescription" href="#" data-id="${prescription.id}"><i class="fas fa-eye me-2"></i>View</a></li>
                                <li><a class="dropdown-item edit-prescription" href="#" data-id="${prescription.id}"><i class="fas fa-pencil-alt me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item delete-prescription" href="#" data-id="${prescription.id}"><i class="fas fa-trash me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                    </div>
                    <p class="mb-2"><i class="far fa-calendar me-1"></i> ${formatDate(prescription.date)}</p>
                    ${prescription.doctor ? `<p class="mb-2"><i class="fas fa-user-md me-1"></i> ${prescription.doctor}</p>` : ''}
                    ${prescription.image_data ? `
                        <div class="text-center">
                            <img src="data:image/jpeg;base64,${prescription.image_data}" class="prescription-img" alt="Prescription">
                        </div>
                    ` : '<p class="text-muted"><i class="fas fa-image me-1"></i> No image uploaded</p>'}
                    ${prescription.notes ? `<p class="notes text-muted small mt-2">${prescription.notes}</p>` : ''}
                </div>
            </div>
        `;

        row.appendChild(col);

        // Add event listeners for view, edit and delete
        setTimeout(() => {
            const viewBtn = col.querySelector('.view-prescription');
            const editBtn = col.querySelector('.edit-prescription');
            const deleteBtn = col.querySelector('.delete-prescription');

            if (viewBtn) {
                viewBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    viewPrescription(prescription);
                });
            }

            if (editBtn) {
                editBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    openEditPrescriptionModal(prescription);
                });
            }

            if (deleteBtn) {
                deleteBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    confirmDeletePrescription(prescription);
                });
            }
        }, 0);
    });

    prescriptionsList.appendChild(row);

    // Add button to add new prescription
    const addBtnContainer = document.createElement('div');
    addBtnContainer.className = 'text-center mt-4';
    addBtnContainer.innerHTML = `
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-prescription-modal">
            <i class="fas fa-plus"></i> Add Prescription
        </button>
    `;

    prescriptionsList.appendChild(addBtnContainer);
}

/**
 * Handles the prescription form submission (add/edit)
 * @param {Event} event - The form submit event
 */
function handlePrescriptionFormSubmit(event) {
    event.preventDefault();

    const form = event.target;
    const prescriptionId = form.dataset.prescriptionId;
    const isEdit = !!prescriptionId;

    // Validate form
    const titleInput = form.querySelector('#prescription-title');
    const dateInput = form.querySelector('#prescription-date');

    const inputs = [titleInput, dateInput];
    let isValid = true;

    inputs.forEach(input => {
        if (!validateInput(input)) {
            isValid = false;
        }
    });

    if (!isValid) {
        return false;
    }

    // Show loading state
    const saveButton = form.querySelector('button[type="submit"]');
    const originalButtonText = saveButton.innerHTML;
    saveButton.disabled = true;
    saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

    // Create FormData for file upload
    const formData = new FormData(form);

    // Send request to server
    const url = isEdit ? `/index.php?route=prescription&action=update&id=${prescriptionId}` : '/index.php?route=prescription&action=create';
    const method = 'POST';

    fetch(url, {
        method: method,
        body: formData,
        credentials: 'include'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to save prescription');
            }
            return response.json();
        })
        .then(data => {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addPrescriptionModal'));
            modal.hide();

            // Show success message
            showAlert(
                isEdit ? 'Prescription updated successfully!' : 'New prescription added successfully!',
                'success'
            );

            // Reset form
            form.reset();
            form.removeAttribute('data-prescription-id');
            document.getElementById('prescription-modal-title').textContent = 'Add New Prescription';
            document.getElementById('image-preview-container').style.display = 'none';

            // Reload prescriptions list
            loadPrescriptions();
        })
        .catch(error => {
            console.error('Error saving prescription:', error);
            showAlert('Failed to save prescription. Please try again.', 'danger');
        })
        .finally(() => {
            // Reset button state
            saveButton.disabled = false;
            saveButton.innerHTML = originalButtonText;
        });
}

/**
 * Shows an alert message
 * @param {string} message - The message to display
 * @param {string} type - The alert type (success, danger, warning, info)
 */
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alert-container');
    if (!alertContainer) return;

    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.role = 'alert';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    alertContainer.appendChild(alert);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 150);
    }, 5000);
}

/**
 * Opens a modal to view prescription details
 * @param {Object} prescription - The prescription object to view
 */
function viewPrescription(prescription) {
    const viewModal = document.getElementById('view-prescription-modal');
    if (!viewModal) return;

    // Update modal content
    viewModal.querySelector('#view-prescription-title').textContent = prescription.title;
    viewModal.querySelector('#view-prescription-date').textContent = formatDate(prescription.date);

    const doctorElement = viewModal.querySelector('#view-prescription-doctor');
    doctorElement.textContent = prescription.doctor || 'Not specified';
    doctorElement.parentElement.style.display = prescription.doctor ? 'block' : 'none';

    const notesElement = viewModal.querySelector('#view-prescription-notes');
    notesElement.textContent = prescription.notes || 'No notes';
    notesElement.parentElement.style.display = prescription.notes ? 'block' : 'none';

    const imageContainer = viewModal.querySelector('#view-prescription-image');
    if (prescription.image_data) {
        imageContainer.innerHTML = `<img src="data:image/jpeg;base64,${prescription.image_data}" class="img-fluid" alt="Prescription">`;
        imageContainer.style.display = 'block';
    } else {
        imageContainer.style.display = 'none';
    }

    // Show modal
    const modal = new bootstrap.Modal(viewModal);
    modal.show();
}

/**
 * Opens the edit prescription modal with pre-filled data
 * @param {Object} prescription - The prescription object to edit
 */
function openEditPrescriptionModal(prescription) {
    const form = document.getElementById('prescription-form');
    if (!form) return;

    // Set form data
    form.dataset.prescriptionId = prescription.id;
    form.querySelector('#prescription-title').value = prescription.title;
    form.querySelector('#prescription-doctor').value = prescription.doctor || '';
    form.querySelector('#prescription-date').value = prescription.date;
    form.querySelector('#prescription-notes').value = prescription.notes || '';

    // Preview image if available
    const previewContainer = document.getElementById('image-preview-container');
    const preview = document.getElementById('image-preview');

    if (prescription.image_data && previewContainer && preview) {
        preview.src = `data:image/jpeg;base64,${prescription.image_data}`;
        previewContainer.style.display = 'block';
    } else if (previewContainer) {
        previewContainer.style.display = 'none';
    }

    // Update modal title
    document.getElementById('prescription-modal-title').textContent = 'Edit Prescription';

    // Open modal
    const modal = new bootstrap.Modal(document.getElementById('add-prescription-modal'));
    modal.show();
}

/**
 * Shows a confirmation dialog before deleting a prescription
 * @param {Object} prescription - The prescription object to delete
 */
function confirmDeletePrescription(prescription) {
    if (confirm(`Are you sure you want to delete "${prescription.title}"?`)) {
        deletePrescription(prescription.id);
    }
}

/**
 * Deletes a prescription from the server
 * @param {number} id - The ID of the prescription to delete
 */
function deletePrescription(id) {
    fetch(`/api/prescriptions/${id}`, {
        method: 'DELETE'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to delete prescription');
            }
            return response.json();
        })
        .then(data => {
            showAlert('Prescription deleted successfully!', 'success');
            loadPrescriptions();
        })
        .catch(error => {
            console.error('Error deleting prescription:', error);
            showAlert('Failed to delete prescription. Please try again.', 'danger');
        });
}

/**
 * Previews the selected image
 */
function previewImage() {
    const input = document.getElementById('prescription-image');
    const previewContainer = document.getElementById('image-preview-container');
    const preview = document.getElementById('image-preview');

    if (!input || !previewContainer || !preview) return;

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
    }
}
