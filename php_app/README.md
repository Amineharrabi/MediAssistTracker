# MediAssist (PHP Version)

MediAssist is a comprehensive web application for managing medications, medical appointments, prescriptions, and emergency contacts. This is the PHP version of the application, converted from the original Flask version.

## Features

- **User Authentication**: Secure login and registration
- **Medication Management**: Track medications, dosages, frequencies, and schedules
- **Appointment Tracking**: Calendar view and list of upcoming appointments
- **Prescription Storage**: Store and view prescription information with image uploads
- **Emergency Contacts**: Quick access to important contacts during emergencies
- **Notifications**: Get reminders for medications and appointments
- **Theme Options**: Toggle between light and dark modes

## Technical Details

- **Backend**: PHP 8.2+
- **Database**: PostgreSQL
- **Frontend**: HTML, CSS, JavaScript
- **Frameworks/Libraries**:
  - Bootstrap 5 for responsive UI
  - Font Awesome for icons
  - Vanilla JavaScript for interactivity

## Project Structure

- `/config`: Configuration files (database, app settings)
- `/includes`: Core PHP files
  - `/models`: Database models (User, Medication, etc.)
  - `functions.php`: Utility functions
- `/public`: Public files (entry point)
  - `index.php`: Main entry point
  - `api.php`: API endpoints for AJAX requests
- `/assets`: Static assets
  - `/css`: Stylesheets
  - `/js`: JavaScript files
  - `/img`: Images
- `/templates`: HTML templates
  - `/layouts`: Layout templates (app, auth)
  - `/partials`: Partial templates for each page

## Installation

1. Clone the repository
2. Ensure PHP 8.2+ and PostgreSQL are installed
3. Create a PostgreSQL database
4. Configure database settings in `/config/database.php`
5. Access the application via a web server (e.g., Apache, Nginx)

## Environment Variables

The application uses the following environment variables:

- `PGHOST`: PostgreSQL host
- `PGPORT`: PostgreSQL port
- `PGDATABASE`: PostgreSQL database name
- `PGUSER`: PostgreSQL username
- `PGPASSWORD`: PostgreSQL password
- `SESSION_SECRET`: Secret key for PHP sessions

## Security Features

- Password hashing with bcrypt
- CSRF protection
- SQL injection prevention with prepared statements
- XSS protection with output escaping
- Secure session management

## Responsive Design

The application is fully responsive and works on:
- Desktop browsers
- Tablets
- Mobile devices

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)