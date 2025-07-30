# Base Project Template

Ce dépôt est un template de projet Symfony moderne, prêt à l'emploi, avec dashboard automatique pour démarrer rapidement vos nouveaux projets.

## Fonctionnalités principales

- Dashboard automatique intégré
- Base Symfony personnalisable
- Structure prête pour le développement

## Dashboard automatique et gestion des entités

Le dashboard admin est généré automatiquement à partir de vos entités grâce à la commande dédiée :

```bash
php bin/console app:configure-cruds
```

Cette commande scanne vos entités et configure automatiquement les CRUDs nécessaires pour l’admin.

### Méthode cruds() dans vos entités

Pour personnaliser le comportement du dashboard, ajoutez une méthode publique `cruds()` dans chaque entité :

```php
public static function cruds(): array
{
    return [
        // Exemple :
        'fields' => ['nom', 'email', 'dateCreation'],
        'labels' => ['nom' => 'Nom', 'email' => 'E-mail'],
        // ...
    ];
}
```

Aucune configuration supplémentaire n’est nécessaire : le dashboard s’adapte automatiquement.

## Sécurité et gestion des rôles

Les routes du dashboard sont protégées par défaut :
- `/admin` est accessible uniquement aux utilisateurs ayant le rôle `ROLE_ADMIN`.
- `/superadmin` (si utilisé) est réservé à `ROLE_SUPERADMIN`.

La configuration de sécurité est déjà prête dans `config/security.yaml` : il suffit d’attribuer les rôles à vos utilisateurs, sans modifier le code ou les routes.

## Création de contrôleurs sécurisés

Pour ajouter un contrôleur d’administration, créez-le simplement avec une route commençant par `/admin` :

```php
#[Route('/admin/mon-module', name: 'admin_mon_module')]
```

Il sera automatiquement protégé par le système de rôles.

## Fixtures pour gagner du temps

Des fixtures sont fournies pour pré-remplir la base de données avec des exemples de données et accélérer le développement :

```bash
php bin/console doctrine:fixtures:load
```

## Docker et environnement de développement

Le projet utilise une image Docker optimisée, générée automatiquement via [generate_docker_image_symfony](https://github.com/cadot-eu/generate_docker_image_symfony). Le fichier `compose.yaml` est prêt à l’emploi pour lancer l’environnement complet (PHP, PostgreSQL, etc.) :

```bash
docker compose up -d
```

---

## Dépôt du projet

<https://github.com/cadot-eu/base>

## Utilisation avec Composer

Pour créer un nouveau projet basé sur ce template :

```bash
composer create-project cadot-eu/base nom-du-projet --repository='{"type":"vcs","url":"git@github.com:cadot-eu/base.git"}' dev-main
```

## Personnalisation

- Modifiez les fichiers de configuration selon vos besoins.
- Consultez la documentation Symfony pour plus d'options.

## Licence

MIT
