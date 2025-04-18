<div align="center">
    <a href="https://www.php.net">
        <img
            alt="PHP"
            src="https://www.php.net/images/logos/new-php-logo.svg"
            width="150">
    </a>
</div>

# MediAssist Tracker

MediAssist Tracker is a web-based application designed to help users manage their medications, appointments, prescriptions, emergency contacts, and notifications. Built with PHP, it uses PostgreSQL as the database and supports a light/dark theme toggle.

---

## Features

- **User Authentication**: Register, login, and logout functionality.
- **Medication Management**: Add, update, delete, and view medications.
- **Appointment Management**: Schedule and manage appointments.
- **Prescription Management**: Upload and manage prescriptions.
- **Emergency Contacts**: Store and manage emergency contact information.
- **Notifications**: Receive reminders for medications and appointments.
- **Light/Dark Theme Toggle**: User preference for light or dark mode.

---

## Installation

### Prerequisites

- PHP 7.4 or higher
- PostgreSQL database
- Web server (e.g., Apache or Nginx)
- Composer (optional, but not used in this setup)

### Steps

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/Amineharrabi/MediAssistTracker.git
   cd MediAssistTracker

2. **Set Up the Database**:
Import the mediassist.sql file into your PostgreSQL database:
```bash
psql -U postgres -d MedAssistTracker -f mediassist.sql
```

Update the .env file in config with your database credentials:

DB_HOST=your_database_host
DB_PORT=5432
DB_NAME=MedAssistTracker
DB_USER=your_database_user
DB_PASS=your_database_password

3. **Configure the Environment:**
Ensure the phpdotenv library is installed manually in the phpdotenv directory.
Update the database.php file to load environment variables using phpdotenv.
4. **Set Up the Web Server**:





5.**Project Structure**

```
MediAssistTracker
├─ generated-icon.png
├─ instance
├─ mediassist.sql
├─ phpdotenv
│  ├─ composer.json
│  ├─ LICENSE
│  └─ src
│     ├─ Dotenv.php
│     ├─ Exception
│     │  ├─ ExceptionInterface.php
│     │  ├─ InvalidEncodingException.php
│     │  ├─ InvalidFileException.php
│     │  ├─ InvalidPathException.php
│     │  └─ ValidationException.php
│     ├─ Loader
│     │  ├─ Loader.php
│     │  ├─ LoaderInterface.php
│     │  └─ Resolver.php
│     ├─ Parser
│     │  ├─ Entry.php
│     │  ├─ EntryParser.php
│     │  ├─ Lexer.php
│     │  ├─ Lines.php
│     │  ├─ Parser.php
│     │  ├─ ParserInterface.php
│     │  └─ Value.php
│     ├─ Repository
│     │  ├─ Adapter
│     │  │  ├─ AdapterInterface.php
│     │  │  ├─ ApacheAdapter.php
│     │  │  ├─ ArrayAdapter.php
│     │  │  ├─ EnvConstAdapter.php
│     │  │  ├─ GuardedWriter.php
│     │  │  ├─ ImmutableWriter.php
│     │  │  ├─ MultiReader.php
│     │  │  ├─ MultiWriter.php
│     │  │  ├─ PutenvAdapter.php
│     │  │  ├─ ReaderInterface.php
│     │  │  ├─ ReplacingWriter.php
│     │  │  ├─ ServerConstAdapter.php
│     │  │  └─ WriterInterface.php
│     │  ├─ AdapterRepository.php
│     │  ├─ RepositoryBuilder.php
│     │  └─ RepositoryInterface.php
│     ├─ Store
│     │  ├─ File
│     │  │  ├─ Paths.php
│     │  │  └─ Reader.php
│     │  ├─ FileStore.php
│     │  ├─ StoreBuilder.php
│     │  ├─ StoreInterface.php
│     │  └─ StringStore.php
│     ├─ Util
│     │  ├─ Regex.php
│     │  └─ Str.php
│     └─ Validator.php
├─ php_app
│  ├─ assets
│  │  ├─ css
│  │  │  └─ style.css
│  │  ├─ img
│  │  │  ├─ auth-bg.svg
│  │  │  ├─ icon.png
│  │  │  └─ icon.svg
│  │  └─ js
│  │     ├─ main.js
│  │     └─ notifications.js
│  ├─ config
│  │  ├─ .env
│  │  ├─ config.php
│  │  └─ database.php
│  ├─ includes
│  │  ├─ functions.php
│  │  └─ models
│  │     ├─ Appointment.php
│  │     ├─ EmergencyContact.php
│  │     ├─ Medication.php
│  │     ├─ Notification.php
│  │     ├─ Prescription.php
│  │     └─ User.php
│  ├─ public
│  │  ├─ api.php
│  │  └─ index.php
│  └─ templates
│     ├─ layouts
│     │  ├─ app.php
│     │  └─ auth.php
│     └─ partials
│        ├─ appointments.php
│        ├─ contacts.php
│        ├─ dashboard.php
│        ├─ login_form.php
│        ├─ medications.php
│        ├─ prescriptions.php
│        └─ register_form.php
├─ php_app.php
├─ README.md
├─ static
│  ├─ css
│  │  └─ style.css
│  └─ js
│     ├─ appointments.js
│     ├─ contacts.js
│     ├─ main.js
│     ├─ medications.js
│     ├─ notifications.js
│     ├─ prescriptions.js
│     └─ theme.js
└─ templates
   ├─ appointments.html
   ├─ base.html
   ├─ contacts.html
   ├─ index.html
   ├─ login.html
   ├─ medications.html
   ├─ prescriptions.html
   └─ register.html

```

6.**Usage**
User Authentication
Register: Create a new account.
Login: Access your dashboard.
Logout: End your session.
Dashboard
View upcoming appointments, medications, and emergency contacts.
Medications
Add, update, delete, and view your medications.
Appointments
Schedule and manage your appointments.


Upload and manage your prescriptions.
Emergency Contacts
Store and manage emergency contact information.
Notifications
Receive reminders for medications and appointments.

7.**Troubleshooting**


Ensure the .env file is correctly configured with your database credentials.
Verify that the PostgreSQL service is running.
Environment Variables Not Loading:

Ensure the phpdotenv library is installed in the phpdotenv directory.
Verify the require_once paths in database.php.
CSS/JS Not Loading:

Ensure the assets directory is accessible from the web server.
Check the browser console for errors.
