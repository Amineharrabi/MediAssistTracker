/**
 * MediAssist - Emergency Contacts Module
 * Handles emergency contact management functionality
 */

// Initialize when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load contacts
    loadContacts();
    
    // Setup form event listeners
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactFormSubmit);
    }
});

/**
 * Loads contacts from the server and displays them
 */
function loadContacts() {
    const contactsList = document.getElementById('contacts-list');
    if (!contactsList) return;
    
    // Show loading state
    contactsList.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    fetch('/api/contacts')
        .then(response => response.json())
        .then(contacts => {
            // Sort contacts alphabetically by name
            contacts.sort((a, b) => a.name.localeCompare(b.name));
            displayContacts(contacts);
        })
        .catch(error => {
            console.error('Error loading contacts:', error);
            contactsList.innerHTML = '<div class="alert alert-danger">Failed to load emergency contacts. Please try again.</div>';
        });
}

/**
 * Displays contacts in the UI
 * @param {Array} contacts - Array of contact objects
 */
function displayContacts(contacts) {
    const contactsList = document.getElementById('contacts-list');
    if (!contactsList) return;
    
    // Clear loading state
    contactsList.innerHTML = '';
    
    if (contacts.length === 0) {
        contactsList.innerHTML = `
            <div class="text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-address-book fa-3x text-muted"></i>
                </div>
                <h5>No emergency contacts added yet</h5>
                <p class="text-muted">Add your first emergency contact to get started</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
                    <i class="fas fa-plus"></i> Add Emergency Contact
                </button>
            </div>
        `;
        return;
    }
    
    // Create row
    const row = document.createElement('div');
    row.className = 'row';
    
    // Add each contact card
    contacts.forEach(contact => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4 mb-4';
        
        col.innerHTML = `
            <div class="card contact-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title">${contact.name}</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item call-contact" href="tel:${contact.phone}"><i class="fas fa-phone me-2"></i>Call</a></li>
                                ${contact.email ? `<li><a class="dropdown-item email-contact" href="mailto:${contact.email}"><i class="fas fa-envelope me-2"></i>Email</a></li>` : ''}
                                <li><a class="dropdown-item edit-contact" href="#" data-id="${contact.id}"><i class="fas fa-pencil-alt me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item delete-contact" href="#" data-id="${contact.id}"><i class="fas fa-trash me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                    </div>
                    ${contact.relationship ? `<p class="relationship mb-2">${contact.relationship}</p>` : ''}
                    <p class="phone mb-2"><i class="fas fa-phone me-2"></i>${contact.phone}</p>
                    ${contact.email ? `<p class="email mb-2"><i class="fas fa-envelope me-2"></i>${contact.email}</p>` : ''}
                    ${contact.notes ? `<p class="notes text-muted small mt-2">${contact.notes}</p>` : ''}
                    <div class="mt-3">
                        <a href="tel:${contact.phone}" class="btn btn-success btn-sm me-1">
                            <i class="fas fa-phone"></i> Call
                        </a>
                        ${contact.email ? `
                            <a href="mailto:${contact.email}" class="btn btn-primary btn-sm">
                                <i class="fas fa-envelope"></i> Email
                            </a>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        
        row.appendChild(col);
        
        // Add event listeners for edit and delete
        setTimeout(() => {
            const editBtn = col.querySelector('.edit-contact');
            const deleteBtn = col.querySelector('.delete-contact');
            
            if (editBtn) {
                editBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    openEditContactModal(contact);
                });
            }
            
            if (deleteBtn) {
                deleteBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    confirmDeleteContact(contact);
                });
            }
        }, 0);
    });
    
    contactsList.appendChild(row);
    
    // Add button to add new contact
    const addBtnContainer = document.createElement('div');
    addBtnContainer.className = 'text-center mt-4';
    addBtnContainer.innerHTML = `
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
            <i class="fas fa-plus"></i> Add Emergency Contact
        </button>
    `;
    
    contactsList.appendChild(addBtnContainer);
}

/**
 * Handles the contact form submission (add/edit)
 * @param {Event} event - The form submit event
 */
function handleContactFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const contactId = form.dataset.contactId;
    const isEdit = !!contactId;
    
    // Validate form
    const nameInput = form.querySelector('#contact-name');
    const phoneInput = form.querySelector('#contact-phone');
    
    const inputs = [nameInput, phoneInput];
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
    const contactData = {
        name: nameInput.value.trim(),
        relationship: form.querySelector('#contact-relationship').value.trim(),
        phone: phoneInput.value.trim(),
        email: form.querySelector('#contact-email').value.trim(),
        notes: form.querySelector('#contact-notes').value.trim()
    };
    
    // Send request to server
    const url = isEdit ? `/api/contacts/${contactId}` : '/api/contacts';
    const method = isEdit ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(contactData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to save contact');
        }
        return response.json();
    })
    .then(data => {
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('add-contact-modal'));
        modal.hide();
        
        // Show success message
        showAlert(
            isEdit ? 'Contact updated successfully!' : 'New emergency contact added successfully!',
            'success'
        );
        
        // Reload contacts
        loadContacts();
        
        // Reset form
        form.reset();
        form.removeAttribute('data-contact-id');
        document.getElementById('contact-modal-title').textContent = 'Add Emergency Contact';
    })
    .catch(error => {
        console.error('Error saving contact:', error);
        showAlert('Failed to save contact. Please try again.', 'danger');
    });
}

/**
 * Opens the edit contact modal with pre-filled data
 * @param {Object} contact - The contact object to edit
 */
function openEditContactModal(contact) {
    const form = document.getElementById('contact-form');
    if (!form) return;
    
    // Set form data
    form.dataset.contactId = contact.id;
    form.querySelector('#contact-name').value = contact.name;
    form.querySelector('#contact-relationship').value = contact.relationship || '';
    form.querySelector('#contact-phone').value = contact.phone;
    form.querySelector('#contact-email').value = contact.email || '';
    form.querySelector('#contact-notes').value = contact.notes || '';
    
    // Update modal title
    document.getElementById('contact-modal-title').textContent = 'Edit Emergency Contact';
    
    // Open modal
    const modal = new bootstrap.Modal(document.getElementById('add-contact-modal'));
    modal.show();
}

/**
 * Shows a confirmation dialog before deleting a contact
 * @param {Object} contact - The contact object to delete
 */
function confirmDeleteContact(contact) {
    if (confirm(`Are you sure you want to delete "${contact.name}" from your emergency contacts?`)) {
        deleteContact(contact.id);
    }
}

/**
 * Deletes a contact from the server
 * @param {number} id - The ID of the contact to delete
 */
function deleteContact(id) {
    fetch(`/api/contacts/${id}`, {
        method: 'DELETE'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to delete contact');
        }
        return response.json();
    })
    .then(data => {
        showAlert('Emergency contact deleted successfully!', 'success');
        loadContacts();
    })
    .catch(error => {
        console.error('Error deleting contact:', error);
        showAlert('Failed to delete contact. Please try again.', 'danger');
    });
}
