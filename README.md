# BUSina – Bus Reservation Platform

Laravel 12 + Jetstream (Livewire, Tailwind CSS) final project of Group 5.

## Setup (first time, after cloning)

Requirements: PHP 8.2+, Composer, Node.js.

```bash
composer install
npm install
cp .env.example .env        # then set APP_NAME=BUSina
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Open http://localhost:8000.

After pulling changes that touch migrations, run `php artisan migrate:fresh --seed` again.
This **wipes the local database** and recreates the demo data.

## Demo accounts

Created by `database/seeders/DatabaseSeeder.php`. These are local demo values only;
change them before deploying.

| Role     | Email                  | Password       |
|----------|------------------------|----------------|
| Admin    | admin@busina.test      | admin1234      |
| Employee | employee@busina.test   | employee1234   |
| Customer | customer@busina.test   | customer1234   |
| Customer | maria@busina.test      | customer1234   |

The seeder also adds 6 buses, 6 routes, trips for the next 7 days and sample reservations
(approved, pending and rejected), all written to the activity log.

## Where things are

| Area | URL prefix | Who | Controllers |
|------|-----------|-----|-------------|
| Public trip search | `/trips` | everyone | `TripController` |
| Customer portal | `/my/...`, `POST /trips/{id}/reserve` | customer | `Customer\*` |
| Staff portal | `/portal/...` | admin, employee | `Portal\*` |
| Users, Activity Logs | `/portal/users`, `/portal/activity-logs` | admin only | `Portal\UserController`, `Portal\ActivityLogController` |

Booking rules (seat holding, approve / reject / cancel) live in `app/Services/ReservationService.php`.
Run `php artisan route:list --except-vendor` to see every route and its view.

## Front end

Blade + Tailwind CSS (Jetstream / Livewire, Alpine.js for the seat map and menus). Run `npm run build` after changing views or CSS.

- **Brand color:** `brand` in `tailwind.config.js` (rose). Change it there to restyle the whole site.
- **Layouts:** `layouts/portal` (staff sidebar, collapsible / mobile drawer) and `layouts/site` (public + customer top bar).
  `<x-app-layout>` picks one by role, so Jetstream's profile page fits both.
- **Shared components** in `resources/views/components`: `logo`, `icon`, `form/input|select|password` (label with red *,
  server error, old input), `badge`, `flash`, `confirm-action` (delete / approve / reject dialog), `seat-map`, `empty-state`, `stat-card`.
- **Shared CSS classes** in `resources/css/app.css`: `.card`, `.btn-primary`, `.btn-secondary`, `.field`, `.data-table`, ...
- Forms use `novalidate`, so all validation messages come from the server (Form Requests in `app/Http/Requests`).
- `tests/Feature/PageRenderTest.php` opens every page as each role; run it after changing a view.

New sign-ups through `/register` always get the `customer` role. The `role` column
is not mass-assignable, so it can only be changed in code (e.g. by an admin screen).

## Tests

```bash
php artisan test
```
