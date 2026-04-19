# API Documentation — Customer Review Analyzer

> **Version** 1.0.0 — Laravel 12 · Sanctum JWT · SQLite  
> **Base URL** `http://127.0.0.1:8000/api`  
> **Authentication** Bearer Token (Laravel Sanctum)

---

## Overview

The Customer Review Analyzer exposes a fully stateless REST API built on Laravel 12. Every request and response is JSON-encoded. Protected routes require a valid Bearer token obtained via `/api/login` or `/api/register`.

**Content negotiation** — always include the following headers:

```http
Accept: application/json
Content-Type: application/json
```

**Authentication header** — required on all protected routes:

```http
Authorization: Bearer {token}
```

---

## Authentication

### Register

Creates a new user account and returns an access token.

```http
POST /api/register
```

**Request body**

```json
{
  "name": "Youssef",
  "email": "youssef@example.com",
  "password": "password123"
}
```

**Validation rules**

| Field | Rules |
|-------|-------|
| `name` | required, string, max 255 |
| `email` | required, valid email, unique |
| `password` | required, string, min 6 |

**Response `201 Created`**

```json
{
  "user": {
    "id": 2,
    "name": "Youssef",
    "email": "youssef@example.com",
    "role": "user",
    "created_at": "2026-04-19T19:49:24.000000Z",
    "updated_at": "2026-04-19T19:49:24.000000Z"
  },
  "token": "1|Bjj1cltT8m0rP24OevJKBzZztMtRD15qpBkdIJYLbc633556"
}
```

---

### Login

Authenticates a user and returns a fresh access token.

```http
POST /api/login
```

**Request body**

```json
{
  "email": "youssef@example.com",
  "password": "password123"
}
```

**Response `200 OK`**

```json
{
  "user": {
    "id": 2,
    "name": "Youssef",
    "email": "youssef@example.com",
    "role": "user",
    "created_at": "2026-04-19T19:49:24.000000Z",
    "updated_at": "2026-04-19T19:49:24.000000Z"
  },
  "token": "3|yORooMGxXR0LeymCQMot7jMleGliLZQCjIsQPOSu3fcde894"
}
```

**Response `401 Unauthorized`**

```json
{
  "message": "Identifiants invalides."
}
```

---

### Logout

Revokes the current access token.

```http
POST /api/logout
```

🔒 **Requires authentication**

**Response `200 OK`**

```json
{
  "message": "Déconnecté avec succès."
}
```

---

## Reviews

### List Reviews

Returns a paginated list of all reviews, sorted by most recent.

```http
GET /api/reviews
```

🔒 **Requires authentication**

**Response `200 OK`**

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "user_id": 2,
      "content": "Livraison très rapide, produit de qualité excellente, je recommande !",
      "sentiment": "positive",
      "score": 70,
      "topics": ["livraison", "qualité", "rapidité"],
      "created_at": "2026-04-19T19:53:28.000000Z",
      "updated_at": "2026-04-19T19:53:28.000000Z",
      "user": {
        "id": 2,
        "name": "Youssef"
      }
    }
  ],
  "per_page": 10,
  "total": 1,
  "last_page": 1
}
```

---

### Create Review

Submits a new review. The NLP analysis pipeline is triggered **automatically** at creation time — sentiment, score, and topics are computed and stored synchronously.

```http
POST /api/reviews
```

🔒 **Requires authentication**

**Request body**

```json
{
  "content": "Livraison très rapide, produit de qualité excellente, je recommande !"
}
```

**Validation rules**

| Field | Rules |
|-------|-------|
| `content` | required, string, min 5 |

**Response `201 Created`**

```json
{
  "id": 1,
  "user_id": 2,
  "content": "Livraison très rapide, produit de qualité excellente, je recommande !",
  "sentiment": "positive",
  "score": 70,
  "topics": ["livraison", "qualité", "rapidité"],
  "created_at": "2026-04-19T19:53:28.000000Z",
  "updated_at": "2026-04-19T19:53:28.000000Z"
}
```

---

### Get Review

Fetches a single review by its ID.

```http
GET /api/reviews/{id}
```

🔒 **Requires authentication**

**Response `200 OK`**

```json
{
  "id": 1,
  "user_id": 2,
  "content": "Livraison très rapide, produit de qualité excellente, je recommande !",
  "sentiment": "positive",
  "score": 70,
  "topics": ["livraison", "qualité", "rapidité"],
  "created_at": "2026-04-19T19:53:28.000000Z",
  "updated_at": "2026-04-19T19:53:28.000000Z",
  "user": {
    "id": 2,
    "name": "Youssef"
  }
}
```

---

### Update Review

Updates the content of an existing review. The NLP analysis is **re-triggered automatically** to reflect the new content.

```http
PUT /api/reviews/{id}
```

🔒 **Requires authentication** — author or admin only

**Request body**

```json
{
  "content": "Finalement très satisfait, le service client a résolu mon problème rapidement."
}
```

**Response `200 OK`** — returns the updated review with fresh AI analysis.

---

### Delete Review

Permanently removes a review from the system.

```http
DELETE /api/reviews/{id}
```

🔒 **Requires authentication** — author or admin only

**Response `200 OK`**

```json
{
  "message": "Avis supprimé."
}
```

**Response `403 Forbidden`** — if the user is neither the author nor an admin.

---

## AI Inference

### Analyze Text

Runs the NLP inference pipeline on arbitrary text without persisting any data. Useful for previewing analysis results before submission.

```http
POST /api/analyze
```

🔒 **Requires authentication**

**Request body**

```json
{
  "text": "Service client horrible, délai trop long, je suis très déçu !"
}
```

**Response `200 OK`**

```json
{
  "sentiment": "negative",
  "score": 40,
  "topics": ["livraison", "service"]
}
```

**Sentiment values**

| Value | Meaning |
|-------|---------|
| `positive` | More positive signals than negative |
| `neutral` | Balanced or no detectable signal |
| `negative` | More negative signals than positive |

**Score range** — `0` (extremely negative) to `100` (extremely positive). Baseline is `50` (neutral), adjusted by lexical polarity, text length, and punctuation patterns.

**Detectable topics**

| Topic | Trigger keywords (FR / EN) |
|-------|---------------------------|
| `livraison` | livraison, délai, expédition, colis, delivery, shipping |
| `qualité` | qualité, matière, solide, durable, quality, material |
| `prix` | prix, cher, coût, valeur, price, cost, expensive |
| `service` | service, support, assistance, réponse, help, staff |
| `rapidité` | rapide, vite, immédiat, fast, quick, speed |

---

## Dashboard

### Global Statistics

Returns aggregated platform-level analytics computed in real time across the full review corpus.

```http
GET /api/dashboard
```

🔒 **Requires authentication**

**Response `200 OK`**

```json
{
  "total_reviews": 1,
  "average_score": 70,
  "sentiment_breakdown": {
    "positive": 100.0,
    "neutral": 0.0,
    "negative": 0.0
  },
  "top_topics": [
    "livraison",
    "qualité",
    "rapidité"
  ],
  "recent_reviews": [
    {
      "id": 1,
      "user_id": 2,
      "content": "Livraison très rapide, produit de qualité excellente, je recommande !",
      "sentiment": "positive",
      "score": 70,
      "topics": ["livraison", "qualité", "rapidité"],
      "created_at": "2026-04-19T19:53:28.000000Z",
      "updated_at": "2026-04-19T19:53:28.000000Z",
      "user": {
        "id": 2,
        "name": "Youssef"
      }
    }
  ]
}
```

**Response fields**

| Field | Type | Description |
|-------|------|-------------|
| `total_reviews` | integer | Total number of reviews in the system |
| `average_score` | float | Mean satisfaction score across all reviews |
| `sentiment_breakdown` | object | Percentage distribution of sentiment labels |
| `top_topics` | array | Top 3 most frequently detected topics |
| `recent_reviews` | array | 5 most recently submitted reviews |

---

## Error Reference

| HTTP Status | Meaning |
|-------------|---------|
| `200 OK` | Request succeeded |
| `201 Created` | Resource successfully created |
| `401 Unauthorized` | Missing or invalid token |
| `403 Forbidden` | Authenticated but insufficient permissions |
| `422 Unprocessable Entity` | Validation failed |
| `500 Internal Server Error` | Server-side error |

**Validation error format**

```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

---

## Data Model

```sql
users
├── id              INTEGER PRIMARY KEY
├── name            VARCHAR(255)
├── email           VARCHAR(255) UNIQUE
├── password        VARCHAR(255)
├── role            ENUM('admin', 'user') DEFAULT 'user'
└── timestamps

reviews
├── id              INTEGER PRIMARY KEY
├── user_id         INTEGER (FK → users.id)
├── content         TEXT
├── sentiment       ENUM('positive', 'neutral', 'negative')
├── score           TINYINT (0–100)
├── topics          JSON
└── timestamps

personal_access_tokens
├── id              INTEGER PRIMARY KEY
├── tokenable_type  VARCHAR
├── tokenable_id    INTEGER
├── name            VARCHAR
├── token           VARCHAR(64) UNIQUE
├── abilities       TEXT
├── last_used_at    TIMESTAMP
└── expires_at      TIMESTAMP
```

---

*Customer Review Analyzer — B3 Développeur en Intelligence Artificielle — ECE Paris 2025/2026*
