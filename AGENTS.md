# AGENTS.md - DevPortfolio Agent Instructions

> Correct file name: `AGENTS.md`.

## Shared VPS rules

The rules shared by every project under `/var/www` live in `/var/www/.ai/`
(its own repo). Always read `/var/www/.ai/core.md` — the invariants that
apply to every session — and check `/var/www/.ai/README.md`, which has the
full routing table by situation: the SPA pattern, Laravel (Blade, Vite,
`storage/` permissions, migrations), VPS infrastructure and stack, SEO, error
pages, login/registration, dashboards, security, the Telegram bot, and the
new-site checklist.

They are read **by path, on demand**; do not import them with `@` (an
`@import` loads into context exactly as if the file were pasted here, so it
would save nothing).

When the user asks you to remember, note, or save something, persist it in
the file that corresponds — `/var/www/.ai/**` if it applies VPS-wide, or this
project's documentation if it's local only — and never leave it only in the
conversation. If you create a new file, add it to the routing table.

**Precedence**: if this file contradicts the shared one, this file wins.

This is the single source of agent instructions for this repository. Do not add
a separate `AGENT.md`; merge future guidance into this file.

---

## Project Overview

**Project Type:** Full-stack web application (Laravel + Blade + Alpine.js)  
**Language:** PHP 8.2, JavaScript ES6+  
**Framework:** Laravel 12  
**Frontend:** Blade templates, Tailwind CSS 3, Alpine.js 3  
**Database:** MySQL 8.0  
**Environment:** Docker, Laravel Sail, WSL2

**DevPortfolio CMS** is a professional portfolio management system with
integrated CRM capabilities. It serves two main purposes:

1. **Public Portfolio:** Dynamic landing page showcasing projects, with
   dark/light mode support.
2. **Admin CRM System:** Management system for projects, clients, contacts, and
   communications.

### Key Features

- Admin dashboard with real-time metrics.
- Project CRUD with image uploads and technology associations.
- Client management for companies and individuals.
- Contact management within clients, including roles, phones, and notes.
- Inbox system for incoming messages with lead-to-contact conversion.
- Message history linked to clients.
- Authentication and authorization through Laravel Breeze.
- Dark/light theme persistence.

---

## Architecture And Structure

### MVC Pattern

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── ClientController
│   │   ├── ContactController
│   │   ├── MessageController
│   │   ├── ProjectController
│   │   └── PortfolioController
│   └── Requests/
├── Models/
│   ├── User.php
│   ├── Project.php
│   ├── Client.php
│   ├── Contact.php
│   ├── Message.php
│   ├── Technology.php
│   └── ContactMethod.php
├── Jobs/
│   └── SendNewContactAlert.php
├── Services/
│   └── Notifications/
│       ├── TelegramNotifier.php
│       └── WhatsAppNotifier.php
└── Providers/
    └── AppServiceProvider.php
```

### Key Models And Relationships

- **User** has many **Projects**.
- **Project** belongs to **Client**.
- **Project** belongs to many **Technologies** through a pivot table.
- **Client** has many **Contacts**.
- **Client** has many **Messages**.
- **Contact** has many **ContactMethods**.

### Frontend Structure

```text
resources/
├── views/
│   ├── layouts/
│   ├── auth/
│   ├── dashboard/
│   ├── projects/
│   ├── clients/
│   ├── messages/
│   └── portfolio/
├── css/
│   └── app.css
└── js/
    └── app.js
```

### Database Schema Highlights

- **projects:** id, title, description, image_path, is_public, user_id,
  client_id, created_at.
- **clients:** id, name, type, email, phone, website, created_at.
- **contacts:** id, first_name, last_name, role, notes, client_id, created_at.
- **messages:** id, name, email, message, is_read, client_id, created_at.
- **technologies:** id, name, icon.
- **project_technology:** project_id, technology_id.

---

## Development Setup

### Initial Setup

```bash
# Clone and install
git clone <repo>
cd DevPortfolio

# Install PHP dependencies via Docker
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" \
  -w /var/www/html laravelsail/php82-composer:latest \
  composer install --ignore-platform-reqs

# Copy environment file
cp .env.example .env

# Start containers
./vendor/bin/sail up -d

# Generate app key and migrate
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate:fresh --seed

# Install and build frontend assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

### Development Commands

```bash
# Run entire stack: server, queue, logs, and Vite
composer run dev

# Just the dev server
./vendor/bin/sail artisan serve

# Watch frontend assets
./vendor/bin/sail npm run dev

# Run tests
composer run test

# Database operations
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail artisan tinker

# Code quality
./vendor/bin/sail artisan pint
./vendor/bin/sail artisan test
```

### Demo Credentials

- **URL:** http://localhost/login
- **Email:** admin@admin.com
- **Password:** password

---

## Technology Decisions

### Frontend: Blade + Alpine.js

Laravel Breeze includes server-rendered Blade templates with Alpine.js for
lightweight interactivity. This is intentional; this is not a React or Vue SPA.

- **Advantages:** Simpler server-side logic, Laravel auth built in, less JS
  overhead.
- **Trade-off:** Full-page refreshes for navigation, which is acceptable for
  this admin tool.
- **Best for:** Content management systems and admin dashboards.

### Styling: Tailwind CSS 3

- Utility-first CSS framework.
- Vite integration through Tailwind tooling.
- Form styling through `@tailwindcss/forms`.

### Database: MySQL

- Structured relational data for clients, projects, messages, and contacts.
- Foreign key constraints.
- No NoSQL dependency is currently needed.

### Notifications

- Queue-based notification flow through Laravel Jobs.
- Telegram and WhatsApp notifier services are available.
- Redis can be added for production queue handling.

---

## Key Code Patterns

### Model Relationships

```php
public function technologies()
{
    return $this->belongsToMany(Technology::class);
}

public function contacts()
{
    return $this->hasMany(Contact::class);
}

public function client()
{
    return $this->belongsTo(Client::class);
}
```

### Form Validation

Form validation belongs in `app/Http/Requests/`. Keep controller methods focused
on workflow and persistence.

### Controller Pattern

Controllers follow standard Laravel resource naming:

- `index()` lists records.
- `create()` shows the create form.
- `store()` saves a new record.
- `show()` displays a record.
- `edit()` shows the edit form.
- `update()` saves changes.
- `destroy()` deletes a record.

### Service Layer

Reusable business logic belongs in services, such as
`app/Services/Notifications/TelegramNotifier.php` and
`app/Services/Notifications/WhatsAppNotifier.php`.

---

## Authentication And Authorization

- **Method:** Laravel Breeze, session-based.
- **Default:** Single admin user seeded in development.
- **Model:** `User` with Laravel guards.
- **Middleware:** `auth` is required on admin routes in `routes/web.php`.

---

## Database Seeding

Run this command to reset and seed development data:

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

This creates the default admin user, sample projects, clients, and technologies.
Be careful with this command outside development because it resets data.

Seeder files live in `database/seeders/`.

---

## Deployment Notes

### Environment Variables

Important `.env` values:

- `APP_URL`
- `DB_HOST`
- `DB_DATABASE`
- `MAIL_FROM_ADDRESS`
- Telegram and WhatsApp API keys, if notification services are enabled.

### Docker

The `compose.yaml` includes:

- Laravel app container, PHP 8.2.
- MySQL database.
- Optional services such as Redis, Memcached, and Milvus.

### Asset Pipeline

- Frontend assets are compiled with Vite.
- CSS is processed through Tailwind.
- JavaScript is bundled by Vite.
- Built files are placed in `public/build/`.

---

## Debugging Laravel 500 Errors

When a 500 appears, check logs and permissions before changing application code.

### Fast Diagnostic Checklist

1. Review `storage/logs/laravel.log`.
2. If the error mentions `Permission denied` in `storage/framework/views`,
   `storage/logs`, or `bootstrap/cache`, treat it as an ownership or writable
   permissions issue first.
3. Clear Laravel caches:

```bash
php artisan optimize:clear
php artisan view:clear
```

4. Retry the page.

### Recommended Local Permissions

From the project root in Linux, WSL, or Sail-oriented local environments:

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
find storage -type f -exec chmod 664 {} \;
find storage -type d -exec chmod 775 {} \;
find bootstrap/cache -type f -exec chmod 664 {} \;
find bootstrap/cache -type d -exec chmod 775 {} \;
```

Notes:

- Avoid `chmod -R 777` except for short, explicit diagnosis.
- With containers, confirm that the effective web-server user can write to
  `storage/` and `bootstrap/cache/`.

### Mixed Owners In `storage/framework/views`

If CLI commands compile Blade views as one user while PHP-FPM or Apache runs as
another user, compiled view files can be owned by the wrong account. A small
Blade edit then forces recompilation and Laravel may fail with
`file_put_contents(.../storage/framework/views/...): Permission denied`.

Stable fix:

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
php artisan view:clear
```

Quick cleanup when sudo is unavailable:

```bash
find storage/framework/views -maxdepth 1 -type f -name '*.php' ! -user www-data -delete
php artisan view:clear
```

Adjust `www-data` if the web-server user is different.

### Common 500 Causes

- Missing `.env` variables such as `APP_KEY`, DB config, or `APP_URL`.
- Missing dependencies in `vendor/` or `node_modules/`.
- Stale Laravel caches after `.env` or config changes.
- Pending migrations.
- Blade or PHP syntax errors.
- Storage or cache directories not writable by the web process.

### Recovery Commands

```bash
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

In Sail:

```bash
./vendor/bin/sail artisan optimize:clear
```

---

## Known Limitations And Future Improvements

### Current Gaps

1. **Image storage:** Uses local storage in `storage/app/public`; production may
   need S3 or another object store.
2. **Notifications:** Telegram and WhatsApp notifiers exist but may need
   endpoint verification.
3. **Email:** No custom email templates for notifications yet.
4. **Real-time:** Messages do not auto-refresh because WebSockets are not yet
   implemented.

### Recommended Enhancements

1. Add REST API routes under `/api/*` for mobile or external integrations.
2. Expand the PHPUnit test suite.
3. Add soft deletes on critical models.
4. Add Laravel Scout or another search layer for full-text search.
5. Implement Laravel Echo with Pusher or Soketi for real-time updates.
6. Add throttling middleware on the public contact form.

---

## Important Files And Commands

| File or command | Purpose |
| --- | --- |
| `routes/web.php` | Web route definitions |
| `routes/api.php` | API routes, if adding REST endpoints |
| `config/database.php` | Database configuration |
| `app/Http/Middleware` | Custom middleware |
| `database/migrations` | Schema version control |
| `vite.config.js` | Frontend build configuration |
| `.env.example` | Environment variable template |
| `artisan` | Laravel CLI tool |
| `storage/logs/laravel.log` | Main Laravel log |

---

## References

- [Laravel Docs](https://laravel.com/docs)
- [Laravel Breeze](https://laravel.com/docs/breeze)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
- [MySQL Docs](https://dev.mysql.com/doc/)

---

**Last Updated:** 2026-06-05  
**Laravel Version:** 12.x  
**PHP Version:** 8.2+

## Tareas pendientes

El backlog vivo de este proyecto está en [`.ai/PENDING.md`](.ai/PENDING.md);
cómo se registra y se cierra una tarea, en
[`.ai/workflows/pending.md`](.ai/workflows/pending.md). Lo transversal de la
VPS va a `/var/www/.ai/PENDING.md`, no aquí.

La organización del resto de documentación local se explica en
[`.ai/README.md`](.ai/README.md).

Al tocar el fondo de puntos y sus interacciones, lee
[`.ai/frontend-performance.md`](.ai/frontend-performance.md).
