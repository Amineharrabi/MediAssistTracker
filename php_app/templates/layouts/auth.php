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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('/assets/img/auth-bg.svg');
            background-size: cover;
            background-position: center;
        }

        .card,
        .modal-content {
            background-color: var(--bg-card);
            color: var(--text-color);
            border-color: var(--border-color);
            box-shadow: 0 0.15rem 1.75rem 0 var(--shadow-color);
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
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

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: var(--heading-color);
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
            padding: 15px;
        }

        .brand-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .brand-icon {
            font-size: 2rem;
            margin-right: 0.5rem;
        }

        .brand-name {
            font-size: 2rem;
            font-weight: bold;
            color: var(--heading-color);
        }

        .flash-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            min-width: 300px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }

        .flash-message .close {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <!-- Flash Messages -->
        <?php if (!empty($flash_messages)): ?>
            <?php foreach ($flash_messages as $message): ?>
                <div class="flash-message alert alert-<?php echo htmlspecialchars($message['type']); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body p-4">
                <!-- Brand -->
                <div class="brand-wrapper">
                    <i class="fas fa-laptop-medical me-3 fa-3x"></i>
                    <div class="brand-name">MediAssist</div>
                </div>
                <!-- Page Content -->
                <?php if (isset($page_content) && file_exists(TEMPLATE_PATH . '/' . $page_content)): ?>
                    <?php include TEMPLATE_PATH . '/' . $page_content; ?>
                <?php else: ?>
                    <div class="alert alert-danger">
                        Template file not found: <?php echo $page_content ?? 'undefined'; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center py-3">
                <?php if ($route === 'login'): ?>
                    <div class="text-center">
                        Don't have an account? <a href="/index.php?route=register" class="text-decoration-none">Register</a>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        Already have an account? <a href="/index.php?route=login" class="text-decoration-none">Login</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-3 text-center">
            <small class="text-muted">
                &copy; <?php echo $current_year; ?> MediAssist. All rights reserved.
            </small>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom notifications script -->
    <script src="/js/notifications.js"></script>
</body>

</html>