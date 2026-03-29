# Car & Office Rent Management System

A premium, production-ready rental management system built with Laravel 12.

## Features

- **Car Rentals**: Browse and book luxury cars from various branches.
- **Office Rentals**: Rent professional office spaces and rental units.
- **Premium UI**: Modern dark-mode interface with glassmorphism effects.
- **RESTful API**: Fully documented API for integration with other services.
- **UUID Security**: All records use UUIDs for enhanced security and privacy.
- **Real-time Pricing**: Automatic price calculation for rentals in the frontend.

## Getting Started

### Prerequisites
- PHP 8.2+
- SQLite (or another supported database)
- Node.js & NPM (for asset compilation)

### Installation
1.  Clone the repository.
2.  Run `composer install`.
3.  Copy `.env.example` to `.env`.
4.  Run `C:\Users\Eyasu\Desktop\tools\php\php.exe artisan key:generate`.
5.  Run `C:\Users\Eyasu\Desktop\tools\php\php.exe artisan migrate --seed`.

### Running Locally
```bash
C:\Users\Eyasu\Desktop\tools\php\php.exe artisan serve
```
Access the application at `http://localhost:8000`.

## API Endpoints

- `GET /api/offices`: List all branches and offices.
- `GET /api/cars`: List all available cars.
- `POST /api/bookings`: Create a new booking.
- `GET /api/bookings`: List user bookings.

## Credentials (Seeded)
- **Admin**: `admin@example.com` / `password`
- **Test Customer**: (Auto-generated in seeder)

## Project Structure
- `app/Models`: Core business logic and relationships.
- `app/Http/Controllers`: API and Web request handling.
- `resources/views`: Blade templates for the premium UI.
- `database/migrations`: Normalized database schema with UUIDs.
