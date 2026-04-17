# 🧠 Customer Review Analyzer

> Plateforme d'analyse automatique d'avis clients —  Laravel/IA | ECE Paris 2025/2026

[![Laravel](https://img.shields.io/badge/Backend-Laravel%2012-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Vue 3](https://img.shields.io/badge/Frontend-Vue%203-42b883?style=flat-square&logo=vue.js)](https://vuejs.org)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue?style=flat-square)](LICENSE)

---

## 👥 Équipe

| Nom | Rôle |
|-----|------|
| **Youssef** | Backend Laravel & Service IA & frontend Vue 3 |
| **Ludkas** | Authentification & Gestion des rôles & Frontend Vue 3 |

---

## 🎯 Objectif

Permettre à des entreprises d'analyser automatiquement les avis de leurs clients grâce à une API REST Laravel couplée à un moteur NLP. Chaque avis soumis est automatiquement classifié, scoré et catégorisé par thème.

---

## 🏗️ Architecture

```
Frontend (Vue 3)  →  API REST (Laravel 12)  →  Service IA (NLP)
                                           →  Base de données (MySQL)
```

---

## ✨ Fonctionnalités

- **Authentification** — Inscription, login, gestion des rôles (admin / user)
- **Gestion des avis** — CRUD complet via API REST
- **Analyse IA** — Sentiment, score de satisfaction (0–100), détection de thèmes
- **Dashboard** — Statistiques globales en temps réel

---

## 📡 Endpoints principaux

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| `POST` | `/api/register` | Inscription |
| `POST` | `/api/login` | Connexion |
| `GET` | `/api/reviews` | Liste des avis |
| `POST` | `/api/reviews` | Soumettre un avis |
| `PUT` | `/api/reviews/{id}` | Modifier un avis |
| `DELETE` | `/api/reviews/{id}` | Supprimer un avis |
| `POST` | `/api/analyze` | Analyser un texte |
| `GET` | `/api/dashboard` | Statistiques globales |

**Exemple — Analyse IA :**
```json
// POST /api/analyze
{ "text": "Livraison rapide, très satisfait du produit !" }

// Réponse
{
  "sentiment": "positive",
  "score": 88,
  "topics": ["livraison", "qualité"]
}
```

---

## 🚀 Lancer le projet

```bash
# Backend
cd backend
composer install
cp .env.example .env
php artisan key:generate && php artisan migrate
php artisan serve

# Frontend
cd frontend
npm install && npm run dev
```

---

## 📁 Structure du projet

```
├── backend/      → API Laravel 12 + Service IA
├── frontend/     → SPA Vue 3
├── docs/         → Documentation & rapport
└── README.md
```

---

*ECE Paris-2025/2026*
