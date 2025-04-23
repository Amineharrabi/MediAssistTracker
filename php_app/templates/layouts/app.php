<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $theme; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'MediAssist'; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <!-- Theme-specific CSS -->
    <style>
        :root {
            --primary-color: #000000;
            --secondary-color: #555555;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }

        [data-theme="light"] {
            --bg-color: #f8f9fc;
            --bg-card: rgba(255, 255, 255, 0.8);
            --text-color: #5a5c69;
            --heading-color: #000000;
            --border-color: #e3e6f0;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        [data-theme="dark"] {
            --bg-color: #1e2130;
            --bg-card: rgba(40, 44, 52, 0.8);
            --text-color: #f8f9fc;
            --heading-color: #ffffff;
            --border-color: #4b5064;
            --shadow-color: rgba(0, 0, 0, 0.3);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .card,
        .modal-content {
            background-color: var(--bg-card);
            color: var(--text-color);
            border-color: var(--border-color);
            box-shadow: 0 0.15rem 1.75rem 0 var(--shadow-color);
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        .table {
            color: var(--text-color);
        }

        .form-control,
        .form-select {
            background-color: var(--bg-color);
            color: var(--text-color);
            border-color: var(--border-color);
        }

        .form-control:focus,
        .form-select:focus {
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .navbar,
        .footer {
            background-color: var(--bg-card);
            border-color: var(--border-color);
        }

        .navbar-brand,
        .nav-link {
            color: var(--text-color) !important;
        }

        .nav-link.active {
            color: var(--heading-color) !important;
            font-weight: bold;
        }

        .dropdown-menu {
            background-color: var(--bg-card);
            border-color: var(--border-color);
        }

        .dropdown-item {
            color: var(--text-color);
        }

        .dropdown-item:hover {
            background-color: var(--bg-color);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: var(--heading-color);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4 backdrop-blur">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <i class="fas fa-laptop-medical me-2 text-primary"></i>
                <span>MediAssist</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $route === 'home' ? 'active' : ''; ?>" href="/">
                            <i class="fas fa-clinic-medical me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $route === 'medications' ? 'active' : ''; ?>" href="/index.php?route=medications">
                            <i class="fas fa-laptop-medical me-2"></i> Medications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $route === 'appointments' ? 'active' : ''; ?>" href="/index.php?route=appointments">
                            <i class="fa-solid fa-calendar-days me-1"></i> Appointments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $route === 'prescriptions' ? 'active' : ''; ?>" href="/index.php?route=prescriptions">
                            <i class="fa-solid fa-file-waveform me-1"></i> Prescriptions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $route === 'contacts' ? 'active' : ''; ?>" href="/index.php?route=contacts">
                            <i class="fas fa-address-book me-1"></i> Contacts
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                                0
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                            <h6 class="dropdown-header">Notifications</h6>
                            <div id="notifications-container">
                                <div class="dropdown-item text-center">No unread notifications</div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="#" id="mark-all-read">Mark all as read</a>
                        </div>
                    </li>

                    <!-- Theme Toggle -->
                    <li class="nav-item me-2">
                        <form method="POST" id="theme-form">
                            <input type="hidden" name="toggle_theme" value="1">
                            <button type="submit" class="btn nav-link">
                                <i class="fas <?php echo $theme === 'light' ? 'fa-moon' : 'fa-sun'; ?>"></i>
                                <span class="d-none d-md-inline ms-1"><?php echo $theme === 'light' ? 'Dark Mode' : 'Light Mode'; ?></span>
                            </button>
                        </form>
                    </li>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-user me-1"></i>
                            <span class="d-none d-md-inline"><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item <?php echo $route === 'profile' ? 'active' : ''; ?>" href="/index.php?route=profile"><i class="fas fa-user-cog me-1"></i> Profile</a></li>
                            <li><a class="dropdown-item <?php echo $route === 'settings' ? 'active' : ''; ?>" href="/index.php?route=settings"><i class="fas fa-cog me-1"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/index.php?route=logout"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mb-4">
        <!-- Flash Messages -->
        <?php if (!empty($flash_messages)): ?>
            <div id="flash-messages">
                <?php foreach ($flash_messages as $message): ?>
                    <div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <?php if (isset($page_content) && file_exists(TEMPLATE_PATH . '/' . $page_content)): ?>
            <?php include TEMPLATE_PATH . '/' . $page_content; ?>
        <?php else: ?>
            <div class="alert alert-danger">
                Template file not found: <?php echo $page_content ?? 'undefined'; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 text-center">
        <div class="container">
            <span class="text-muted">
                &copy; <?php echo $current_year; ?> MediAssist. All rights reserved.
            </span>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom JS -->
    <script src="/static/js/main.js"></script>
    <script src="/static/js/notifications.js"></script>

    <!-- Page-specific JS -->
    <?php if ($route === 'medications'): ?>
        <script src="/static/js/medications.js"></script>
    <?php elseif ($route === 'appointments'): ?>
        <script src="/static/js/appointments.js"></script>
    <?php elseif ($route === 'prescriptions'): ?>
        <script src="/static/js/prescriptions.js"></script>
    <?php elseif ($route === 'contacts'): ?>
        <script src="/static/js/contacts.js"></script>
    <?php endif; ?>
</body>

</html>