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

**Try the Website !**
https://mediassisttracker.onrender.com
ONLINE 24/7

## Features

- **User Authentication**: Register, login, and logout functionality.
- **Medication Management**: Add, update, delete, and view medications.
- **Appointment Management**: Schedule and manage appointments.
- **Prescription Management**: Upload and manage prescriptions.
- **Emergency Contacts**: Store and manage emergency contact information.
- **Notifications**: Receive reminders for medications and appointments.
- **Light/Dark Theme Toggle**: User preference for light or dark mode.

---
## screenshots
<img src="https://github.com/Amineharrabi/MediAssistTracker/blob/main/templates/SCR1.png?raw=true" alt="Screenshot 1" width="300"/>
<img src="https://github.com/Amineharrabi/MediAssistTracker/blob/main/templates/SCR2.png?raw=true" alt="Screenshot 2" width="300"/>
<img src="https://github.com/Amineharrabi/MediAssistTracker/blob/main/templates/SCR3.png?raw=true" alt="Screenshot 3" width="300"/>





### Prerequisites

- PHP 7.4 or higher
- PostgreSQL database and Supabase (used here)
- Web server (Nginx)
- Composer (optional, but not used in this setup)
## Installation

### Steps

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/Amineharrabi/MediAssistTracker.git
   cd MediAssistTracker

   ```

2. **Set Up the Database**:
   Import the mediassist.sql file into your PostgreSQL database:

```bash
psql -U postgres -d MedAssistTracker -f mediassist.sql
```

setup Supabase:
create a new project ->
get the SUPABASE_URL
get the SUPABASE_KEY (example :
SUPABASE_URL=https://xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.supabase.co
SUPABASE_KEY=kjgKJhjhEBljfIDYDnKDldnxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

Update the .env file in config with your database credentials:

SUPABASE_URL=https://xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.supabase.co
SUPABASE_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx)

3. **Configure the Environment:**
   Ensure the phpdotenv library is installed manually in the phpdotenv directory.
   Update the database.php file to load environment variables using phpdotenv.
4. **Set Up the Web Server**:
   -setup render
   push your github fork to render , open a docker project modify the CMD docker command at the end of the dockerfile

```bash
CMD ["php", "-S", "0.0.0.0:PORT", "-t", "php_app/public"]
```

5.**Usage**
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

6.**Troubleshooting**

Ensure the .env file is correctly configured with your database credentials.
Verify that the PostgreSQL service is running.
Environment Variables Not Loading:

Ensure the phpdotenv library is installed in the phpdotenv directory.
Verify the require_once paths in database.php.
CSS/JS Not Loading:

Ensure the assets directory is accessible from the web server.
Check the browser console for errors.

5.**Project Structure**

```
MediAssistTracker
├─ composer-setup.php
├─ composer.json
├─ composer.lock
├─ Dockerfile
├─ generated-icon.png
├─ instance
├─ mediassist.sql
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
│  │  ├─ config.php
│  │  ├─ database.php
│  │  └─ load_env.php
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
│  │  ├─ .env
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
├─ start.sh
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
├─ templates
│  ├─ appointments.html
│  ├─ base.html
│  ├─ contacts.html
│  ├─ index.html
│  ├─ login.html
│  ├─ medications.html
│  ├─ prescriptions.html
│  └─ register.html
├─ test_env.php
└─ vendor
   ├─ autoload.php
   ├─ composer
   │  ├─ autoload_classmap.php
   │  ├─ autoload_files.php
   │  ├─ autoload_namespaces.php
   │  ├─ autoload_psr4.php
   │  ├─ autoload_real.php
   │  ├─ autoload_static.php
   │  ├─ ClassLoader.php
   │  ├─ installed.json
   │  ├─ installed.php
   │  ├─ InstalledVersions.php
   │  ├─ LICENSE
   │  └─ platform_check.php
   ├─ graham-campbell
   │  └─ result-type
   │     ├─ CHANGELOG.md
   │     ├─ composer.json
   │     ├─ LICENSE
   │     ├─ phpunit.xml.dist
   │     ├─ README.md
   │     ├─ src
   │     │  ├─ Error.php
   │     │  ├─ Result.php
   │     │  └─ Success.php
   │     └─ tests
   │        └─ ResultTest.php
   ├─ phpoption
   │  └─ phpoption
   │     ├─ composer.json
   │     ├─ LICENSE
   │     ├─ Makefile
   │     ├─ phpstan-baseline.neon
   │     ├─ phpstan.neon.dist
   │     ├─ phpunit.xml.dist
   │     ├─ psalm-baseline.xml
   │     ├─ psalm.xml
   │     ├─ README.md
   │     ├─ src
   │     │  └─ PhpOption
   │     │     ├─ LazyOption.php
   │     │     ├─ None.php
   │     │     ├─ Option.php
   │     │     └─ Some.php
   │     ├─ tests
   │     │  ├─ bootstrap.php
   │     │  └─ PhpOption
   │     │     └─ Tests
   │     │        ├─ EnsureTest.php
   │     │        ├─ LazyOptionTest.php
   │     │        ├─ NoneTest.php
   │     │        ├─ OptionTest.php
   │     │        └─ SomeTest.php
   │     └─ vendor-bin
   │        ├─ phpstan
   │        │  └─ composer.json
   │        └─ psalm
   │           └─ composer.json
   ├─ symfony
   │  ├─ polyfill-ctype
   │  │  ├─ bootstrap.php
   │  │  ├─ bootstrap80.php
   │  │  ├─ composer.json
   │  │  ├─ Ctype.php
   │  │  ├─ LICENSE
   │  │  └─ README.md
   │  ├─ polyfill-mbstring
   │  │  ├─ bootstrap.php
   │  │  ├─ bootstrap80.php
   │  │  ├─ composer.json
   │  │  ├─ LICENSE
   │  │  ├─ Mbstring.php
   │  │  ├─ README.md
   │  │  └─ Resources
   │  │     └─ unidata
   │  │        ├─ caseFolding.php
   │  │        ├─ lowerCase.php
   │  │        ├─ titleCaseRegexp.php
   │  │        └─ upperCase.php
   │  └─ polyfill-php80
   │     ├─ bootstrap.php
   │     ├─ composer.json
   │     ├─ LICENSE
   │     ├─ Php80.php
   │     ├─ PhpToken.php
   │     ├─ README.md
   │     └─ Resources
   │        └─ stubs
   │           ├─ Attribute.php
   │           ├─ PhpToken.php
   │           ├─ Stringable.php
   │           ├─ UnhandledMatchError.php
   │           └─ ValueError.php
   └─ vlucas
      └─ phpdotenv
         ├─ .editorconfig
         ├─ composer.json
         ├─ LICENSE
         ├─ Makefile
         ├─ phpstan-baseline.neon
         ├─ phpstan.neon.dist
         ├─ phpunit.xml.dist
         ├─ psalm-baseline.xml
         ├─ psalm.xml
         ├─ README.md
         ├─ src
         │  ├─ Dotenv.php
         │  ├─ Exception
         │  │  ├─ ExceptionInterface.php
         │  │  ├─ InvalidEncodingException.php
         │  │  ├─ InvalidFileException.php
         │  │  ├─ InvalidPathException.php
         │  │  └─ ValidationException.php
         │  ├─ Loader
         │  │  ├─ Loader.php
         │  │  ├─ LoaderInterface.php
         │  │  └─ Resolver.php
         │  ├─ Parser
         │  │  ├─ Entry.php
         │  │  ├─ EntryParser.php
         │  │  ├─ Lexer.php
         │  │  ├─ Lines.php
         │  │  ├─ Parser.php
         │  │  ├─ ParserInterface.php
         │  │  └─ Value.php
         │  ├─ Repository
         │  │  ├─ Adapter
         │  │  │  ├─ AdapterInterface.php
         │  │  │  ├─ ApacheAdapter.php
         │  │  │  ├─ ArrayAdapter.php
         │  │  │  ├─ EnvConstAdapter.php
         │  │  │  ├─ GuardedWriter.php
         │  │  │  ├─ ImmutableWriter.php
         │  │  │  ├─ MultiReader.php
         │  │  │  ├─ MultiWriter.php
         │  │  │  ├─ PutenvAdapter.php
         │  │  │  ├─ ReaderInterface.php
         │  │  │  ├─ ReplacingWriter.php
         │  │  │  ├─ ServerConstAdapter.php
         │  │  │  └─ WriterInterface.php
         │  │  ├─ AdapterRepository.php
         │  │  ├─ RepositoryBuilder.php
         │  │  └─ RepositoryInterface.php
         │  ├─ Store
         │  │  ├─ File
         │  │  │  ├─ Paths.php
         │  │  │  └─ Reader.php
         │  │  ├─ FileStore.php
         │  │  ├─ StoreBuilder.php
         │  │  ├─ StoreInterface.php
         │  │  └─ StringStore.php
         │  ├─ Util
         │  │  ├─ Regex.php
         │  │  └─ Str.php
         │  └─ Validator.php
         ├─ tests
         │  ├─ Dotenv
         │  │  ├─ DotenvTest.php
         │  │  ├─ Loader
         │  │  │  └─ LoaderTest.php
         │  │  ├─ Parser
         │  │  │  ├─ EntryParserTest.php
         │  │  │  ├─ LexerTest.php
         │  │  │  ├─ LinesTest.php
         │  │  │  └─ ParserTest.php
         │  │  ├─ Repository
         │  │  │  ├─ Adapter
         │  │  │  │  ├─ ArrayAdapterTest.php
         │  │  │  │  ├─ EnvConstAdapterTest.php
         │  │  │  │  ├─ PutenvAdapterTest.php
         │  │  │  │  └─ ServerConstAdapterTest.php
         │  │  │  └─ RepositoryTest.php
         │  │  ├─ Store
         │  │  │  └─ StoreTest.php
         │  │  └─ ValidatorTest.php
         │  └─ fixtures
         │     └─ env
         │        ├─ .env
         │        ├─ assertions.env
         │        ├─ booleans.env
         │        ├─ commented.env
         │        ├─ empty.env
         │        ├─ example.env
         │        ├─ exported.env
         │        ├─ immutable.env
         │        ├─ integers.env
         │        ├─ large.env
         │        ├─ multibyte.env
         │        ├─ multiline.env
         │        ├─ multiple.env
         │        ├─ mutable.env
         │        ├─ nested.env
         │        ├─ quoted.env
         │        ├─ specialchars.env
         │        ├─ unicodevarnames.env
         │        ├─ utf8-with-bom-encoding.env
         │        └─ windows.env
         ├─ UPGRADING.md
         └─ vendor-bin
            ├─ phpstan
            │  └─ composer.json
            └─ psalm
               └─ composer.json

```
