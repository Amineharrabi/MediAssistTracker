<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="card-title">
                        <i class="fa-solid fa-file-waveform me-2"></i> Prescriptions
                    </h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPrescriptionModal">
                        <i class="fas fa-plus-circle me-1"></i> Add Prescription
                    </button>
                </div>
                <p class="card-text">Store and manage your prescription records securely.</p>
            </div>
        </div>
    </div>
</div>

<div class="row" id="prescriptions-container">
    <?php if (empty($prescriptions)): ?>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-file-medical fa-4x mb-3 text-muted"></i>
                    <h5 class="text-muted mb-3">No prescriptions found</h5>
                    <p class="mb-4">You haven't added any prescriptions yet.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPrescriptionModal">
                        <i class="fas fa-plus-circle me-1"></i> Add Your First Prescription
                    </button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($prescriptions as $prescription): ?>
            <div class="col-lg-4 col-md-6 mb-4 prescription-card" data-id="<?php echo $prescription['id']; ?>">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?php echo htmlspecialchars($prescription['title']); ?></h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item view-prescription" type="button" data-id="<?php echo $prescription['id']; ?>">
                                            <i class="fas fa-eye me-2"></i> View
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item edit-prescription" type="button" data-id="<?php echo $prescription['id']; ?>">
                                            <i class="fas fa-pencil-alt me-2"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item delete-prescription" type="button" data-id="<?php echo $prescription['id']; ?>">
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
                                <i class="fa-solid fa-calendar-days me-2 text-primary"></i>
                                <h6 class="mb-0">Date:</h6>
                            </div>
                            <p class="mb-0 ps-4"><?php echo format_date($prescription['date']); ?></p>
                        </div>

                        <?php if (!empty($prescription['doctor'])): ?>
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user-md me-2 text-primary"></i>
                                    <h6 class="mb-0">Doctor:</h6>
                                </div>
                                <p class="mb-0 ps-4"><?php echo htmlspecialchars($prescription['doctor']); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($prescription['notes'])): ?>
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-sticky-note me-2 text-primary"></i>
                                    <h6 class="mb-0">Notes:</h6>
                                </div>
                                <p class="mb-0 ps-4"><?php echo nl2br(htmlspecialchars($prescription['notes'])); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($prescription['image_data'])): ?>
                            <div class="mt-3 text-center">
                                <button class="btn btn-sm btn-outline-primary view-prescription" data-id="<?php echo $prescription['id']; ?>">
                                    <i class="fas fa-image me-1"></i> View Image
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-muted small">
                        <div class="d-flex justify-content-between">
                            <span><i class="fa-solid fa-calendar-days me-1"></i> Added: <?php echo format_date($prescription['created_at']); ?></span>
                            <?php if ($prescription['updated_at'] != $prescription['created_at']): ?>
                                <span><i class="fas fa-pencil-alt me-1"></i> Updated: <?php echo format_date($prescription['updated_at']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Add/Edit Prescription Modal -->
<div class="modal fade" id="addPrescriptionModal" tabindex="-1" aria-labelledby="addPrescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPrescriptionModalLabel">
                    <i class="fas fa-plus-circle me-2"></i> Add Prescription
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="prescription-form" enctype="multipart/form-data">
                    <input type="hidden" id="prescription-id" name="id">

                    <div class="mb-3">
                        <label for="prescription-title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="prescription-title" name="title" placeholder="e.g., Blood Pressure Medication" required>
                    </div>

                    <div class="mb-3">
                        <label for="prescription-date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="prescription-date" name="date" required>
                    </div>

                    <div class="mb-3">
                        <label for="prescription-doctor" class="form-label">Doctor</label>
                        <input type="text" class="form-control" id="prescription-doctor" name="doctor" placeholder="e.g., Dr. Smith">
                    </div>

                    <div class="mb-3">
                        <label for="prescription-image" class="form-label">Prescription Image</label>
                        <input type="file" class="form-control" id="prescription-image" name="image" accept="image/*">
                        <div class="form-text">Upload an image of your prescription (optional)</div>

                        <div id="image-preview-container" class="mt-2 text-center" style="display: none;">
                            <img id="image-preview" class="img-thumbnail" style="max-height: 200px;" alt="Prescription preview">
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" id="remove-image">
                                    <i class="fas fa-times"></i> Remove Image
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="prescription-notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="prescription-notes" name="notes" rows="3" placeholder="Additional notes or instructions"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-prescription">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- View Prescription Modal -->
<div class="modal fade" id="viewPrescriptionModal" tabindex="-1" aria-labelledby="viewPrescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPrescriptionModalLabel">
                    <i class="fa-solid fa-file-waveform me-2"></i> <span id="view-prescription-title"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fa-solid fa-calendar-days fa-fw me-2 text-primary"></i>
                            <h6 class="mb-0">Date:</h6>
                        </div>
                        <p class="ms-4 mb-0" id="view-prescription-date"></p>
                    </div>
                    <div class="col-md-6" id="view-doctor-container">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-user-md fa-fw me-2 text-primary"></i>
                            <h6 class="mb-0">Doctor:</h6>
                        </div>
                        <p class="ms-4 mb-0" id="view-prescription-doctor"></p>
                    </div>
                </div>

                <div id="view-notes-container" class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-sticky-note fa-fw me-2 text-primary"></i>
                        <h6 class="mb-0">Notes:</h6>
                    </div>
                    <p class="ms-4 mb-0" id="view-prescription-notes"></p>
                </div>

                <div id="view-image-container" class="text-center mb-2">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-image fa-fw me-2 text-primary"></i>
                        <h6 class="mb-0">Prescription Image:</h6>
                    </div>
                    <img id="view-prescription-image" class="img-fluid img-thumbnail" style="max-height: 400px;" alt="Prescription image">
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
<div class="modal fade" id="deletePrescriptionModal" tabindex="-1" aria-labelledby="deletePrescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePrescriptionModalLabel">
                    <i class="fas fa-trash-alt me-2"></i> Delete Prescription
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the prescription "<span id="delete-prescription-title"></span>"?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-prescription">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load prescriptions on page load
        loadPrescriptions();

        // Save prescription button
        document.getElementById('save-prescription').addEventListener('click', function() {
            handlePrescriptionFormSubmit();
        });

        // Preview uploaded image
        document.getElementById('prescription-image').addEventListener('change', function(event) {
            previewImage(event);
        });

        // Remove image button
        document.getElementById('remove-image').addEventListener('click', function() {
            document.getElementById('prescription-image').value = '';
            document.getElementById('image-preview-container').style.display = 'none';
        });

        // Edit from view button
        document.getElementById('edit-from-view').addEventListener('click', function() {
            const prescriptionId = this.getAttribute('data-id');
            bootstrap.Modal.getInstance(document.getElementById('viewPrescriptionModal')).hide();
            openEditPrescriptionModal(prescriptionId);
        });

        // Setup event delegation for prescription actions
        document.getElementById('prescriptions-container').addEventListener('click', function(event) {
            // View prescription
            if (event.target.classList.contains('view-prescription') ||
                event.target.closest('.view-prescription')) {

                const button = event.target.classList.contains('view-prescription') ?
                    event.target : event.target.closest('.view-prescription');

                const prescriptionId = button.getAttribute('data-id');
                viewPrescription(prescriptionId);
            }

            // Edit prescription
            if (event.target.classList.contains('edit-prescription') ||
                event.target.closest('.edit-prescription')) {

                const button = event.target.classList.contains('edit-prescription') ?
                    event.target : event.target.closest('.edit-prescription');

                const prescriptionId = button.getAttribute('data-id');
                openEditPrescriptionModal(prescriptionId);
            }

            // Delete prescription
            if (event.target.classList.contains('delete-prescription') ||
                event.target.closest('.delete-prescription')) {

                const button = event.target.classList.contains('delete-prescription') ?
                    event.target : event.target.closest('.delete-prescription');

                const prescriptionId = button.getAttribute('data-id');
                confirmDeletePrescription(prescriptionId);
            }
        });

        // Confirm delete prescription button
        document.getElementById('confirm-delete-prescription').addEventListener('click', function() {
            const prescriptionId = this.getAttribute('data-id');
            deletePrescription(prescriptionId);
        });

        // When modal is closed, reset the form
        document.getElementById('addPrescriptionModal').addEventListener('hidden.bs.modal', function() {
            resetPrescriptionForm();
        });
    });

    /**
     * Loads prescriptions from the server
     */
    function loadPrescriptions() {
        fetch('/api.php?endpoint=prescriptions')
            .then(response => response.json())
            .then(prescriptions => {
                displayPrescriptions(prescriptions);
            })
            .catch(error => {
                console.error('Error fetching prescriptions:', error);
                showAlert('Failed to load prescriptions.', 'danger');
            });
    }

    /**
     * Displays prescriptions in the UI
     * @param {Array} prescriptions - Array of prescription objects
     */
    function displayPrescriptions(prescriptions) {
        const container = document.getElementById('prescriptions-container');

        if (!prescriptions || prescriptions.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-file-medical fa-4x mb-3 text-muted"></i>
                            <h5 class="text-muted mb-3">No prescriptions found</h5>
                            <p class="mb-4">You haven't added any prescriptions yet.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPrescriptionModal">
                                <i class="fas fa-plus-circle me-1"></i> Add Your First Prescription
                            </button>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        let html = '';

        prescriptions.forEach(prescription => {
            html += `
                <div class="col-lg-4 col-md-6 mb-4 prescription-card" data-id="${prescription.id}">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">${escapeHtml(prescription.title)}</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item view-prescription" type="button" data-id="${prescription.id}">
                                                <i class="fas fa-eye me-2"></i> View
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item edit-prescription" type="button" data-id="${prescription.id}">
                                                <i class="fas fa-pencil-alt me-2"></i> Edit
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item delete-prescription" type="button" data-id="${prescription.id}">
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
                                    <i class="fa-solid fa-calendar-days me-2 text-primary"></i>
                                    <h6 class="mb-0">Date:</h6>
                                </div>
                                <p class="mb-0 ps-4">${formatDate(prescription.date)}</p>
                            </div>
                            
                            ${prescription.doctor ? `
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user-md me-2 text-primary"></i>
                                        <h6 class="mb-0">Doctor:</h6>
                                    </div>
                                    <p class="mb-0 ps-4">${escapeHtml(prescription.doctor)}</p>
                                </div>
                            ` : ''}
                            
                            ${prescription.notes ? `
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-sticky-note me-2 text-primary"></i>
                                        <h6 class="mb-0">Notes:</h6>
                                    </div>
                                    <p class="mb-0 ps-4">${escapeHtml(prescription.notes).replace(/\n/g, '<br>')}</p>
                                </div>
                            ` : ''}
                            
                            ${prescription.image_data ? `
                                <div class="mt-3 text-center">
                                    <button class="btn btn-sm btn-outline-primary view-prescription" data-id="${prescription.id}">
                                        <i class="fas fa-image me-1"></i> View Image
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                        <div class="card-footer bg-transparent border-0 text-muted small">
                            <div class="d-flex justify-content-between">
                                <span><i class="fa-solid fa-calendar-days me-1"></i> Added: ${formatDate(prescription.created_at)}</span>
                                ${prescription.updated_at !== prescription.created_at ? 
                                    `<span><i class="fas fa-pencil-alt me-1"></i> Updated: ${formatDate(prescription.updated_at)}</span>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    /**
     * Resets the prescription form
     */
    function resetPrescriptionForm() {
        const form = document.getElementById('prescription-form');
        form.reset();
        document.getElementById('prescription-id').value = '';

        // Reset image preview
        document.getElementById('image-preview-container').style.display = 'none';

        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('prescription-date').value = today;

        // Update modal title
        document.getElementById('addPrescriptionModalLabel').innerHTML = '<i class="fas fa-plus-circle me-2"></i> Add Prescription';
    }

    /**
     * Views a prescription's details
     * @param {string} prescriptionId - The ID of the prescription to view
     */
    function viewPrescription(prescriptionId) {
        fetch(`/api.php?endpoint=prescription&id=${prescriptionId}`)
            .then(response => response.json())
            .then(prescription => {
                document.getElementById('view-prescription-title').textContent = prescription.title;
                document.getElementById('view-prescription-date').textContent = formatDate(prescription.date);

                // Doctor (optional)
                const doctorContainer = document.getElementById('view-doctor-container');
                if (prescription.doctor) {
                    document.getElementById('view-prescription-doctor').textContent = prescription.doctor;
                    doctorContainer.style.display = 'block';
                } else {
                    doctorContainer.style.display = 'none';
                }

                // Notes (optional)
                const notesContainer = document.getElementById('view-notes-container');
                if (prescription.notes) {
                    document.getElementById('view-prescription-notes').innerHTML = escapeHtml(prescription.notes).replace(/\n/g, '<br>');
                    notesContainer.style.display = 'block';
                } else {
                    notesContainer.style.display = 'none';
                }

                // Image (optional)
                const imageContainer = document.getElementById('view-image-container');
                if (prescription.image_data) {
                    document.getElementById('view-prescription-image').src = 'data:image/jpeg;base64,' + prescription.image_data;
                    imageContainer.style.display = 'block';
                } else {
                    imageContainer.style.display = 'none';
                }

                // Set prescription ID for edit button
                document.getElementById('edit-from-view').setAttribute('data-id', prescription.id);

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('viewPrescriptionModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching prescription:', error);
                showAlert('Failed to fetch prescription details.', 'danger');
            });
    }

    /**
     * Opens the edit prescription modal with pre-filled data
     * @param {string} prescriptionId - The ID of the prescription to edit
     */
    function openEditPrescriptionModal(prescriptionId) {
        resetPrescriptionForm();

        // Fetch prescription data
        fetch(`/api.php?endpoint=prescription&id=${prescriptionId}`)
            .then(response => response.json())
            .then(prescription => {
                document.getElementById('prescription-id').value = prescription.id;
                document.getElementById('prescription-title').value = prescription.title;
                document.getElementById('prescription-date').value = prescription.date;
                document.getElementById('prescription-doctor').value = prescription.doctor || '';
                document.getElementById('prescription-notes').value = prescription.notes || '';

                // Set image preview if available
                if (prescription.image_data) {
                    document.getElementById('image-preview').src = 'data:image/jpeg;base64,' + prescription.image_data;
                    document.getElementById('image-preview-container').style.display = 'block';
                }

                // Update modal title
                document.getElementById('addPrescriptionModalLabel').innerHTML = '<i class="fas fa-pencil-alt me-2"></i> Edit Prescription';

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('addPrescriptionModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching prescription:', error);
                showAlert('Failed to fetch prescription details.', 'danger');
            });
    }

    /**
     * Handles the prescription form submission
     */
    function handlePrescriptionFormSubmit() {
        const form = document.getElementById('prescription-form');

        // Check form validity
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Create FormData object
        const formData = new FormData(form);

        // Get prescription ID (if editing)
        const prescriptionId = document.getElementById('prescription-id').value;

        // Determine if this is an add or update operation
        const isUpdate = !!prescriptionId;
        const url = isUpdate ? `/api.php?endpoint=prescription&id=${prescriptionId}` : '/api.php?endpoint=prescriptions';

        // Check if file input is empty and update is being performed
        if (isUpdate && document.getElementById('prescription-image').files.length === 0) {
            // If updating and no new image, remove the image field from the form
            // so the existing image is preserved
            formData.delete('image');
        }

        // Send request
        fetch(url, {
                method: 'POST', // Always use POST for form with file upload
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('addPrescriptionModal')).hide();

                    // Show success message
                    showAlert(
                        isUpdate ? 'Prescription updated successfully!' : 'Prescription added successfully!',
                        'success'
                    );

                    // Reload prescriptions
                    loadPrescriptions();
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
     * Shows a confirmation dialog before deleting a prescription
     * @param {string} prescriptionId - The ID of the prescription to delete
     */
    function confirmDeletePrescription(prescriptionId) {
        // Fetch prescription data to display title
        fetch(`/api.php?endpoint=prescription&id=${prescriptionId}`)
            .then(response => response.json())
            .then(prescription => {
                document.getElementById('delete-prescription-title').textContent = prescription.title;
                document.getElementById('confirm-delete-prescription').setAttribute('data-id', prescriptionId);

                const modal = new bootstrap.Modal(document.getElementById('deletePrescriptionModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching prescription:', error);
                showAlert('Failed to fetch prescription details.', 'danger');
            });
    }

    /**
     * Deletes a prescription
     * @param {string} prescriptionId - The ID of the prescription to delete
     */
    function deletePrescription(prescriptionId) {
        fetch(`/api.php?endpoint=prescription&id=${prescriptionId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('deletePrescriptionModal')).hide();

                    // Show success message
                    showAlert('Prescription deleted successfully!', 'success');

                    // Reload prescriptions
                    loadPrescriptions();
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
     * Previews the selected image
     * @param {Event} event - The change event of the file input
     */
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview-container').style.display = 'block';
            };

            reader.readAsDataURL(file);
        }
    }
</script>