# SAE 504 — Génération et Publication Automatisée d’Articles

## Description du projet

Ce projet vise à automatiser la sélection, la génération, l’évaluation et la publication de contenus basés sur des flux RSS, grâce à l’utilisation d’un LLM (Gemini).
Il permet également de créer des contre-articles (« debunk »), de générer des images, et de publier automatiquement sur X (Twitter).

## Fonctionnalités principales :

### Requêtes LLM Gemini
Le système génère automatiquement un pipeline complet autour d’articles sélectionnés via RSS :

- Sélection automatique d’un article via un flux RSS
- Réécriture “ragebait sourcé” d’un article
- Auto-évaluation selon des critères prédéfinis et régénération si nécessaire
- Création d’un contre-article “debunk”
- Génération d’une image pour illustrer l’article
- Génération d’un post pour X
- Stockage complet en base MySQL :
    - Articles sélectionnés
    - Articles générés
    - Contre-articles “debunk”
- Système de logs pour tracer l’ensemble du processus

### Automatisation & Scheduler
Cron / Service externe :
Un scheduler automatisé permet de générer de nouveaux articles chaque heure (modifiable).
Le projet peut s’intégrer avec un cron système ou un service externe (ex : Laravel Forge, Ploi…).

### Publication sur X
- Intégration avec l’API Twitter/X
- Génération automatique d’un post via réécriture de l’article
- Publication automatisée synchronisée avec le scheduler

### Tests unitaires
- Tests réalisés avec Pest
- Pas de mocks (tests réels)
- Certaines fonctionnalités manquent encore de tests, mais les principales sont couvertes

## nstallation & Configuration
1. Cloner le projet
```bash
git clone <REPO>
cd <NOM_DU_PROJET>
```

2. Installer les dépendances Laravel

```bash
composer install
```

3. Configuration de l’environnement
Copiez le fichier d’exemple :
```bash
cp .env.example .env
```

Puis configurez vos variables :
- Clé API LLM (Gemini)
- Accès base MySQL
- Clé API de X (Twitter API v2)
Générez ensuite la clé d'application :
```bash
php artisan key:generate
```
4. Base de données
```bash
php artisan migrate
```

5. Middleware API (optionnel)
Le middleware basé sur API_KEY est désactivé par défaut.
Pour l’activer, décommentez la ligne correspondante dans :
```bash
/routes/web.php
```
Et configurez :
```bash
API_KEY=VOTRE_CLE_API_PERSO
```

6. Scheduler
Pour activer l’automatisation :
Ajoutez par exemple dans votre crontab :
```bash
* * * * * php /chemin/vers/projet/artisan schedule:run >> /dev/null 2>&1
```

---

## Technologies utilisées

- Laravel (backend)
- Blade (frontend)
- MySQL
- Gemini API
- Twitter/X API
- Pest (tests)
