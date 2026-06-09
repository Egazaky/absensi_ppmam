# PesantrenCMS — Agent Guide

## Stack
- **Laravel 11** (PHP ^8.2) with **Stisla** admin theme (Bootstrap 5, jQuery, Sass)
- **JWT auth** via `tymon/jwt-auth` (API) + Laravel UI auth scaffold (web, register/reset/verify disabled)
- **Asset pipeline**: Laravel Mix (`npm run dev` / `npm run production`)
- **DB**: MySQL (production), SQLite `:memory:` (tests)

## Setup & Commands
```
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan storage:link
php artisan migrate
php artisan db:seed
php artisan serve
```

### Asset build
- `npm run dev` / `npm run production` — compiles `resources/js/app.js` + `resources/sass/app.scss`
- `npm run watch` — watch mode

### Code quality
- `vendor/bin/phpunit` — runs all tests (unit + feature)
- `./vendor/bin/pint` — Laravel Pint for code style (PSR-12/Laravel preset)

## Architecture

### UUIDs
All primary keys are UUIDs (string, non-incrementing) via `App\Traits\Uuids`. Route-model binding uses `whereUuid` for `Schedule`.

### Roles & RBAC
4 roles: `SuperAdmin`, `Administrator`, `Pengurus`, `Santri`.
- **SuperAdmin**: full access, invisible to lower roles in queries
- **RBAC**: `config/rbac.php` defines permissions per role; overridable at runtime via `rbac_permissions` table
- **Middleware**: `permission:{key}` gate — checked by `PermissionMiddleware` via `User::canAccess()`
- **Route-level**: routes declare `->middleware('permission:xxx')` per action (see `routes/web.php`)

### Models (6)
| Model | Key traits | Notes |
|-------|-----------|-------|
| `User` | `JWTSubject`, `Uuids` | `role` enum column, `santri_id` FK |
| `Santri` | `Uuids` | Auto-creates `User` on store |
| `Attendance` | `Uuids` | Sessions: `Subuh`, `Isya`; `status` boolean |
| `Schedule` | `Uuids` | Route uses `whereUuid` binding |
| `RbacPermission` | — | Overrides `config/rbac.php` defaults |
| `LogActivity` | — | Activity audit trail |

### Routes
- **Web** (`routes/web.php`): all CMS routes under `auth` middleware, permission-gated
- **API** (`routes/api.php`): login/logout (JWT), profile, password; `prefix v1`

## Testing
- **phpunit.xml**: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`, `RefreshDatabase` trait
- **Test base** (`tests/TestCase.php`): `signIn($role, $user)` helper creates + authenticates user
- **Factories**: `UserFactory` auto-creates a `Santri` via `Santri::factory()`
- **Integration tests** seed `SantrisTableSeeder` + `UsersTableSeeder`
- Run: `vendor/bin/phpunit` or `vendor/bin/phpunit --filter=SpecificTest`

## Domain Quirks
- **Attendance week**: Sunday→Saturday (Carbon `SUNDAY` start). Week date array always 7 days.
- **Attendance sessions**: exactly `Subuh` and `Isya` (validated in `KehadiranController@toggle`)
- **QR scanning**: `html5-qrcode` copied to `public/js/html5-qrcode.min.js` via Mix
- **Cache flush**: `Cache::flush()` called after attendance mutations (both `store` and `toggle`)
- **SuperAdmin invisibility**: non-SuperAdmin users cannot see/link Santri records that belong to SuperAdmin users
- **Auto user creation**: creating a Santri auto-generates a `User` with email `santri_{random}@ppm.am` and password = last 6 of phone (or `password`)
- **PDF/Excel exports**: attendance reports export as PDF (DomPDF) or HTML-table-as-XLS

## CI (`.github/workflows/pesantren.yml`)
Runs on push to `master`/`features/**` and PRs to `master`:
1. PHP 8.1, `composer update` (not `install`)
2. `key:generate`, `jwt:secret`
3. SQLite file DB (`database/database.sqlite`)
4. `vendor/bin/phpunit`
