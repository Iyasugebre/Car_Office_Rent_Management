This repository is a Laravel-based Car & Office Rent Management application. Use these notes to be productive quickly and make changes that fit the project's existing patterns.

High-level architecture
- Laravel app (HTTP + API): `routes/web.php` (server-rendered pages) and `routes/api.php` (JSON API resource controllers).
- Models in `app/Models` use `HasUuids` and UUID primary keys (see `Car`, `Office`, `Booking`, `User`).
- Controllers in `app/Http/Controllers` return Eloquent models/collections directly (JSON responses for API).
- Database schema lives in `database/migrations` (UUID primary keys and UUID foreign keys). Seeds in `database/seeders/DatabaseSeeder.php` provide sample data and admin credentials.

Developer workflows (how to run & test)
- Project setup: run Composer and NPM from repository root or use the `composer` script: `composer setup` (runs `composer install`, copies `.env`, generates app key, runs migrations, installs npm deps, builds assets).
- Dev server (workaround): `php artisan serve` can spawn child processes and sometimes fails on Windows; if `artisan serve` fails, start the PHP dev server directly:

```powershell
C:\Users\Eyasu\Desktop\tools\php\php.exe -S 127.0.0.1:8000 -t public
```

- Asset building: `npm run dev` for development, `npm run build` for production (Vite + Tailwind configured in `package.json` and `vite.config.js`).
- Tests: `composer test` (runs `php artisan test`).

Project-specific conventions & patterns
- UUIDs everywhere: migrations use `$table->uuid('id')->primary()` and foreign keys reference UUIDs. Keep UUIDs as strings when writing tests and API payloads.
- Controller style: validation happens inside controller `store`/`update` methods with Laravel validation arrays; controllers return the model instance or `response()->noContent()` after deletes.
- API resources: `routes/api.php` registers `apiResource` for `offices`, `cars`, and `bookings`. Follow the same controller method names and validation rules when adding endpoints.
- Domain data shapes: `Car` has `price_per_day`, `plate_number` (unique), `status` (`available` default). `Office` has `type` with values `branch` or `rental_unit` and optional `price_per_month`. `Booking` may reference either `car_id` or `office_id` and stores `start_date`, `end_date`, `total_price`, `status`.

Integration & external dependencies
- Vite/Tailwind for frontend assets: see `package.json`, `vite.config.js`, and `resources/js` + `resources/css`.
- Uses Laravel core libs; runtime PHP version expected >= 8.3 per `composer.json`.
- Authentication middleware: `routes/api.php` uses `auth:sanctum` for `/api/user` — be cautious when modifying auth flows.

Safe change guidelines for AI agents
- Do not change primary key types in migrations (UUID decisions are pervasive). If you must change schema, update migrations, factories, and seeders together.
- When adding APIs, reuse controller validation patterns (see `CarController`, `OfficeController`, `BookingController`) and register routes in `routes/api.php`.
- Use route model binding (type-hinted model parameters) the codebase expects (e.g., `function show(Car $car)`). When testing, pass UUID strings as route params.
- Keep responses consistent: controllers return Eloquent models directly (JSON). For deletes, return `response()->noContent()`.

Examples to reference
- `routes/api.php` — resource registration for `offices`, `cars`, `bookings`.
- `app/Http/Controllers/BookingController.php` — validation and relationship loading (`with(['user','car','office'])`).
- `database/migrations/2026_03_27_190659_create_bookings_table.php` — booking table structure and foreign key behavior.
- `database/seeders/DatabaseSeeder.php` — seeded admin user (`admin@example.com`) and example records.

If something fails to run
- Check the PHP binary used (this repo's README references a local PHP at `C:\Users\Eyasu\Desktop\tools\php\php.exe`). Use that binary for `artisan` and the PHP built-in server.
- If `php artisan serve` reports "Failed to listen...", start the server manually with the `-S` command shown above.

Questions for reviewers
- Are there areas you want stricter API response shapes (Resource classes) instead of returning Eloquent directly?
- Should the README contain a section about the Windows PHP binary path and `artisan serve` workaround?

If anything in this file is unclear or incomplete, tell me which area to expand (architecture, workflows, examples, or run/debug guidance).
