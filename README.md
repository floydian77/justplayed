# JustPlayed

Import your collection from discogs and scrobble your CD's, LP's, to last.fm

## Installation

### Docker container

#### Environment variables

```bash
$ cd docker
$ cp .env.example .env
```

#### Set permissions

```bash
$ chmod -R 755 .
$ chmod -R 1777 storage
```

#### Start services

It could take a while to build the container for the first time.

```bash
$ docker-compose up -d
```

### Laravel

```bash
$ composer install

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
