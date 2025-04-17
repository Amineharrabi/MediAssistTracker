<h4 class="text-center mb-4">Create an account</h4>

<form method="POST" action="/index.php?route=register" class="needs-validation" novalidate>
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required autofocus>
        </div>
        <div class="invalid-feedback">Please choose a username.</div>
    </div>
    
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="invalid-feedback">Please enter a valid email address.</div>
    </div>
    
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required minlength="8">
            <button class="btn btn-outline-secondary toggle-password" type="button">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <div class="form-text">Password must be at least 8 characters long.</div>
        <div class="invalid-feedback">Password must be at least 8 characters long.</div>
    </div>
    
    <div class="mb-4">
        <label for="password_confirm" class="form-label">Confirm Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirm your password" required>
            <button class="btn btn-outline-secondary toggle-password" type="button">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <div class="invalid-feedback">Please confirm your password.</div>
    </div>
    
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-user-plus me-2"></i> Register
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
    
    // Password confirmation validation
    document.getElementById('password_confirm').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        
        if (this.value !== password) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
    
    document.getElementById('password').addEventListener('input', function() {
        const confirmPassword = document.getElementById('password_confirm');
        
        if (confirmPassword.value !== this.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
        } else {
            confirmPassword.setCustomValidity('');
        }
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