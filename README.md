# 🔗 Mini LinkedIn — Backend API

> RESTful API for a recruitment platform built with **Laravel**, featuring JWT 
> authentication, role-based middleware, Eloquent ORM, and an Events & Listeners system.

---

## 📋 Table of Contents

- [Context](#context)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Database Seeding](#database-seeding)
- [API Routes](#api-routes)
- [Authorization Rules](#authorization-rules)
- [Events & Listeners](#events--listeners)
- [Postman Collection](#postman-collection)
- [Project Structure](#project-structure)

---

## Context

Backend API of a Mini LinkedIn recruitment platform connecting **candidates** 
and **recruiters**, supervised by an **administrator**.

- A **candidat** creates a profile, adds skills, and applies to job offers.
- A **recruteur** posts offers and manages received applications.
- An **admin** supervises all users and offers across the platform.

---

## Tech Stack

| Layer           | Technology              |
|-----------------|-------------------------|
| Language        | PHP 8.x                 |
| Framework       | Laravel 10.x            |
| Authentication  | JWT (`tymon/jwt-auth`)  |
| Database        | MySQL                   |
| Testing         | Postman                 |

---

## Prerequisites

- PHP >= 8.1
- Composer
- MySQL
- Laravel CLI

---

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/projet-backend/Mini_LinkedIn.git
cd Mini_LinkedIn

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.example .env
# Edit DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env

# 4. Generate application key
php artisan key:generate

# 5. Generate JWT secret
php artisan jwt:secret

# 6. Run migrations and seed
php artisan migrate --seed

# 7. Start the server
php artisan serve
```

API base URL: `http://127.0.0.1:8000/api`

---

## Database Seeding

| Role      | Count | Details                         |
|-----------|-------|---------------------------------|
| Admin     | 2     | Full platform access            |
| Recruteur | 5     | 2–3 active job offers each      |
| Candidat  | 10    | Each with a profile and skills  |

---

## API Routes

###  Public

| Method | Endpoint        | Description           |
|--------|-----------------|-----------------------|
| POST   | `/api/register` | Register a new user   |
| POST   | `/api/login`    | Login — receive JWT   |

---

###  Protected (all roles — `auth:api`)

| Method | Endpoint    | Description         |
|--------|-------------|---------------------|
| POST   | `/api/logout` | Invalidate token  |

---

###  Candidat only (`role:candidat`)

| Method | Endpoint                                  | Description                     |
|--------|-------------------------------------------|---------------------------------|
| POST   | `/api/profil`                             | Create profile (once only)      |
| GET    | `/api/profil`                             | View own profile                |
| PUT    | `/api/profil`                             | Update own profile              |
| POST   | `/api/profil/competences`                 | Add a skill with level          |
| DELETE | `/api/profil/competences/{competence}`    | Remove a skill                  |
| POST   | `/api/offres/{offre}/candidater`          | Apply to a job offer            |
| GET    | `/api/mes-candidatures`                   | View own applications           |

---

###  Job Offers — all authenticated users

| Method | Endpoint              | Description                              |
|--------|-----------------------|------------------------------------------|
| GET    | `/api/offres`         | List active offers (paginated, filtered) |
| GET    | `/api/offres/{offre}` | View offer details                       |

> Supports query filters: `?localisation=Casablanca&type=CDI`  
> Paginated: 10 results/page — sorted by `created_at` descending.

---

###  Recruteur only (`role:recruteur`)

| Method | Endpoint                                   | Description                       |
|--------|--------------------------------------------|-----------------------------------|
| POST   | `/api/offres`                              | Create a new offer                |
| PUT    | `/api/offres/{offre}`                      | Update own offer                  |
| DELETE | `/api/offres/{offre}`                      | Delete own offer                  |
| GET    | `/api/offres/{offre}/candidatures`         | View applications for own offer   |
| PATCH  | `/api/candidatures/{candidature}/statut`   | Change application status         |

---

###  Admin only (`role:admin` — prefix `/admin`)

| Method | Endpoint                        | Description                   |
|--------|---------------------------------|-------------------------------|
| GET    | `/api/admin/users`              | List all platform users       |
| DELETE | `/api/admin/users/{user}`       | Delete a user account         |
| PATCH  | `/api/admin/offres/{offre}`     | Toggle offer active status    |

---

## Authorization Rules

- All routes except `/register` and `/login` require a valid JWT → `401` if missing
- Role violations return `403 Forbidden`
- A recruteur can only modify or delete **their own** offers
- A candidat can only view **their own** applications
- Role enforcement is handled via a custom `role` middleware

---

## Events & Listeners

Laravel's Event/Listener system is used to decouple application logic from controllers.

| Event                  | Triggered When                        | Listener                     | Output                                              |
|------------------------|---------------------------------------|------------------------------|-----------------------------------------------------|
| `CandidatureDeposee`   | Candidat applies to an offer          | `LogCandidatureDeposee`      | Logs date, candidate name, offer title              |
| `StatutCandidatureMis` | Recruteur changes application status  | `LogStatutCandidatureMis`    | Logs old status, new status, and timestamp          |

Log file: `storage/logs/candidatures.log`

The `CandidatureDeposee` event receives the full `Candidature` model instance 
via its constructor, making all related data accessible to the listener.

---

## Postman Collection

Available in `/postman/mini_linkedin.json`.

Covers: registration, login (all roles), profile CRUD, offer CRUD, 
apply to offer, view applications, change status, admin actions,  
and error cases: `401`, `403`, `422`.

---

## Project Structure

```
app/
├── Events/
│   ├── CandidatureDeposee.php
│   └── StatutCandidatureMis.php
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── CandidatureController.php
│   │   ├── OffreController.php
│   │   └── ProfilController.php
│   └── Middleware/
│       └── RoleMiddleware.php
├── Listeners/
│   ├── LogCandidatureDeposee.php
│   └── LogStatutCandidatureMis.php
├── Models/
│   ├── Candidature.php
│   ├── Competence.php
│   ├── Offre.php
│   ├── Profil.php
│   └── User.php
└── Providers/
    └── AppServiceProvider.php
database/
├── factories/
│   ├── CandidatureFactory.php
│   ├── CompetenceFactory.php
│   ├── OffreFactory.php
│   ├── ProfilFactory.php
│   └── UserFactory.php
├── migrations/
│   ├── create_users_table.php
│   ├── create_profils_table.php
│   ├── create_competences_table.php
│   ├── create_competence_profil_table.php
│   ├── create_offres_table.php
│   └── create_candidatures_table.php
└── seeders/
    └── DatabaseSeeder.php
routes/
├── api.php
└── web.php
postman/
└── mini_linkedin.json
```

---

## Authors

Developed by:
- **EL Hamoudi Wiam**
- **Biby Maryam**
- **ELMaaroufi Soukaina**

ENSAM Casablanca — Département Génie Informatique et IA
Supervised by: **WARDI Ahmed**
