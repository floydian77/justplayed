# JustPlayed

Import your collection from discogs and scrobble your CD's, LP's, to last.fm

Offline scrobbler.

## Installation

### Laravel

```bash
$ cp .env.example .env
$ php artisan key:generate
$ php artisan migrate
```

### Last.fm

A last.fm api key is required, get one here: https://www.last.fm/api

Save them in `.env`

```
LASTFM_API_KEY=my_api_key
LASTFM_SECRET=my_secret
```

## Setup development environment

### Docker

#### Permissions

```bash
$ chmod -R 755 .
$ chmod -R 1777 storage
```

#### Install dependencies

```bash
$ cd docker
$ docker run --rm -v $(pwd):/app composer install
```

#### Start services

```bash
$ docker-compose up -d
$ docker-compose exec app bash
$ cd docker
$ ./build-ssl-cert.sh
```

#### Composer

How to install composer globally in container.
For step 3, see https://getcomposer.org/download/ for the current hash.

```bash
$ cd ~
$ curl -sS https://getcomposer.org/installer -o composer-setup.php
$ php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
$ php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

#### Rebuild

```bash
$ docker-compose up -d --build
```