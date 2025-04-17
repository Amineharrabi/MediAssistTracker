<?php
/**
 * API Endpoints for AJAX Requests
 */

// Load configuration
require_once dirname(__DIR__) . '/config/config.php';

// Load model classes
require_once INCLUDES_PATH . '/models/User.php';
require_once INCLUDES_PATH . '/models/Medication.php';
require_once INCLUDES_PATH . '/models/Appointment.php';
require_once INCLUDES_PATH . '/models/Prescription.php';
require_once INCLUDES_PATH . '/models/EmergencyContact.php';
require_once INCLUDES_PATH . '/models/Notification.php';

// Initialize models
$userModel = new User($db);
$medicationModel = new Medication($db);
$appointmentModel = new Appointment($db);
$prescriptionModel = new Prescription($db);
$contactModel = new EmergencyContact($db);
$notificationModel = new Notification($db);

// Set content type to JSON
header('Content-Type: application/json');

// Check login status
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get user ID
$user_id = get_user_id();

// Get the API endpoint
$endpoint = $_GET['endpoint'] ?? '';

// Process API request
switch ($endpoint) {
    // Medications API
    case 'medications':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get all medications
            $medications = $medicationModel->getAll($user_id);
            echo json_encode($medications ?: []);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Create new medication
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
                exit;
            }
            
            $name = sanitize($data['name'] ?? '');
            $dosage = sanitize($data['dosage'] ?? '');
            $frequency = sanitize($data['frequency'] ?? '');
            $times = array_map('sanitize', $data['times'] ?? []);
            $notes = sanitize($data['notes'] ?? '');
            
            if (empty($name) || empty($dosage) || empty($frequency) || empty($times)) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }
            
            $medication_id = $medicationModel->create($name, $dosage, $frequency, $times, $notes, $user_id);
            
            if ($medication_id) {
                $medication = $medicationModel->getById($medication_id, $user_id);
                
                // Create notification for medication
                $notificationModel->createMedicationNotification($medication, $user_id);
                
                echo json_encode(['success' => true, 'medication' => $medication]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create medication']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case 'medication':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing medication ID']);
            exit;
        }
        
        $medication_id = (int)$_GET['id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get medication by ID
            $medication = $medicationModel->getById($medication_id, $user_id);
            
            if ($medication) {
                echo json_encode($medication);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Medication not found']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            // Update medication
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
                exit;
            }
            
            $name = sanitize($data['name'] ?? '');
            $dosage = sanitize($data['dosage'] ?? '');
            $frequency = sanitize($data['frequency'] ?? '');
            $times = array_map('sanitize', $data['times'] ?? []);
            $notes = sanitize($data['notes'] ?? '');
            
            if (empty($name) || empty($dosage) || empty($frequency) || empty($times)) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }
            
            $result = $medicationModel->update($medication_id, $name, $dosage, $frequency, $times, $notes, $user_id);
            
            if ($result) {
                $medication = $medicationModel->getById($medication_id, $user_id);
                echo json_encode(['success' => true, 'medication' => $medication]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update medication']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // Delete medication
            $result = $medicationModel->delete($medication_id, $user_id);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete medication']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    // Appointments API
    case 'appointments':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get all appointments
            $appointments = $appointmentModel->getAll($user_id);
            echo json_encode($appointments ?: []);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Create new appointment
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
                exit;
            }
            
            $title = sanitize($data['title'] ?? '');
            $doctor = sanitize($data['doctor'] ?? '');
            $location = sanitize($data['location'] ?? '');
            $date = sanitize($data['date'] ?? '');
            $time = sanitize($data['time'] ?? '');
            $notes = sanitize($data['notes'] ?? '');
            
            if (empty($title) || empty($date) || empty($time)) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }
            
            $appointment_id = $appointmentModel->create($title, $doctor, $location, $date, $time, $notes, $user_id);
            
            if ($appointment_id) {
                $appointment = $appointmentModel->getById($appointment_id, $user_id);
                
                // Create notification for appointment
                $notificationModel->createAppointmentNotification($appointment, $user_id);
                
                echo json_encode(['success' => true, 'appointment' => $appointment]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create appointment']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case 'appointment':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing appointment ID']);
            exit;
        }
        
        $appointment_id = (int)$_GET['id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get appointment by ID
            $appointment = $appointmentModel->getById($appointment_id, $user_id);
            
            if ($appointment) {
                echo json_encode($appointment);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Appointment not found']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            // Update appointment
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
                exit;
            }
            
            $title = sanitize($data['title'] ?? '');
            $doctor = sanitize($data['doctor'] ?? '');
            $location = sanitize($data['location'] ?? '');
            $date = sanitize($data['date'] ?? '');
            $time = sanitize($data['time'] ?? '');
            $notes = sanitize($data['notes'] ?? '');
            
            if (empty($title) || empty($date) || empty($time)) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }
            
            $result = $appointmentModel->update($appointment_id, $title, $doctor, $location, $date, $time, $notes, $user_id);
            
            if ($result) {
                $appointment = $appointmentModel->getById($appointment_id, $user_id);
                echo json_encode(['success' => true, 'appointment' => $appointment]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update appointment']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // Delete appointment
            $result = $appointmentModel->delete($appointment_id, $user_id);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete appointment']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    // Prescriptions API
    case 'prescriptions':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get all prescriptions
            $prescriptions = $prescriptionModel->getAll($user_id);
            echo json_encode($prescriptions ?: []);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Create new prescription
            // For file uploads, we need to use $_POST and $_FILES
            
            $title = sanitize($_POST['title'] ?? '');
            $doctor = sanitize($_POST['doctor'] ?? '');
            $date = sanitize($_POST['date'] ?? '');
            $notes = sanitize($_POST['notes'] ?? '');
            
            if (empty($title) || empty($date)) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }
            
            // Handle image upload
            $image_data = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image_data = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
            }
            
            $prescription_id = $prescriptionModel->create($title, $doctor, $date, $image_data, $notes, $user_id);
            
            if ($prescription_id) {
                $prescription = $prescriptionModel->getById($prescription_id, $user_id);
                echo json_encode(['success' => true, 'prescription' => $prescription]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create prescription']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case 'prescription':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing prescription ID']);
            exit;
        }
        
        $prescription_id = (int)$_GET['id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get prescription by ID
            $prescription = $prescriptionModel->getById($prescription_id, $user_id);
            
            if ($prescription) {
                echo json_encode($prescription);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Prescription not found']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Update prescription (using POST for file uploads)
            
            $title = sanitize($_POST['title'] ?? '');
            $doctor = sanitize($_POST['doctor'] ?? '');
            $date = sanitize($_POST['date'] ?? '');
            $notes = sanitize($_POST['notes'] ?? '');
            
            if (empty($title) || empty($date)) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }
            
            // Handle image upload
            $image_data = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image_data = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
            }
            
            $result = $prescriptionModel->update($prescription_id, $title, $doctor, $date, $image_data, $notes, $user_id);
            
            if ($result) {
                $prescription = $prescriptionModel->getById($prescription_id, $user_id);
                echo json_encode(['success' => true, 'prescription' => $prescription]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update prescription']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // Delete prescription
            $result = $prescriptionModel->delete($prescription_id, $user_id);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete prescription']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    // Emergency Contacts API
    case 'contacts':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get all emergency contacts
            $contacts = $contactModel->getAll($user_id);
            echo json_encode($contacts ?: []);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Create new emergency contact
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
                exit;
            }
            
            $name = sanitize($data['name'] ?? '');
            $relationship = sanitize($data['relationship'] ?? '');
            $phone = sanitize($data['phone'] ?? '');
            $email = sanitize($data['email'] ?? '');
            $notes = sanitize($data['notes'] ?? '');
            
            if (empty($name) || empty($phone)) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }
            
            $contact_id = $contactModel->create($name, $relationship, $phone, $email, $notes, $user_id);
            
            if ($contact_id) {
                $contact = $contactModel->getById($contact_id, $user_id);
                echo json_encode(['success' => true, 'contact' => $contact]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create emergency contact']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case 'contact':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing contact ID']);
            exit;
        }
        
        $contact_id = (int)$_GET['id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get emergency contact by ID
            $contact = $contactModel->getById($contact_id, $user_id);
            
            if ($contact) {
                echo json_encode($contact);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Emergency contact not found']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            // Update emergency contact
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
                exit;
            }
            
            $name = sanitize($data['name'] ?? '');
            $relationship = sanitize($data['relationship'] ?? '');
            $phone = sanitize($data['phone'] ?? '');
            $email = sanitize($data['email'] ?? '');
            $notes = sanitize($data['notes'] ?? '');
            
            if (empty($name) || empty($phone)) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }
            
            $result = $contactModel->update($contact_id, $name, $relationship, $phone, $email, $notes, $user_id);
            
            if ($result) {
                $contact = $contactModel->getById($contact_id, $user_id);
                echo json_encode(['success' => true, 'contact' => $contact]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update emergency contact']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // Delete emergency contact
            $result = $contactModel->delete($contact_id, $user_id);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete emergency contact']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    // Notifications API
    case 'notifications':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get all notifications
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
            $notifications = $notificationModel->getAll($user_id, $limit);
            echo json_encode($notifications ?: []);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case 'unread-notifications':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get unread notifications
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
            $notifications = $notificationModel->getUnread($user_id, $limit);
            echo json_encode($notifications ?: []);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case 'mark-notification':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing notification ID']);
            exit;
        }
        
        $notification_id = (int)$_GET['id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Mark notification as read
            $result = $notificationModel->markAsRead($notification_id, $user_id);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to mark notification as read']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case 'mark-all-notifications':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Mark all notifications as read
            $result = $notificationModel->markAllAsRead($user_id);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to mark all notifications as read']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    // Theme API
    case 'set-theme':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['theme'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid data']);
                exit;
            }
            
            $theme = $data['theme'] === 'dark' ? 'dark' : 'light';
            $result = $userModel->setTheme($user_id, $theme);
            
            if ($result) {
                echo json_encode(['success' => true, 'theme' => $theme]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to set theme preference']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'API endpoint not found']);
        break;
}