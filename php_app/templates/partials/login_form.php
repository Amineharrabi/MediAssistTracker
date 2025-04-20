<h4 class="text-center mb-4">Login to your account</h4>

<form method="POST" action="/index.php?route=login" class="needs-validation" novalidate>
    <div class="mb-3">
        <label for="username_or_email" class="form-label">Username or Email</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input type="text" class="form-control" id="username_or_email" name="username_or_email" placeholder="Enter username or email" required autofocus>
        </div>
        <div class="invalid-feedback">Please enter your username or email.</div>
    </div>

    <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
            <button class="btn btn-outline-secondary toggle-password" type="button" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <div class="invalid-feedback">Please enter your password.</div>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg" style="background-color: #1a9b6c; border-color: #1a9b6c; color: white;">
            <i class="fas fa-sign-in-alt me-2"></i> Login
        </button>
    </div>
</form>

<script>
    // Show/hide password toggle
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('input');
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.needs-validation');

        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);
        });
    });
</script>