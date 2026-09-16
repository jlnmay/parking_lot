# On-Site Setup — Local Deployment

This app runs entirely on-site with no dependency on internet access for
daily operation. This document is the exact sequence to get it running
from a clean machine.

## Prerequisites (assumed already installed on-site)

- PHP 8.x with the `pdo_pgsql` extension
- Composer
- Node.js + npm
- PostgreSQL, running locally

## Setup

1. Clone the repo

```bash
   git clone <repo-url> parking-lot
   cd parking-lot
```

2. Copy environment config

```bash
   cp .env.example .env
```

Edit `.env` and confirm the DB connection block matches the local
Postgres instance on this machine:

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=parking_lot
DB_USERNAME=postgres
DB_PASSWORD=
QUEUE_CONNECTION=database

3. Install PHP dependencies

```bash
   composer install
```

4. Install JS dependencies

```bash
   npm install
```

5. Generate the app key

```bash
   php artisan key:generate
```

6. Create the database (if it doesn't exist yet)

```bash
   createdb parking_lot
```

7. Run migrations

```bash
   php artisan migrate
```

8. Seed the database

```bash
   php artisan db:seed
```

At this stage this is a no-op (stub seeders only) — it should complete
with no errors. Real seed data (roles, dev admin user) lands with DB-6.

9. Build frontend assets

```bash
   npm run build
```

For active development instead, use `npm run dev` alongside a running
`php artisan serve`.

10. Start the queue worker

```bash
    php artisan queue:work
```

    This must stay running — it processes ticket print jobs (and, from
    Phase 3 onward, the sync worker). On-site this should run under a
    process supervisor (e.g. `supervisord` or a systemd service), not a
    bare foreground terminal — process management setup is DEPLOY-1's
    concern, not this ticket's.

11. Serve the app

```bash
    php artisan serve
```

    Or point a local web server (nginx/Apache) at `public/` for a more
    permanent on-site setup.

## Verifying the setup worked

- Visiting the app in a browser shows the login screen
- `php artisan queue:work` shows no connection errors on startup
- `php artisan migrate:status` shows all migrations as `Ran`

## Notes

- No CI is used for this app — this document is the entire deploy process.
- Hardware setup (thermal printer, kiosk device) is covered separately in
  DEPLOY-2, not here.
