<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="card-title">
                        <i class="fas fa-address-book me-2"></i> Emergency Contacts
                    </h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
                        <i class="fas fa-plus-circle me-1"></i> Add Contact
                    </button>
                </div>
                <p class="card-text">Manage your emergency contacts for quick access during emergencies.</p>
            </div>
        </div>
    </div>
</div>

<div class="row" id="contacts-container">
    <?php if (empty($contacts)): ?>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-friends fa-4x mb-3 text-muted"></i>
                    <h5 class="text-muted mb-3">No emergency contacts found</h5>
                    <p class="mb-4">You haven't added any emergency contacts yet.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
                        <i class="fas fa-plus-circle me-1"></i> Add Your First Contact
                    </button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($contacts as $contact): ?>
            <div class="col-lg-4 col-md-6 mb-4 contact-card" data-id="<?php echo $contact['id']; ?>">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0"><?php echo htmlspecialchars($contact['name']); ?></h5>
                                <?php if (!empty($contact['relationship'])): ?>
                                    <span class="badge bg-light text-dark"><?php echo htmlspecialchars($contact['relationship']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item edit-contact" type="button" data-id="<?php echo $contact['id']; ?>">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item delete-contact" type="button" data-id="<?php echo $contact['id']; ?>">
                                            <i class="fas fa-trash-alt me-2"></i> Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="contact-avatar bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                <?php echo strtoupper(substr($contact['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($contact['name']); ?></h6>
                                <?php if (!empty($contact['relationship'])): ?>
                                    <small class="text-muted"><?php echo htmlspecialchars($contact['relationship']); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                <h6 class="mb-0">Phone:</h6>
                            </div>
                            <div class="ps-4 d-flex align-items-center">
                                <p class="mb-0"><?php echo htmlspecialchars($contact['phone']); ?></p>
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $contact['phone']); ?>" class="btn btn-sm btn-outline-primary ms-auto">
                                    <i class="fas fa-phone-alt"></i>
                                </a>
                            </div>
                        </div>
                        
                        <?php if (!empty($contact['email'])): ?>
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    <h6 class="mb-0">Email:</h6>
                                </div>
                                <div class="ps-4 d-flex align-items-center">
                                    <p class="mb-0"><?php echo htmlspecialchars($contact['email']); ?></p>
                                    <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>" class="btn btn-sm btn-outline-primary ms-auto">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($contact['notes'])): ?>
                            <div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-sticky-note me-2 text-primary"></i>
                                    <h6 class="mb-0">Notes:</h6>
                                </div>
                                <p class="mb-0 ps-4"><?php echo nl2br(htmlspecialchars($contact['notes'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-muted small">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-calendar-alt me-1"></i> Added: <?php echo format_date($contact['created_at']); ?></span>
                            <?php if ($contact['updated_at'] != $contact['created_at']): ?>
                                <span><i class="fas fa-edit me-1"></i> Updated: <?php echo format_date($contact['updated_at']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Add/Edit Contact Modal -->
<div class="modal fade" id="addContactModal" tabindex="-1" aria-labelledby="addContactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addContactModalLabel">
                    <i class="fas fa-plus-circle me-2"></i> Add Emergency Contact
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="contact-form">
                    <input type="hidden" id="contact-id">
                    
                    <div class="mb-3">
                        <label for="contact-name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contact-name" placeholder="Enter full name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact-relationship" class="form-label">Relationship</label>
                        <select class="form-select" id="contact-relationship">
                            <option value="">Select relationship...</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Parent">Parent</option>
                            <option value="Child">Child</option>
                            <option value="Sibling">Sibling</option>
                            <option value="Relative">Relative</option>
                            <option value="Friend">Friend</option>
                            <option value="Neighbor">Neighbor</option>
                            <option value="Doctor">Doctor</option>
                            <option value="Caregiver">Caregiver</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact-phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="contact-phone" placeholder="Enter phone number" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="contact-email" placeholder="Enter email address">
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact-notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="contact-notes" rows="3" placeholder="Additional notes or instructions"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-contact">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteContactModal" tabindex="-1" aria-labelledby="deleteContactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteContactModalLabel">
                    <i class="fas fa-trash-alt me-2"></i> Delete Contact
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<span id="delete-contact-name"></span>" from your emergency contacts?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-contact">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load contacts on page load
        loadContacts();
        
        // Save contact button
        document.getElementById('save-contact').addEventListener('click', function() {
            handleContactFormSubmit();
        });
        
        // Setup event delegation for contact actions
        document.getElementById('contacts-container').addEventListener('click', function(event) {
            // Edit contact
            if (event.target.classList.contains('edit-contact') || 
                event.target.closest('.edit-contact')) {
                
                const button = event.target.classList.contains('edit-contact') ? 
                               event.target : event.target.closest('.edit-contact');
                
                const contactId = button.getAttribute('data-id');
                openEditContactModal(contactId);
            }
            
            // Delete contact
            if (event.target.classList.contains('delete-contact') || 
                event.target.closest('.delete-contact')) {
                
                const button = event.target.classList.contains('delete-contact') ? 
                               event.target : event.target.closest('.delete-contact');
                
                const contactId = button.getAttribute('data-id');
                confirmDeleteContact(contactId);
            }
        });
        
        // Confirm delete contact button
        document.getElementById('confirm-delete-contact').addEventListener('click', function() {
            const contactId = this.getAttribute('data-id');
            deleteContact(contactId);
        });
        
        // When modal is closed, reset the form
        document.getElementById('addContactModal').addEventListener('hidden.bs.modal', function() {
            resetContactForm();
        });
    });
    
    /**
     * Loads contacts from the server
     */
    function loadContacts() {
        fetch('/api.php?endpoint=contacts')
            .then(response => response.json())
            .then(contacts => {
                displayContacts(contacts);
            })
            .catch(error => {
                console.error('Error fetching contacts:', error);
                showAlert('Failed to load contacts.', 'danger');
            });
    }
    
    /**
     * Displays contacts in the UI
     * @param {Array} contacts - Array of contact objects
     */
    function displayContacts(contacts) {
        const container = document.getElementById('contacts-container');
        
        if (!contacts || contacts.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-user-friends fa-4x mb-3 text-muted"></i>
                            <h5 class="text-muted mb-3">No emergency contacts found</h5>
                            <p class="mb-4">You haven't added any emergency contacts yet.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
                                <i class="fas fa-plus-circle me-1"></i> Add Your First Contact
                            </button>
                        </div>
                    </div>
                </div>
            `;
            return;
        }
        
        let html = '';
        
        contacts.forEach(contact => {
            const initial = contact.name.charAt(0).toUpperCase();
            const phoneFormatted = contact.phone.replace(/[^0-9+]/g, '');
            
            html += `
                <div class="col-lg-4 col-md-6 mb-4 contact-card" data-id="${contact.id}">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">${escapeHtml(contact.name)}</h5>
                                    ${contact.relationship ? `
                                        <span class="badge bg-light text-dark">${escapeHtml(contact.relationship)}</span>
                                    ` : ''}
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item edit-contact" type="button" data-id="${contact.id}">
                                                <i class="fas fa-edit me-2"></i> Edit
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item delete-contact" type="button" data-id="${contact.id}">
                                                <i class="fas fa-trash-alt me-2"></i> Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="contact-avatar bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                    ${initial}
                                </div>
                                <div>
                                    <h6 class="mb-0">${escapeHtml(contact.name)}</h6>
                                    ${contact.relationship ? `
                                        <small class="text-muted">${escapeHtml(contact.relationship)}</small>
                                    ` : ''}
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-phone me-2 text-primary"></i>
                                    <h6 class="mb-0">Phone:</h6>
                                </div>
                                <div class="ps-4 d-flex align-items-center">
                                    <p class="mb-0">${escapeHtml(contact.phone)}</p>
                                    <a href="tel:${phoneFormatted}" class="btn btn-sm btn-outline-primary ms-auto">
                                        <i class="fas fa-phone-alt"></i>
                                    </a>
                                </div>
                            </div>
                            
                            ${contact.email ? `
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-envelope me-2 text-primary"></i>
                                        <h6 class="mb-0">Email:</h6>
                                    </div>
                                    <div class="ps-4 d-flex align-items-center">
                                        <p class="mb-0">${escapeHtml(contact.email)}</p>
                                        <a href="mailto:${escapeHtml(contact.email)}" class="btn btn-sm btn-outline-primary ms-auto">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </div>
                                </div>
                            ` : ''}
                            
                            ${contact.notes ? `
                                <div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-sticky-note me-2 text-primary"></i>
                                        <h6 class="mb-0">Notes:</h6>
                                    </div>
                                    <p class="mb-0 ps-4">${escapeHtml(contact.notes).replace(/\n/g, '<br>')}</p>
                                </div>
                            ` : ''}
                        </div>
                        <div class="card-footer bg-transparent border-0 text-muted small">
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-calendar-alt me-1"></i> Added: ${formatDate(contact.created_at)}</span>
                                ${contact.updated_at !== contact.created_at ? 
                                    `<span><i class="fas fa-edit me-1"></i> Updated: ${formatDate(contact.updated_at)}</span>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    /**
     * Resets the contact form
     */
    function resetContactForm() {
        const form = document.getElementById('contact-form');
        form.reset();
        document.getElementById('contact-id').value = '';
        
        // Update modal title
        document.getElementById('addContactModalLabel').innerHTML = '<i class="fas fa-plus-circle me-2"></i> Add Emergency Contact';
    }
    
    /**
     * Opens the edit contact modal with pre-filled data
     * @param {string} contactId - The ID of the contact to edit
     */
    function openEditContactModal(contactId) {
        resetContactForm();
        
        // Fetch contact data
        fetch(`/api.php?endpoint=contact&id=${contactId}`)
            .then(response => response.json())
            .then(contact => {
                document.getElementById('contact-id').value = contact.id;
                document.getElementById('contact-name').value = contact.name;
                document.getElementById('contact-relationship').value = contact.relationship || '';
                document.getElementById('contact-phone').value = contact.phone;
                document.getElementById('contact-email').value = contact.email || '';
                document.getElementById('contact-notes').value = contact.notes || '';
                
                // Update modal title
                document.getElementById('addContactModalLabel').innerHTML = '<i class="fas fa-edit me-2"></i> Edit Contact';
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('addContactModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching contact:', error);
                showAlert('Failed to fetch contact details.', 'danger');
            });
    }
    
    /**
     * Handles the contact form submission
     */
    function handleContactFormSubmit() {
        const form = document.getElementById('contact-form');
        
        // Check form validity
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Get form values
        const contactId = document.getElementById('contact-id').value;
        const name = document.getElementById('contact-name').value;
        const relationship = document.getElementById('contact-relationship').value;
        const phone = document.getElementById('contact-phone').value;
        const email = document.getElementById('contact-email').value;
        const notes = document.getElementById('contact-notes').value;
        
        // Prepare data
        const data = {
            name,
            relationship,
            phone,
            email,
            notes
        };
        
        // Determine if this is an add or update operation
        const isUpdate = !!contactId;
        const method = isUpdate ? 'PUT' : 'POST';
        const url = isUpdate ? `/api.php?endpoint=contact&id=${contactId}` : '/api.php?endpoint=contacts';
        
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
                bootstrap.Modal.getInstance(document.getElementById('addContactModal')).hide();
                
                // Show success message
                showAlert(
                    isUpdate ? 'Contact updated successfully!' : 'Contact added successfully!', 
                    'success'
                );
                
                // Reload contacts
                loadContacts();
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
     * Shows a confirmation dialog before deleting a contact
     * @param {string} contactId - The ID of the contact to delete
     */
    function confirmDeleteContact(contactId) {
        // Fetch contact data to display name
        fetch(`/api.php?endpoint=contact&id=${contactId}`)
            .then(response => response.json())
            .then(contact => {
                document.getElementById('delete-contact-name').textContent = contact.name;
                document.getElementById('confirm-delete-contact').setAttribute('data-id', contactId);
                
                const modal = new bootstrap.Modal(document.getElementById('deleteContactModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching contact:', error);
                showAlert('Failed to fetch contact details.', 'danger');
            });
    }
    
    /**
     * Deletes a contact
     * @param {string} contactId - The ID of the contact to delete
     */
    function deleteContact(contactId) {
        fetch(`/api.php?endpoint=contact&id=${contactId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('deleteContactModal')).hide();
                
                // Show success message
                showAlert('Contact deleted successfully!', 'success');
                
                // Reload contacts
                loadContacts();
            } else {
                showAlert(result.error || 'An error occurred.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred. Please try again.', 'danger');
        });
    }
</script>