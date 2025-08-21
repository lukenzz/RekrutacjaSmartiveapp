# Image Thumbnail Generator - Symfony 7

This project is a Symfony 7 application that allows generating thumbnails from images stored on disk or remote storage (FTP, Dropbox, S3). The application is built with best practices in mind, including asynchronous processing with Symfony Messenger, logging with Monolog, and adherence to PSR coding standards.

---

## Features

- Generate thumbnails from local images.
- Support multiple storage backends:
    - Local filesystem
    - FTP
    - Dropbox
    - Amazon S3
- Asynchronous processing of thumbnail generation using Symfony Messenger.
- Retry strategy for failed tasks.
- Logging with Monolog.
- Configurable storage backend via `.env`.
- Unit tests included (PHPUnit compatible).

---

## Setup

Start containers in the background:
```bash
docker-compose up -d
```
Services exposed outside your environment
Service	Address outside containers
Webserver	http://localhost:12345

Install composer
```bash
composer install
```

## Command
For running command use:
- first option (interact questions)
```bash
docker compose exec php-fpm php bin/console smartiveapp:generate_thumbnails
```
- second option (command with parameters)
```bash
docker compose exec php-fpm php bin/console smartiveapp:generate_thumbnails public/img/exaple.jpg -s local
```
first argument represent `path to file`

second argument `-s` or `--storge` represent one of storage (local, ftp, dropbox, s3)


## Development Tools

The project includes the following dev tools:

- **PHPStan** – static analysis for detecting potential errors:
```bash
docker compose exec php-fpm vendor/bin/phpstan analyse src
```
- **PHP CS Fixer – automatic code formatting according to PSR standards:**
```bash
docker compose exec php-fpm vendor/bin/php-cs-fixer fix
```
## Testing
Unit tests are located in src/tests and can be run with PHPUnit:
```bash
docker compose exec php-fpm vendor/bin/phpunit
```
