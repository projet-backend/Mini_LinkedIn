# 🔗 Mini LinkedIn — API Backend

> API RESTful d'une plateforme de recrutement construite avec **Laravel**, 
> intégrant l'authentification JWT, une autorisation par rôles, 
> l'ORM Eloquent et un système d'Events & Listeners.

---

##  Table des matières

- [Contexte](#contexte)
- [Technologies utilisées](#technologies-utilisées)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Données de test](#données-de-test)
- [Routes de l'API](#routes-de-lapi)
- [Règles d'autorisation](#règles-dautorisation)
- [Events & Listeners](#events--listeners)
- [Collection Postman](#collection-postman)
- [Structure du projet](#structure-du-projet)
- [Auteurs](#auteurs)

---

## Contexte

API backend d'une plateforme de recrutement Mini LinkedIn mettant en relation 
des **candidats** et des **recruteurs**, supervisée par un **administrateur**.

- Un **candidat** crée son profil, ajoute ses compétences et postule à des offres.
- Un **recruteur** publie des offres et gère les candidatures reçues.
- Un **admin** supervise l'ensemble des utilisateurs et des offres.

---

## Technologies utilisées

| Couche          | Technologie             |
|-----------------|-------------------------|
| Langage         | PHP 8.x                 |
| Framework       | Laravel 10.x            |
| Authentification| JWT (`tymon/jwt-auth`)  |
| Base de données | MySQL                   |
| Tests           | Postman                 |

---

## Prérequis

- PHP >= 8.1
- Composer
- MySQL
- Laravel CLI

---

## Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/projet-backend/Mini_LinkedIn.git
cd Mini_LinkedIn

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env
# Modifier DB_DATABASE, DB_USERNAME, DB_PASSWORD dans le fichier .env

# 4. Générer la clé de l'application
php artisan key:generate

# 5. Générer le secret JWT
php artisan jwt:secret

# 6. Exécuter les migrations et les seeders
php artisan migrate --seed

# 7. Démarrer le serveur
php artisan serve
```

URL de base de l'API : `http://127.0.0.1:8000/api`

---

## Données de test

| Rôle      | Nombre | Détails                              |
|-----------|--------|--------------------------------------|
| Admin     | 2      | Accès complet à la plateforme        |
| Recruteur | 5      | 2 à 3 offres d'emploi chacun         |
| Candidat  | 10     | Chacun avec un profil et compétences |

---

## Routes de l'API

###  Publiques

| Méthode | Endpoint        | Description              |
|---------|-----------------|--------------------------|
| POST    | `/api/register` | Créer un compte          |
| POST    | `/api/login`    | Se connecter — token JWT |

---

###  Protégées (tous les rôles — `auth:api`)

| Méthode | Endpoint      | Description             |
|---------|---------------|-------------------------|
| POST    | `/api/logout` | Invalider le token JWT  |

---

###  Candidat uniquement (`role:candidat`)

| Méthode | Endpoint                                  | Description                        |
|---------|-------------------------------------------|------------------------------------|
| POST    | `/api/profil`                             | Créer son profil (une seule fois)  |
| GET     | `/api/profil`                             | Consulter son propre profil        |
| PUT     | `/api/profil`                             | Modifier son profil                |
| POST    | `/api/profil/competences`                 | Ajouter une compétence avec niveau |
| DELETE  | `/api/profil/competences/{competence}`    | Retirer une compétence             |
| POST    | `/api/offres/{offre}/candidater`          | Postuler à une offre               |
| GET     | `/api/mes-candidatures`                   | Consulter ses candidatures         |

---

###  Offres d'emploi — tous les utilisateurs authentifiés

| Méthode | Endpoint              | Description                                  |
|---------|-----------------------|----------------------------------------------|
| GET     | `/api/offres`         | Liste des offres actives (paginée, filtrée)  |
| GET     | `/api/offres/{offre}` | Détail d'une offre                           |

> Filtres disponibles : `?localisation=Casablanca&type=CDI`  
> Pagination : 10 résultats par page — triés par `created_at` décroissant.

---

###  Recruteur uniquement (`role:recruteur`)

| Méthode | Endpoint                                   | Description                          |
|---------|--------------------------------------------|--------------------------------------|
| POST    | `/api/offres`                              | Créer une offre                      |
| PUT     | `/api/offres/{offre}`                      | Modifier son offre                   |
| DELETE  | `/api/offres/{offre}`                      | Supprimer son offre                  |
| GET     | `/api/offres/{offre}/candidatures`         | Voir les candidatures reçues         |
| PATCH   | `/api/candidatures/{candidature}/statut`   | Changer le statut d'une candidature  |

---

###  Admin uniquement (`role:admin` — préfixe `/admin`)

| Méthode | Endpoint                        | Description                      |
|---------|---------------------------------|----------------------------------|
| GET     | `/api/admin/users`              | Liste de tous les utilisateurs   |
| DELETE  | `/api/admin/users/{user}`       | Supprimer un compte utilisateur  |
| PATCH   | `/api/admin/offres/{offre}`     | Activer / désactiver une offre   |

---

## Règles d'autorisation

- Toutes les routes sauf `/register` et `/login` nécessitent un JWT valide → `401` sinon
- Toute violation de rôle retourne `403 Forbidden`
- Un recruteur ne peut modifier ou supprimer que **ses propres** offres
- Un candidat ne peut consulter que **ses propres** candidatures
- L'autorisation par rôle est gérée via le middleware personnalisé `RoleMiddleware`

---

## Events & Listeners

Le système d'Events & Listeners de Laravel est utilisé pour découpler 
la logique métier des contrôleurs.

| Événement              | Déclenché quand                            | Listener                  | Action                                                        |
|------------------------|--------------------------------------------|---------------------------|---------------------------------------------------------------|
| `CandidatureDeposee`   | Un candidat postule à une offre            | `LogCandidatureDeposee`   | Enregistre la date, le nom du candidat et le titre de l'offre |
| `StatutCandidatureMis` | Un recruteur change le statut              | `LogStatutCandidatureMis` | Enregistre l'ancien statut, le nouveau statut et la date      |

Fichier de log : `storage/logs/candidatures.log`

---

## Collection Postman

Disponible dans `/postman/mini_linkedin.json`.

Couvre : inscription, connexion (tous les rôles), CRUD profil, CRUD offres,
candidature, changement de statut, actions admin,
et les cas d'erreur : `401`, `403`, `422`.

---

## Structure du projet

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

## Auteurs

Projet réalisé par :
- **El Hamoudi Wiam**
- **Biby Maryam**
- **El Maaroufi Soukaina**

ENSAM Casablanca — Département Génie Informatique et IA  
Encadré par : **WARDI Ahmed**
