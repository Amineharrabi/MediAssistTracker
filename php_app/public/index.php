<?php
require_once dirname(__DIR__) . '/config/config.php';
echo getenv('DB_HOST');
require_once INCLUDES_PATH . '/models/User.php';
require_once INCLUDES_PATH . '/models/Medication.php';
require_once INCLUDES_PATH . '/models/Appointment.php';
require_once INCLUDES_PATH . '/models/Prescription.php';
require_once INCLUDES_PATH . '/models/EmergencyContact.php';
require_once INCLUDES_PATH . '/models/Notification.php';
require_once INCLUDES_PATH . '/models/Notification.php';
require_once __DIR__ . '/../config/load_env.php';


// Initialize models
$userModel = new User($_ENV['SUPABASE_URL'], $_ENV['SUPABASE_KEY']);
$medicationModel = new Medication($_ENV['SUPABASE_URL'], $_ENV['SUPABASE_KEY']);
$appointmentModel = new Appointment($_ENV['SUPABASE_URL'], $_ENV['SUPABASE_KEY']);
$prescriptionModel = new Prescription($_ENV['SUPABASE_URL'], $_ENV['SUPABASE_KEY']);
$contactModel = new EmergencyContact($_ENV['SUPABASE_URL'], $_ENV['SUPABASE_KEY']);
$notificationModel = new Notification($_ENV['SUPABASE_URL'], $_ENV['SUPABASE_KEY']);

// Handle page routing
$route = $_GET['route'] ?? 'home';


// Check for theme toggle
if (isset($_POST['toggle_theme']) && is_logged_in()) {
    $new_theme = $_SESSION['user_theme'] === 'light' ? 'dark' : 'light';
    $userModel->setTheme(get_user_id(), $new_theme);

    // Redirect to same page to prevent form resubmission
    $current_url = $_SERVER['REQUEST_URI'];
    redirect($current_url);
}

// Define variables for templates
$page_title = 'MediAssist';
$flash_messages = get_flash_messages();

// Handle routing
switch ($route) {
    case 'register':
        if (is_logged_in()) {
            redirect('/');
        }

        $page_title = 'Register to MediAssist';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = sanitize($_POST['username'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            $errors = [];

            if (empty($username)) {
                $errors[] = 'Username is required.';
            }

            if (empty($email)) {
                $errors[] = 'Email is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format.';
            }

            if (empty($password)) {
                $errors[] = 'Password is required.';
            } elseif (strlen($password) < 8) {
                $errors[] = 'Password must be at least 8 characters.';
            }

            if ($password !== $password_confirm) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                $result = $userModel->create($username, $email, $password);

                if ($result) {
                    add_flash_message('Account created successfully! Please log in.', 'success');
                    redirect('/index.php?route=login');
                }
            } else {
                foreach ($errors as $error) {
                    add_flash_message($error, 'danger');
                }
            }
        }

        render('layouts/auth.php', [
            'page_title' => $page_title,
            'page_content' => 'partials/register_form.php',
            'flash_messages' => $flash_messages,
            'route' => $route
        ]);
        break;

    case 'login':
        if (is_logged_in()) {
            redirect('/');
        }

        $page_title = 'Login - MediAssist';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username_or_email = sanitize($_POST['username_or_email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username_or_email) || empty($password)) {
                add_flash_message('Both username/email and password are required.', 'danger');
            } else {
                $result = $userModel->login($username_or_email, $password);

                if ($result) {
                    add_flash_message('Login successful!', 'success');
                    redirect('/');
                } else {
                    add_flash_message('Invalid username/email or password.', 'danger');
                }
            }
        }

        render('layouts/auth.php', [
            'page_title' => $page_title,
            'page_content' => 'partials/login_form.php',
            'flash_messages' => $flash_messages,
            'route' => $route
        ]);
        break;

    case 'logout':
        if (is_logged_in()) {
            $userModel->logout();
            add_flash_message('You have been logged out.', 'info');
        }

        redirect('/index.php?route=login');
        break;

    case 'medications':
        require_login();
        $page_title = 'Medications - MediAssist';

        // Get all medications for user
        $medications = $medicationModel->getAll(get_user_id());

        render('layouts/app.php', [
            'page_title' => $page_title,
            'page_content' => 'partials/medications.php',
            'flash_messages' => $flash_messages,
            'medications' => $medications ?: [],
            'route' => $route
        ]);
        break;

    case 'appointments':
        require_login();
        $page_title = 'Appointments - MediAssist';

        // Get all appointments for user
        $appointments = $appointmentModel->getAll(get_user_id());

        render('layouts/app.php', [
            'page_title' => $page_title,
            'page_content' => 'partials/appointments.php',
            'flash_messages' => $flash_messages,
            'appointments' => $appointments ?: [],
            'route' => $route
        ]);
        break;

    case 'prescriptions':
        require_login();
        $page_title = 'Prescriptions - MediAssist';

        // Get all prescriptions for user
        $prescriptions = $prescriptionModel->getAll(get_user_id());

        render('layouts/app.php', [
            'page_title' => $page_title,
            'page_content' => 'partials/prescriptions.php',
            'flash_messages' => $flash_messages,
            'prescriptions' => $prescriptions ?: [],
            'route' => $route
        ]);
        break;

    case 'contacts':
        require_login();
        $page_title = 'Emergency Contacts - MediAssist';

        // Get all emergency contacts for user
        $contacts = $contactModel->getAll(get_user_id());

        render('layouts/app.php', [
            'page_title' => $page_title,
            'page_content' => 'partials/contacts.php',
            'flash_messages' => $flash_messages,
            'contacts' => $contacts ?: [],
            'route' => $route
        ]);
        break;

    case 'home':
    default:
        if (!is_logged_in()) {
            redirect('/index.php?route=login');
        }

        $page_title = 'Dashboard - MediAssist';

        // Get upcoming appointments
        $upcoming_appointments = $appointmentModel->getUpcoming(get_user_id(), 5);

        // Get medications
        $medications = $medicationModel->getAll(get_user_id());

        // Get emergency contacts
        $contacts = $contactModel->getAll(get_user_id());

        render('layouts/app.php', [
            'page_title' => $page_title,
            'page_content' => 'partials/dashboard.php',
            'flash_messages' => $flash_messages,
            'upcoming_appointments' => $upcoming_appointments ?: [],
            'medications' => $medications ?: [],
            'contacts' => $contacts ?: [],
            'route' => $route
        ]);
        break;
}
