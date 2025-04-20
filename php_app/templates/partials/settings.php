
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">
                    <i class="fas fa-cog me-2 text-primary"></i> Settings
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/index.php?route=settings" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <h6>Theme Preference</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="theme" id="theme_light" value="light" <?php echo $theme === 'light' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="theme_light">
                                <i class="fas fa-sun me-1"></i> Light Mode
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="theme" id="theme_dark" value="dark" <?php echo $theme === 'dark' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="theme_dark">
                                <i class="fas fa-moon me-1"></i> Dark Mode
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6>Notification Preferences</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="notifications_enabled" id="notifications_enabled" checked>
                            <label class="form-check-label" for="notifications_enabled">
                                Enable Notifications
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Preferences
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
