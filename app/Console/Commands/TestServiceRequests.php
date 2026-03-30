<?php

namespace App\Console\Commands;

use App\Models\Car;
use App\Models\Office;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Console\Command;

class TestServiceRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate realistic dummy data for the vehicle service and maintenance tracker.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Setting up realistic dummy Service Requests...");

        // Ensure we have an admin
        $admin = User::first();
        if (!$admin) {
            $admin = User::factory()->create([
                'name' => 'Demo Admin',
                'email' => 'admin@demo.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Ensure an office exists
        $office = Office::first();
        if (!$office) {
            $office = Office::create([
                'name' => 'Central Headquarters',
                'address' => '100 Main St',
                'city' => 'Metropolis',
                'type' => 'headquarters',
                'price_per_month' => 10000,
                'status' => 'active',
            ]);
        }

        // Generate robust dummy cars
        $vehicles = [
            ['make' => 'Toyota', 'model' => 'Camry', 'plate' => 'ABC-1029'],
            ['make' => 'Ford', 'model' => 'Transit Van', 'plate' => 'T-85028'],
            ['make' => 'Honda', 'model' => 'Civic', 'plate' => 'XYZ-5822'],
            ['make' => 'Mercedes', 'model' => 'Sprinter', 'plate' => 'SPR-9911'],
        ];

        $cars = [];
        foreach ($vehicles as $v) {
            $cars[] = Car::firstOrCreate(
                ['plate_number' => $v['plate']],
                [
                    'office_id' => $office->id,
                    'make' => $v['make'],
                    'model' => $v['model'],
                    'year' => rand(2020, 2024),
                    'price_per_day' => rand(50, 150),
                    'status' => 'available',
                ]
            );
        }

        // 1. Pending Request
        ServiceRequest::create([
            'car_id' => $cars[0]->id,
            'requester_id' => $admin->id,
            'problem_description' => 'Brakes are squeaking loudly when applied at high speeds. Needs immediate inspection before renting out again.',
            'service_type' => 'inspection',
            'urgency_level' => 'high',
            'status' => 'pending',
        ]);
        $this->line("Created: Pending Request for {$cars[0]->plate_number}");

        // 2. Approved & Assigned Request (In Progress)
        ServiceRequest::create([
            'car_id' => $cars[1]->id,
            'requester_id' => $admin->id,
            'fleet_manager_id' => $admin->id,
            'problem_description' => 'Rear passenger door locking mechanism is jammed. Suspect faulty actuator.',
            'service_type' => 'repair',
            'urgency_level' => 'medium',
            'status' => 'approved',
            'service_provider' => 'Downtown Auto Repair Ltd',
        ]);
        // Put the van in maintenance state matching workflow
        $cars[1]->update(['status' => 'maintenance']);
        $this->line("Created: Approved/In Progress Request for {$cars[1]->plate_number}");

        // 3. Completed Request
        ServiceRequest::create([
            'car_id' => $cars[2]->id,
            'requester_id' => $admin->id,
            'fleet_manager_id' => $admin->id,
            'problem_description' => 'Standard 50,000 mile routine tune-up. Oil change, filter replacements, and tire rotation.',
            'service_type' => 'routine',
            'urgency_level' => 'low',
            'status' => 'completed',
            'service_provider' => 'QuickLube Diagnostics',
            'cost' => 385.50,
            'service_report_path' => null, 
        ]);
        $this->line("Created: Completed Service Log for {$cars[2]->plate_number}");

        $this->info("✅ Successfully injected! You can now browse the Maintenance tab to see the live records.");
    }
}
