# justplayed

Offline scrobbler.

## Installation

### Docker

Permissions.

```sh
$ chmod -R 755 .
$ chmod -R 1777 storage
```

Install dependencies.

```sh
$ cd docker
$ docker run --rm -v $(pwd):/app composer install
```

Start services.

```sh
$ docker-compose up -d
```

Laravel.

```sh
$ docker-compose exec app bash
$ cp .env.example .env
$ php artisan key:generate
```