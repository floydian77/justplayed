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
