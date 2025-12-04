# 🏠 API Immo - Catalogue d'Appartements

![Tests](https://github.com/ethan-hgt/api-immo-tp/actions/workflows/tests.yml/badge.svg)
![Coverage](https://img.shields.io/badge/coverage-check%20CI-brightgreen)

Une API REST moderne pour la gestion d'un catalogue d'appartements avec authentification JWT et notifications par email.

## 👥 Contributeurs

- **Ethan HUGEROT** - [@ethan-hgt](https://github.com/ethan-hgt)
- **Basile PARRAIN** - [@4keezix](https://github.com/4keezix)

## 🚀 Technologies

- **PHP 8.4**
- **Symfony 7.3**
- **API Platform 4.0**
- **Doctrine ORM**
- **Lexik JWT Authentication**
- **Symfony Mailer + MailHog**
- **SQLite**
- **Docker & Docker Compose**
- **PHPUnit 12**

## 📋 Fonctionnalités

- ✅ **CRUD Appartements** : Gestion complète des appartements (titre, description, prix, surface)
- ✅ **Authentification JWT** : Login sécurisé avec tokens JWT
- ✅ **Email de bienvenue** : Envoi automatique d'un email lors de l'inscription
- ✅ **Tests automatisés** : Tests fonctionnels avec PHPUnit
- ✅ **API REST** : Documentation Swagger/OpenAPI intégrée
- ✅ **Docker** : Environnement de développement conteneurisé

## 🔧 Installation et utilisation en local

### Prérequis

- Docker Desktop installé et en cours d'exécution
- Git

### Installation

```bash
# 1. Cloner le repository
git clone https://github.com/ethan-hgt/api-immo-tp.git
cd api-immo-tp

# 2. Démarrer les containers Docker
docker compose up -d --build

# 3. Attendre que les services démarrent (5-10 secondes)
# Vérifier que les containers sont en cours d'exécution
docker compose ps

# 4. Installer les dépendances PHP (si nécessaire)
docker compose exec php composer install

# 5. Configurer les secrets locaux (IMPORTANT pour la sécurité)
# Copier le fichier d'exemple et générer un nouveau secret
cp symfony/.env.dev.example symfony/.env.local
# Générer un nouveau APP_SECRET
docker compose exec php php -r "echo 'APP_SECRET=' . bin2hex(random_bytes(16)) . PHP_EOL;" >> symfony/.env.local

# 6. Créer les clés JWT (déjà présentes dans le repo)
# Si besoin de régénérer :
docker compose exec php openssl genrsa -out config/jwt/private.pem 4096
docker compose exec php openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

# 7. L'API est maintenant accessible sur http://localhost:8000
```

### Accéder à l'API

- **API** : http://localhost:8000
- **Documentation Swagger** : http://localhost:8000/api/docs
- **MailHog (interface email)** : http://localhost:8025

### Compte de test

Pour tester l'authentification, utilisez :
- **Email** : `correction`
- **Mot de passe** : `correction`

## 🗄️ Initialisation de la base de données

### Créer le schéma de la base de données

```bash
# En environnement de développement
docker compose exec php php bin/console doctrine:schema:create

# En environnement de test
docker compose exec php php bin/console doctrine:schema:create --env=test
```

### Charger les données de test (Seeders/Fixtures)

```bash
# Charger les fixtures en dev
docker compose exec php php bin/console doctrine:fixtures:load

# Charger les fixtures en test
docker compose exec php php bin/console doctrine:fixtures:load --env=test
```

**Données créées :**
- 10 appartements avec des données aléatoires
- 1 utilisateur de test : `correction` / `correction`

### Reset complet de la base

```bash
# Supprimer et recréer la base
docker compose exec php php bin/console doctrine:schema:drop --force
docker compose exec php php bin/console doctrine:schema:create
docker compose exec php php bin/console doctrine:fixtures:load -n
```

## 🧪 Exécution des tests

### Initialiser l'environnement de test

```bash
# 1. Créer le schéma de la base de test
docker compose exec php php bin/console doctrine:schema:create --env=test

# 2. Charger les fixtures de test
docker compose exec php php bin/console doctrine:fixtures:load --env=test -n
```

### Lancer tous les tests

```bash
docker compose exec php vendor/bin/phpunit
```

### Lancer un test spécifique

```bash
# Par nom de classe
docker compose exec php vendor/bin/phpunit tests/AppartementTest.php

# Par nom de méthode
docker compose exec php vendor/bin/phpunit --filter testGetAppartements
```

### Tests avec couverture de code

```bash
# Avec PCOV (plus rapide)
docker compose exec php vendor/bin/phpunit --coverage-text

# Générer un rapport HTML
docker compose exec php vendor/bin/phpunit --coverage-html coverage
```

### Résultats attendus

```
PHPUnit 12.4.0 by Sebastian Bergmann and contributors.

.....                                                               5 / 5 (100%)

Time: 00:02.985, Memory: 73.50 MB

OK (5 tests, 11 assertions)
```

## 📚 Structure du projet

```
api-immo-tp/
├── docker-compose.yml          # Configuration Docker
├── Dockerfile                  # Image PHP 8.4 FPM Alpine
├── symfony/
│   ├── config/
│   │   ├── packages/           # Configuration Symfony
│   │   │   ├── mailer.yaml
│   │   │   ├── messenger.yaml
│   │   │   ├── security.yaml
│   │   │   └── test/           # Config spécifique aux tests
│   │   └── jwt/                # Clés JWT
│   ├── src/
│   │   ├── Entity/
│   │   │   ├── Appartement.php # Entité Appartement
│   │   │   └── User.php        # Entité Utilisateur
│   │   ├── Repository/
│   │   ├── DataFixtures/       # Seeders
│   │   ├── EventSubscriber/
│   │   │   └── UserWelcomeSubscriber.php
│   │   └── State/
│   │       └── UserProcessor.php  # Gestion création utilisateur
│   ├── tests/
│   │   ├── AppartementTest.php
│   │   └── AuthenticationTest.php
│   └── var/
│       └── data.db3            # Base SQLite (dev)
└── README.md
```

## 🔐 Utilisation de l'API

### 1. Créer un utilisateur

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/ld+json" \
  -d '{
    "email": "user@example.com",
    "password": "password123",
    "roles": ["ROLE_USER"]
  }'
```

➡️ **Un email de bienvenue est automatiquement envoyé** (visible dans MailHog : http://localhost:8025)

### 2. Se connecter et obtenir un token JWT

```bash
curl -X POST http://localhost:8000/api/login_check \
  -H "Content-Type: application/json" \
  -d '{
    "username": "correction",
    "password": "correction"
  }'
```

Réponse :
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

### 3. Lister les appartements (authentifié)

```bash
curl -X GET http://localhost:8000/api/appartements \
  -H "Authorization: Bearer <votre_token>"
```

### 4. Créer un appartement (authentifié)

```bash
curl -X POST http://localhost:8000/api/appartements \
  -H "Authorization: Bearer <votre_token>" \
  -H "Content-Type: application/ld+json" \
  -d '{
    "titre": "Studio lumineux",
    "description": "Proche métro",
    "prix": 650,
    "surface": 25
  }'
```

## 📧 Configuration Email (MailHog)

MailHog est configuré pour intercepter tous les emails en développement :

- **SMTP** : `mailhog:1025` (dans Docker)
- **Interface web** : http://localhost:8025
- **Mode** : Synchrone (pas de queue Messenger)

### Tester l'envoi d'email manuellement

```bash
docker compose exec php php bin/console mailer:test test@example.com
```

## 🎯 Points bonus implémentés

### ✅ Documentation Swagger (0.5 pts)

API Platform génère automatiquement la documentation OpenAPI/Swagger :
👉 http://localhost:8000/api/docs

### ✅ Formateur automatique PHP-CS-Fixer (0.5 pts)

Configuration complète de PHP-CS-Fixer avec :
- Règles PSR-12 et Symfony
- Format automatique à la sauvegarde dans VSCode
- Vérification dans le pipeline CI/CD

**Utilisation :**
```bash
# Vérifier le formatage
docker compose exec php vendor/bin/php-cs-fixer fix --dry-run --diff

# Appliquer le formatage
docker compose exec php vendor/bin/php-cs-fixer fix
```

**Configuration VSCode :**
1. Installer l'extension `junstyle.php-cs-fixer`
2. Les fichiers `.vscode/settings.json` et `.vscode/extensions.json` sont déjà configurés
3. Le formatage se fait automatiquement à la sauvegarde

### ✅ Pipeline CI/CD GitHub Actions (0.5 pts)

- Tests automatiques sur chaque push
- Vérification du formatage du code (PHP-CS-Fixer)
- Badge de statut dans le README

### ⏳ Coverage Badge (en cours)

Badge de couverture de code intégré au README.

## 🐛 Dépannage

### Les containers ne démarrent pas

```bash
# Vérifier les logs
docker compose logs php
docker compose logs mailhog

# Redémarrer complètement
docker compose down
docker compose up -d --build
```

### Erreur "JWT Token not found"

```bash
# Regénérer les clés JWT
docker compose exec php sh -c "
  openssl genrsa -out config/jwt/private.pem 4096 && 
  openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
"
```

### Les tests échouent

```bash
# Recréer la base de test
docker compose exec php php bin/console doctrine:schema:drop --force --env=test
docker compose exec php php bin/console doctrine:schema:create --env=test
docker compose exec php php bin/console doctrine:fixtures:load --env=test -n

# Vider le cache de test
docker compose exec php php bin/console cache:clear --env=test
```

### Voir les logs Symfony

```bash
docker compose exec php tail -f var/log/dev.log
```

## 🔒 Sécurité

### Fichiers sensibles

⚠️ **IMPORTANT** : Les fichiers suivants ne doivent **JAMAIS** être commités dans Git :
- `symfony/.env.local` : Contient les secrets locaux
- `symfony/.env.*.local` : Fichiers d'environnement locaux
- `symfony/config/jwt/*.pem` : Clés JWT (déjà dans .gitignore)

### Bonnes pratiques

1. **Secrets en local** : Utilisez toujours `.env.local` pour vos secrets
2. **Pas de secrets hardcodés** : Jamais de mots de passe dans le code
3. **Rotation des secrets** : Changez régulièrement les secrets en production
4. **GitGuardian** : Le repo est scanné automatiquement pour détecter les fuites

## 📖 Ressources

- [Documentation Symfony](https://symfony.com/doc/current/index.html)
- [Documentation API Platform](https://api-platform.com/docs/core/)
- [Lexik JWT Authentication](https://github.com/lexik/LexikJWTAuthenticationBundle)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)

## 📄 Licence

Ce projet est réalisé dans le cadre d'un TP académique - BUT3 Informatique.

---

**🎓 Projet réalisé dans le cadre du module Qualité & Tests - BUT3 Informatique**
