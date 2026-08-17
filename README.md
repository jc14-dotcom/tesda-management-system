# Alcatt Portal

Alcatt Portal is a role-based employee and credential management system built with Laravel. It gives organizations a single place to maintain user profiles, store employee documents, track certificates and qualifications, and manage account approvals and compliance-related activity.



## What the system does

### For users

- Register for an account and provide required profile information.
- Accept the Data Privacy Act of 2012 (RA 10173) privacy notice and terms of use.
- Complete and maintain a personal profile, including employment, qualification, and TESDA-related details.
- Upload, preview, download, and delete documents such as CVs and training certificates.
- Add and manage professional certificates, including certificate type, number, issuing organization, issue date, expiry date, and remarks.
- View certificate status, including valid, expiring, and expired certificates.
- Browse the user directory when permitted.
- Receive in-application and email notifications.
- Update account settings and manage profile information.

### For administrators

- View dashboard statistics and live activity information.
- Review, approve, activate, deactivate, and manage user accounts.
- View user profiles, certificates, and uploaded documents.
- Review certificate and document records across the organization.
- Configure qualification titles and application settings.
- Publish announcements.
- Manage notifications and review the activity log.
- Export user and certificate data.
- Create, download, restore, and delete database backups.
- Send weekly activity digests and certificate-expiry notifications.

## Authentication and account workflow

1. A person registers and accepts the privacy notice and terms of use.
2. The account remains pending until an administrator approves it.
3. After approval, the user logs in for the first time.
4. The system sends a six-digit email verification code to the approved user.
5. The user enters the code before continuing to profile completion or the dashboard.
6. Once verified, the user can access the rest of the portal according to their role.

Verification codes are hashed before storage, expire after a configurable period, and are replaced when a new code is requested. The default expiration period is 10 minutes.

## Access control and privacy

The application uses separate `admin` and `user` roles. Administrative routes are protected by role middleware, while users can access only their own profiles, certificates, and documents unless they are administrators.

The application records privacy-notice acceptance and uses activity logging for important user, profile, certificate, and document changes. Uploaded documents are stored on the configured private filesystem and are served through authorized application routes.

## Technology stack

- PHP 8.3+
- Laravel 13
- SQLite by default for local development; other Laravel-supported databases can be configured
- Laravel Blade, Vite, Tailwind CSS, Alpine.js, Flowbite, and Turbo
- Database-backed sessions, queues, and cache by default in the example environment
- Spatie Laravel Permission for roles
- Spatie Laravel Activitylog for audit history
- Spatie Laravel Backup for backup support
- PHPUnit for automated tests

## Requirements

Install the following before setting up the project:

- PHP 8.3 or newer with the required Laravel extensions
- Composer
- Node.js and npm
- A database supported by Laravel
- An SMTP provider if real OTP and notification emails are required

Windows users can run the application with Laragon. The included `start-app.bat` starts the Laravel development server and a database queue worker.

## Local installation

Clone the repository and enter the project directory:

```bash
git clone <repository-url>
cd Alcat-system
```

Install the PHP and frontend dependencies:

```bash
composer install
npm install
```

Create the environment file and application key:

```bash
copy .env.example .env
php artisan key:generate
```

On macOS/Linux, use this instead of `copy`:

```bash
cp .env.example .env
```

Configure the database and mail settings in `.env`. The default example uses SQLite. Create the SQLite file if it does not exist:

```powershell
New-Item database/database.sqlite -ItemType File
```

On macOS/Linux:

```bash
touch database/database.sqlite
```

Run migrations and seed the default roles and sample accounts:

```bash
php artisan migrate --seed
```

The seeder creates the `admin` and `user` roles, plus one administrator and one sample user. Set these values in `.env` before seeding if you want custom credentials:

```dotenv
ADMIN_NAME=System Admin
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=change-this-password

USER_NAME=Sample User
USER_EMAIL=user@example.com
USER_PASSWORD=change-this-password
```

Build the frontend assets:

```bash
npm run build
```

## Running the application

### Standard development setup

Run the Laravel server, queue worker, log viewer, and Vite development server together:

```bash
composer run dev
```

Alternatively, run the services separately:

```bash
php artisan serve
php artisan queue:work --tries=5
npm run dev
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

### Windows/Laragon launcher

From a Windows terminal, run:

```bat
start-app.bat
```

This starts the queue worker in the background and serves the application at `http://127.0.0.1:8000`. Run `npm run dev` separately when working with live frontend asset rebuilding, or run `npm run build` first for compiled assets.

### Queue worker startup on Windows

The queue is used for asynchronous notifications and other background work. To install the optional login-startup worker:

```bash
php artisan app:install-startup
```

To remove it:

```bash
php artisan app:install-startup --uninstall
```

This command is Windows-only. On Linux or macOS, use a process manager such as Supervisor or systemd.

## Configuration

Important environment settings include:

| Variable | Purpose | Default |
| --- | --- | --- |
| `APP_URL` | Base application URL | `http://localhost` |
| `DB_CONNECTION` | Database driver | `sqlite` |
| `QUEUE_CONNECTION` | Queue backend | `database` |
| `CACHE_STORE` | Cache backend | `database` |
| `MAIL_MAILER` | Mail transport | `log` |
| `CERTIFICATES_NOTIFICATIONS_ENABLED` | Enable user certificate-expiry emails | `false` |
| `VERIFICATION_OTP_EXPIRE` | OTP lifetime in minutes | `10` |
| `PERF_LOG_ENABLED` | Enable request performance logging | `false` |

For local development, `MAIL_MAILER=log` writes outgoing mail to the application logs. Configure SMTP values before testing real OTP delivery.

## Useful Artisan commands

```bash
# Run the test suite
php artisan test

# Clear application caches
php artisan app:optimize --clear

# Warm configuration, route, and view caches
php artisan app:optimize

# Run a database-only backup
php artisan backups:run-database

# Update certificate statuses and send configured expiry notifications
php artisan certificates:send-expiry-notifications

# Send the weekly administrator activity digest
php artisan admin:send-notifications --weekly-digest
```

## Testing

Run the complete PHPUnit feature-test suite with:

```bash
composer test
```

The tests cover authentication, registration, email verification, password flows, profiles, certificates, and documents.

## Project structure

```text
app/
ÃƒÂ¢Ã¢â‚¬ÂÃ…â€œÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Console/Commands/       Custom maintenance, backup, and notification commands
ÃƒÂ¢Ã¢â‚¬ÂÃ…â€œÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Http/Controllers/       User, admin, authentication, profile, and document flows
ÃƒÂ¢Ã¢â‚¬ÂÃ…â€œÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Models/                 Users, profiles, certificates, documents, and settings
ÃƒÂ¢Ã¢â‚¬ÂÃ…â€œÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Notifications/          User and administrator notifications
ÃƒÂ¢Ã¢â‚¬ÂÃ…â€œÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Policies/               Authorization rules for protected records
ÃƒÂ¢Ã¢â‚¬ÂÃ¢â‚¬ÂÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Support/                Backup, caching, and notification helpers

database/
ÃƒÂ¢Ã¢â‚¬ÂÃ…â€œÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ migrations/             Database schema and indexes
ÃƒÂ¢Ã¢â‚¬ÂÃ…â€œÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ seeders/                Roles and development accounts
ÃƒÂ¢Ã¢â‚¬ÂÃ¢â‚¬ÂÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ factories/              Test data factories

resources/
ÃƒÂ¢Ã¢â‚¬ÂÃ…â€œÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ views/                  Blade pages and reusable UI components
ÃƒÂ¢Ã¢â‚¬ÂÃ…â€œÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ css/                    Tailwind and application styles
ÃƒÂ¢Ã¢â‚¬ÂÃ¢â‚¬ÂÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ js/                     Frontend modules and UI behavior

routes/                     Web and authentication routes
tests/                      PHPUnit feature tests
```

## Production notes

Before deploying:

- Set `APP_ENV=production`, `APP_DEBUG=false`, and a strong application key.
- Use strong, unique administrator credentials.
- Configure a production database, mail provider, cache, queue worker, and private file storage.
- Run `php artisan migrate --force` during deployment.
- Run `php artisan app:optimize` after configuration and route changes.
- Keep queue workers running continuously so notifications are processed.
- Schedule certificate status/expiry notification commands according to the organization's operations.
- Protect database backups and uploaded documents from public access.
- Never commit `.env`, real credentials, or production documents to the repository.

## License

This project is released under the MIT License. See the [MIT License](https://opensource.org/licenses/MIT) for details.
