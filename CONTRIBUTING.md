# Contributing

## Requirements

- PHP 8.3 or newer
- Composer 2
- Docker and Docker Compose for the full local stack

## Local Setup

```bash
cp .env.example .env
composer install
php artisan key:generate
docker compose up -d
php artisan migrate --seed
php artisan passport:keys
```

For the Sail wrapper, use `sail` instead of `docker compose` and `php artisan` after the container is running.

## Quality Checks

The test suite is isolated from Docker and uses SQLite in memory. Passport test keys are generated automatically under ignored `storage/` paths.

```bash
composer test
composer phpstan
composer security:audit
composer php-cs-fixer
```

Run `git diff --check` before opening a pull request. Do not commit `.env`, `.env.testing`, OAuth keys, coverage files, or generated documentation.

## Pull Requests

Describe the behavior changed, the tests run, and any migration or environment changes. Keep API response formats and authentication guards backward compatible unless the change explicitly documents a breaking change.
