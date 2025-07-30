# Base Project Template

This repository is a modern Symfony project template, ready to use, with an automatic dashboard to quickly start your new projects.

## Main Features

- Built-in automatic admin dashboard
- Customizable Symfony base
- Ready-to-develop project structure

## Automatic Dashboard and Entity Management

The admin dashboard is generated automatically from your entities using the dedicated command:

```bash
php bin/console app:configure-cruds
```

This command scans your entities and configures the necessary CRUDs for the admin panel.

### cruds() Method in Your Entities

To customize the dashboard behavior, add a public static `cruds()` method in each entity:

```php
public static function cruds(): array
{
    return [
        // Example:
        'fields' => ['name', 'email', 'createdAt'],
        'labels' => ['name' => 'Name', 'email' => 'E-mail'],
        // ...
    ];
}
```

No extra configuration is needed: the dashboard adapts automatically.

## Security and Role Management

Dashboard routes are protected by default:

- `/admin` is accessible only to users with the `ROLE_ADMIN` role.
- `/superadmin` (if used) is reserved for `ROLE_SUPERADMIN`.

The security configuration is already set in `config/security.yaml`: just assign roles to your users, no need to change code or routes.

## Creating Secured Controllers

To add an admin controller, simply create it with a route starting with `/admin`:

```php
#[Route('/admin/my-module', name: 'admin_my_module')]
```

It will automatically be protected by the role system.

## Fixtures for Fast Prototyping

Fixtures are provided to pre-fill the database with sample data and speed up development:

```bash
php bin/console doctrine:fixtures:load
```

## Docker & Development Environment

The project uses an optimized Docker image, automatically generated via [generate_docker_image_symfony](https://github.com/cadot-eu/generate_docker_image_symfony). The `compose.yaml` file is ready to launch the full environment (PHP, PostgreSQL, etc.):

```bash
docker compose up -d
```

---

## Project Repository

<https://github.com/cadot-eu/base>

## Usage with Composer

To create a new project based on this template:

```bash
composer create-project cadot-eu/base your-project-name --repository='{"type":"vcs","url":"git@github.com:cadot-eu/base.git"}' dev-main
```

## Customization

- Edit configuration files as needed.
- See the Symfony documentation for more options.

## License

MIT
