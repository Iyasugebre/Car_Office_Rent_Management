<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'role' => 'admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Offices (Branches and Rental Units)
        $branch1 = \App\Models\Office::firstOrCreate(
            ['name' => 'Main Branch'],
            [
                'address' => '123 Main St',
                'city' => 'Addis Ababa',
                'phone' => '0111223344',
                'type' => 'branch',
                'status' => 'available',
            ]
        );

        \App\Models\Office::firstOrCreate(
            ['name' => 'Suite 101'],
            [
                'address' => 'Bole Road',
                'city' => 'Addis Ababa',
                'phone' => '0111556677',
                'type' => 'rental_unit',
                'price_per_month' => 1500.00,
                'status' => 'available',
            ]
        );

        // Cars
        \App\Models\Car::firstOrCreate(
            ['plate_number' => 'AA-1020'],
            [
                'office_id' => $branch1->id,
                'make' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2022,
                'price_per_day' => 50.00,
                'status' => 'available',
            ]
        );

        \App\Models\Car::firstOrCreate(
            ['plate_number' => 'AA-3040'],
            [
                'office_id' => $branch1->id,
                'make' => 'Hyundai',
                'model' => 'Tucson',
                'year' => 2023,
                'price_per_day' => 80.00,
                'status' => 'available',
            ]
        );
    }
}
