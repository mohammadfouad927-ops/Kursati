# Kursati

Kursati is a training management platform built with Laravel and Filament. It provides an admin panel for managing training programs and their related records, plus a versioned JSON API for student authentication and browsing courses, groups, and branches.

> **Project status:** The admin panel and initial API are under development. The API currently supports student registration and login, and read endpoints for courses, groups, and branches. Student self-service program discovery and enrollment are planned; API write handlers for course, group, and branch resources are not implemented yet.

## Contents

- [Features](#features)
- [Technology](#technology)
- [Requirements](#requirements)
- [Installation](#installation)
- [Running the application](#running-the-application)
- [Admin panel](#admin-panel)
- [API](#api)
- [Data model](#data-model)
- [Tests](#tests)
- [Project structure](#project-structure)
- [Roadmap](#roadmap)

## Features

### Admin panel

The Filament admin panel is available at `/admin`. Its resources cover:

- Students and user accounts
- Training courses and subjects
- Instructors and their subject qualifications
- Branches and groups
- Group schedules and course assignments
- Enrollments and payment details

### API

The initial API is prefixed with `/api/v1` and includes:

- Student registration and login endpoints
- Student profile endpoint protected by the student Sanctum guard
- Paginated listing and detail endpoints for training courses, groups, and branches
- JSON resources for course subjects, group schedules, and group branch/course details

## Technology

- PHP 8.2+
- Laravel 12
- Filament 3
- Laravel Sanctum 4
- MySQL by default (SQLite can also be configured)
- Vite 7 and Tailwind CSS 4
- PHPUnit 11

## Requirements

- PHP 8.2 or newer with the PHP extensions required by Laravel
- Composer
- Node.js and npm
- A database supported by Laravel (the example environment is configured for MySQL)

## Installation

1. Clone the repository and enter the project directory:

   ```bash
   git clone https://github.com/YOUR-USERNAME/kursati.git
   cd kursati
   ```

2. Install PHP dependencies and create the environment file:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

   On Windows Command Prompt, use `copy .env.example .env` instead of `cp`.

3. Configure database settings in `.env`. The defaults expect a local MySQL database named `filament_tutorials` and a `root` user without a password. Create the database first, or update these values to match your setup.

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=filament_tutorials
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Run the migrations:

   ```bash
   php artisan migrate
   ```

5. Install JavaScript dependencies and build the frontend assets:

   ```bash
   npm install
   npm run build
   ```

The project also defines a convenience setup script. Review its commands before using it, since it runs migrations and builds assets:

```bash
composer run setup
```

## Running the application

For local development, start the Laravel server and Vite separately:

```bash
php artisan serve
```

```bash
npm run dev
```

Open the URL shown by `php artisan serve` to view the welcome page. The admin panel is at `/admin` on the same host.

Alternatively, `composer run dev` starts the Laravel server, queue listener, and Vite development server together.

## Admin panel

Visit `/admin` and sign in with a user account. The current seeder creates a basic user:

```bash
php artisan db:seed
```

The seeded account is `test@example.com` with the factory password `password`. This is for local development only; change it immediately and never use it in production.

## API

All versioned routes use the `/api/v1` prefix. For local development, the base URL is commonly `http://127.0.0.1:8000/api/v1` when using `php artisan serve`.

### Current endpoints

| Method | Endpoint | Access | Purpose |
| --- | --- | --- | --- |
| `POST` | `/register` | Public | Register a student |
| `POST` | `/login` | Public | Authenticate a student and issue a Sanctum token |
| `GET` | `/my-profile` | Student token | Retrieve the authenticated student's profile |
| `GET` | `/training-courses` | Public | List training courses with subjects (10 per page) |
| `GET` | `/training-courses/{training_course}` | Public | Retrieve one course and its subjects |
| `GET` | `/groups` | Public | List groups with branch, course, and schedules (10 per page) |
| `GET` | `/groups/{group}` | Public | Retrieve one group and its course and schedules |
| `GET` | `/branches` | Public | List branches (10 per page) |
| `GET` | `/branches/{branch}` | Public | Retrieve one branch |

Laravel's API resource declarations also register `POST`, `PUT/PATCH`, and `DELETE` routes for training courses, groups, and branches, but their controller methods are placeholders and do not currently implement those operations.

### Example: list training courses

```bash
curl http://127.0.0.1:8000/api/v1/training-courses
```

### Example: register a student

Registration validation currently expects these fields. The `avater` field name is spelled that way in the current request validator and must be sent as an image upload.

```bash
curl -X POST http://127.0.0.1:8000/api/v1/register \
  -F "name=Example Student" \
  -F "email=student@example.com" \
  -F "date_of_birth=2005-04-12" \
  -F "gender=female" \
  -F "id_card_number=12345678901234" \
  -F "phone_number=01012345678" \
  -F "address=Example Street" \
  -F "avater=@/path/to/profile.jpg" \
  -F "city=Cairo" \
  -F "school=Example School" \
  -F "grade=Grade 10"
```

The current login validator accepts `email` and `password`; its authentication logic compares `password` with the student's ID card number. Registration does not currently issue a token. These are initial implementation details and should be replaced with a secure password-based flow before production use.

### Example: access the student profile

Send the token returned from login as a Bearer token:

```bash
curl http://127.0.0.1:8000/api/v1/my-profile \
  -H "Authorization: Bearer YOUR_TOKEN"
```

The response envelope is built by the shared API response trait. Consult the current controller and resource classes for the exact response fields, which may evolve as the API is developed.

## Data model

The database schema represents the following main relationships:

- A training course contains subjects through the `program_courses` pivot table.
- Groups belong to a training course and can be assigned to a branch; groups have schedules.
- Instructors can be qualified for multiple subjects.
- Students enroll in groups through enrollment records.
- Enrollment records can have payment details, including payment status and verification information.

Migrations are in `database/migrations`. Models are in `app/Models`.

## Tests

Run the PHPUnit suite with:

```bash
php artisan test
```

The repository currently includes basic example feature and unit tests. Add feature coverage as API and enrollment behavior is implemented.

## Project structure

```text
app/
  Filament/Resources/  Admin panel resources
  Http/Controllers/    Web and API controllers
  Http/Requests/       Request validation
  Http/Resources/      API JSON resources
  Models/              Eloquent models
database/
  migrations/          Database schema
  seeders/             Database seeders
routes/
  api.php              Versioned API routes
  web.php              Web routes
tests/
  Feature/             Feature tests
  Unit/                Unit tests
```

## Roadmap

- Complete authenticated student profile management.
- Add a student-facing API to browse training programs with course descriptions and available branches.
- Add enrollment endpoints so students can join an available program/group and view their enrollments.
- Validate enrollment availability and prevent duplicate enrollments.
- Complete API resource create, update, and delete operations where appropriate.
- Expand automated tests for authentication, browsing, enrollment, and payment workflows.

## License

This project currently uses the MIT license declared in `composer.json`. See the repository's license file when one is added for the full license text.
