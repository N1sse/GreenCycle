<p align="center">
  <!-- Graphic identifier for docs and GitHub -->
  <img src="design/gcycle_white.png" alt="GreenCycle Logo" width="300">
</p>

# GreenCycle

**GreenCycle** is a gamified web application where users manage a personal virtual greenhouse and a collection of digital trees.

Users can plant and view their own trees while the system progressively incorporates growth, health, time-based mechanics, harvesting, virtual currency, inventory, and a virtual shop.

---

## 🌱 Current Scope — Sprint 1

During Sprint 1, GreenCycle focuses on the initial structure of the application.

The application is expected to support:

- User registration, login, and logout.
- Access to protected resources.
- Planting a new tree.
- Viewing the authenticated user's trees.
- Viewing the details of a specific owned tree.
- Initial data modeling, migrations, relationships, and seeders.
- Authentication and authorization.
- Initial tree API.
- Base responsive interface using Fetch.

> Time-based care rules, health deterioration, progression, harvesting, Green Coins, inventory, effects, and the virtual shop will be developed in later sprints.

---

## 🛠️ Technologies and Requirements

| Area | Technologies |
|---|---|
| **Backend** | PHP 8.5 · Laravel 13 · Composer 2 |
| **Authentication** | Laravel Sanctum |
| **Frontend** | Blade · HTML5 · CSS3 · JavaScript · Fetch API · Vite |
| **Node Environment** | Node.js 26 · npm |
| **Database** | PostgreSQL · Neon |
| **Testing** | PHPUnit · SQLite in memory |
| **Local Environment** | Laravel Herd |
| **Version Control** | Git · GitHub |
| **Code Quality** | Laravel Pint · GitHub Actions |
| **Deployment** | Render |

> Docker and a local PostgreSQL installation are not required.

---

## 📁 Project Structure

- `backend/`: backend development with Laravel.

- `design/`: graphic resources and interface prototypes.

- `docs/`: project documentation, diagrams, API documentation, Sprint evidence, and development records.

- `tests/`: system testing resources and verification files.

---

## API — Sprint 1

The initial API provides the basic operations required to manage the authenticated user's trees.

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | `/api/trees` | List the authenticated user's trees |
| `GET` | `/api/trees/{tree}` | View the details of an owned tree |
| `POST` | `/api/trees` | Plant a new tree |

All tree endpoints require authentication and must prevent users from accessing trees that do not belong to them.

Internal values such as level, health, progress, state, and dates are controlled by the server and must not be defined by the client.

Detailed API documentation and related resources are stored in:

```text
docs/
```

---

## 👥 Team & Collaboration

GreenCycle is developed collaboratively by:

- **Gabriela Calderón Benavides** — C31405
- **Denisse Ramírez Villarreal** — C06398

Both team members participate in project planning, data modeling, authentication and authorization decisions, API definition, interface development, documentation, testing, and review of each Sprint delivery.

---

## 🌐 Resources & Attribution

External resources, references, libraries, and AI-assisted tools used during the project will be declared when required by the course.

The development team remains responsible for the implementation, testing, project decisions, and technical explanations.

---

## Project Status

**Sprint 1 — In Development**

The current focus is the initial data model, authentication and authorization, tree API, base interface, documentation, and reproducible development environment.

---

## Git Workflow

GreenCycle uses Git and GitHub for version control.

The project is organized around the following planned branch workflow:

| Branch | Purpose |
|---|---|
| `main` | Stable and approved versions of the project |
| `develop` | Integration of completed Sprint work |
| `feature/*` | Development of individual features |
| `docs/*` | Documentation changes |
| `fix/*` | Corrections and bug fixes |

Branches will be created and used according to the needs of each Sprint.

The final verified version of each Sprint will be merged into `main` and identified with a specific commit, tag, or GitHub release.

---

## 📜 Development Records

Detailed Sprint documentation and development records are maintained in the `docs/` directory.

Relevant commits, technical decisions, evidence, and other Sprint-related documentation can be found in:

```text
docs/commits/
```

The complete Git history remains available directly in the GitHub repository.

---

# Getting Started

The following sections explain how to obtain, configure, run, and verify GreenCycle in a local development environment.

---

# ⚙️ Installation

## 1. Clone the Repository

Open PowerShell and run:

```powershell
git clone URL_OF_THE_REPOSITORY
Set-Location greencycle
```

Replace `URL_OF_THE_REPOSITORY` with the actual GreenCycle GitHub repository URL.

---

## 2. Open the Laravel Project

Navigate to the Laravel application located inside the `backend/` directory:

```powershell
Set-Location backend/greencycle
```

All Composer, Artisan, Herd, and npm commands must be executed from the directory that contains the `artisan` file.

---

## 3. Configure Laravel Herd

GreenCycle uses **Laravel Herd through a project link** rather than depending on Herd's default parked directory.

Make sure you are inside the Laravel application directory before running the following commands.

Initialize Herd:

```powershell
herd init
```

Use PHP 8.5 for GreenCycle:

```powershell
herd isolate 8.5
```

Link the Laravel application using the GreenCycle site name:

```powershell
herd link greencycle
```

After linking the application, GreenCycle will be available locally at:

```text
http://greencycle.test
```

> GreenCycle uses `herd link greencycle` to register the Laravel application directly with Herd. The project does not need to be located inside Herd's default parked directory.

---

## 4. Select the Node.js Version

Use the Node.js version configured for the project:

```powershell
nvm use
```

---

## 5. Install Dependencies

Install the PHP dependencies:

```powershell
composer install
```

Install the frontend dependencies:

```powershell
npm ci
```

---

## 6. Create the Environment File

Copy the example environment file:

```powershell
Copy-Item .env.example .env
```

The `.env` file contains the local environment configuration required by Laravel.

> The `.env` file must never be committed to the repository.

Generate the Laravel application key:

```powershell
php artisan key:generate
```

The generated `APP_KEY` is stored automatically in the local `.env` file.

---

# ⚙️ Configuration

## 7. Configure the Development Database

Open the `.env` file and configure the connection to the Neon `development` database.

```env
DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=
DB_PASSWORD=
DB_SSLMODE=require
```

> Real database credentials and connection strings must never be committed to the repository.

---

## 8. Clear Cached Configuration

After configuring the environment variables, run:

```powershell
php artisan optimize:clear
```

---

## 9. Run Migrations and Seeders

Create the database structure and load the initial project data:

```powershell
php artisan migrate:fresh --seed
```

This command runs the project migrations and seeders required to reproduce the development database structure and initial data.

---

# ▶ Execution

## 10. Start Vite

Run:

```powershell
npm run dev
```

Keep this terminal open while developing GreenCycle.

---

## 11. Open GreenCycle

Because the Laravel application was registered using:

```powershell
herd link greencycle
```

open the following address in the browser:

```text
http://greencycle.test
```

Laravel Herd serves the application automatically.

It is not necessary to run:

```powershell
php artisan serve
```

---

## Testing and Verification

After installing and configuring GreenCycle, verify the project from the Laravel application directory.

### Automated Tests

```powershell
php artisan test
```

### PHP Formatting

Check PHP formatting with Laravel Pint:

```powershell
.\vendor\bin\pint --test
```

To automatically fix formatting:

```powershell
.\vendor\bin\pint
```

### Frontend Build

Verify that the frontend assets compile correctly:

```powershell
npm run build
```

---

## Security

The following information must never be committed or published:

- `.env`
- Real database credentials or connection strings
- `APP_KEY`
- GitHub tokens
- Passwords
- API keys
- Private credentials

Only `.env.example` should be included in the repository to document the environment variables required by the project.

Before committing changes:

```powershell
git status
```

---

## Demo Credentials

Demo credentials will only be included if they are required and allowed for the Sprint delivery.

Personal accounts or personal credentials must never be used.

If demo credentials are required, they will be added to this section before the final delivery.

---

<p align="center">
 <img src="design/gc_divider.png" alt="GreenCycle divider" width="800">
</p>

<p align="center">
  <strong>Plant. Care. Grow.</strong>
</p>

<p align="center">
  <!-- Graphic identifier for web in this case is use to close the read me-->
  <img src="design/gcycle_color.png" alt="GreenCycle LogoColor" width="300">
</p>