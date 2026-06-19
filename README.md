# keyce-stagiaires-backend

Backend REST PHP + MySQL pour l'épreuve de Développement Web — **JavaScript avec React** (Session Rattrapage 2025-2026).

> **⚠ NE PAS MODIFIER CE DÉPÔT** — Les étudiants doivent uniquement cloner ce dépôt et créer leur frontend React.

---

## Démarrage rapide

```bash
# 1. Cloner le backend
git clone https://github.com/Legeni07/keyce-stagiaires-backend.git

# 2. Lancer avec Docker Compose
cd keyce-stagiaires-backend
docker compose up --build

# API disponible sur → http://localhost:8001/api
```

La base de données est initialisée automatiquement avec des données de démonstration.

---

## Comptes de démonstration

| Email | Mot de passe | Rôle |
|---|---|---|
| admin@keyce.cm | Keyce2025! | admin |
| responsable@keyce.cm | Keyce2025! | responsable |
| tuteur1@keyce.cm | Keyce2025! | tuteur |
| stagiaire1@keyce.cm | Keyce2025! | stagiaire |

---

## Endpoints API

### Authentification
| Méthode | Endpoint | Description |
|---|---|---|
| POST | `/api/auth/login` | Connexion → JWT 24h |
| GET | `/api/auth/me` | Profil de l'utilisateur connecté |

### Étudiants
| Méthode | Endpoint | Paramètres | Accès |
|---|---|---|---|
| GET | `/api/etudiants` | `?search=&filiere=&niveau=&page=&per_page=` | admin, responsable, tuteur |
| POST | `/api/etudiants` | body JSON | admin, responsable |
| GET | `/api/etudiants/{id}` | — | admin, responsable, tuteur |
| PUT | `/api/etudiants/{id}` | body JSON | admin, responsable |
| DELETE | `/api/etudiants/{id}` | — | admin |
| POST | `/api/etudiants/import` | `{ rows: [...] }` | admin, responsable |

### Entreprises
| Méthode | Endpoint | Paramètres |
|---|---|---|
| GET | `/api/entreprises` | `?search=&secteur=&partenaire=0\|1&page=` |
| POST | `/api/entreprises` | body JSON |
| GET | `/api/entreprises/{id}` | — |
| PUT | `/api/entreprises/{id}` | body JSON |
| DELETE | `/api/entreprises/{id}` | — |

### Tuteurs
| Méthode | Endpoint | Paramètres |
|---|---|---|
| GET | `/api/tuteurs` | `?search=&type_tuteur=academique\|entreprise` |
| POST | `/api/tuteurs` | body JSON |
| GET | `/api/tuteurs/{id}` | — |
| PUT | `/api/tuteurs/{id}` | body JSON |
| DELETE | `/api/tuteurs/{id}` | — |

### Stages
| Méthode | Endpoint | Paramètres |
|---|---|---|
| GET | `/api/stages` | `?statut=&type_stage=&filiere=&entreprise_id=&etudiant_id=&search=` |
| POST | `/api/stages` | body JSON |
| GET | `/api/stages/{id}` | — (inclut visites, évaluations, documents) |
| PUT | `/api/stages/{id}` | body JSON |
| DELETE | `/api/stages/{id}` | — |
| PATCH | `/api/stages/{id}/statut` | `{ "statut": "en_cours" }` |
| PATCH | `/api/stages/{id}/convention` | `{ "convention_signee": 1 }` |

Statuts valides : `en_attente` → `en_cours` → `termine` | `abandonne`

### Visites
| Méthode | Endpoint | Paramètres |
|---|---|---|
| GET | `/api/visites` | `?stage_id=&tuteur_id=&type=` |
| POST | `/api/visites` | body JSON |
| GET | `/api/visites/{id}` | — |
| PUT | `/api/visites/{id}` | body JSON |
| DELETE | `/api/visites/{id}` | — |

### Évaluations
| Méthode | Endpoint | Paramètres |
|---|---|---|
| GET | `/api/evaluations` | `?stage_id=&evaluateur_type=academique\|entreprise` |
| POST | `/api/evaluations` | body JSON |
| GET | `/api/evaluations/{id}` | — |
| PUT | `/api/evaluations/{id}` | body JSON |
| DELETE | `/api/evaluations/{id}` | — |

### Documents
| Méthode | Endpoint | Description |
|---|---|---|
| GET | `/api/documents` | `?stage_id=&statut=&type=` |
| POST | `/api/documents/upload` | `multipart/form-data` avec champ `fichier` + `stage_id` + `type` |
| PATCH | `/api/documents/{id}` | `{ "statut": "valide" }` — admin/responsable |
| DELETE | `/api/documents/{id}` | — |

### Statistiques & Vue stagiaire
| Méthode | Endpoint | Description |
|---|---|---|
| GET | `/api/stats` | Dashboard global (admin/responsable/tuteur) |
| GET | `/api/stagiaires/mon-stage` | Stage actif du stagiaire connecté + progression |

---

## Format d'une requête authentifiée

```javascript
// Axios — header JWT obligatoire sur tous les endpoints sauf /api/auth/login
headers: {
  'Authorization': `Bearer ${token}`,
  'Content-Type': 'application/json'
}
```

---

## Clés localStorage attendues (côté frontend étudiant)

| Clé | Exemple | Description |
|---|---|---|
| `keyce_token` | `"eyJ..."` | JWT de session |
| `keyce_user` | `{id,nom,role,...}` | Profil utilisateur |

---

## Structure du projet

```
keyce-stagiaires-backend/
├── index.php               ← Routeur principal
├── Dockerfile
├── docker-compose.yml
├── .htaccess
├── config/
│   └── database.php        ← Config PDO + constantes
├── middleware/
│   ├── auth.php            ← JWT encode/decode + requireAuth()
│   └── cors.php            ← CORS + helpers JSON
├── api/
│   ├── auth/               login.php, me.php
│   ├── etudiants/          list, create, show, update, delete, import
│   ├── entreprises/        list, create, show, update, delete
│   ├── tuteurs/            list, create, show, update, delete
│   ├── stages/             list, create, show, update, delete, statut, convention
│   ├── visites/            list, create, show, update, delete
│   ├── evaluations/        list, create, show, update, delete
│   ├── documents/          list, upload, statut, delete
│   ├── stats/              index
│   └── stagiaires/         mon_stage
├── database/
│   └── migrations.sql      ← Schéma + données de démo
└── uploads/                ← Fichiers uploadés (gitignored)
```

---

## Variables d'environnement Docker

| Variable | Valeur par défaut |
|---|---|
| `DB_HOST` | `db` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `keyce_stagiaires` |
| `DB_USER` | `keyce` |
| `DB_PASS` | `keyce2025` |
| `JWT_SECRET` | `keyce_secret_jwt_2025_exam_b2` |

---

## Stack technique

- **PHP 8.2** + Apache (php:8.2-apache)
- **MySQL 8.0**
- **JWT HS256** implémenté nativement (sans dépendance Composer)
- **PDO** pour toutes les requêtes SQL
- **Docker Compose** pour l'orchestration

---

*Keyce Academy — Examinateur : BOGNI-DANCHI — 2025-2026*
