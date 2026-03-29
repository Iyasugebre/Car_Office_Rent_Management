<?php

namespace App\Console\Commands;

use App\Models\Agreement;
use App\Models\User;
use App\Models\BranchRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestAgreementRenewal extends Command
{
    protected $signature = 'test:agreements';
    protected $description = 'Generate dummy agreements near expiry and trigger the check-expiry command to test notifications.';

    public function handle()
    {
        $this->info("Creating dummy expiring agreements...");

        // Ensure we have an admin user
        $admin = User::first();
        if (!$admin) {
            $admin = User::factory()->create([
                'name' => 'Test Admin',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'password' => bcrypt('password'),
            ]);
        }

        // Agreements expiries to test: 30, 60, 90 days.
        $testDays = [30, 60, 90];


        foreach ($testDays as $days) {
            $branch = BranchRequest::create([
                'reference_number' => 'BR-TEST-' . $days,
                'branch_name' => "Test Branch {$days}",
                'location' => 'Test City',
                'proposed_office' => 'Office Space A',
                'landlord_details' => 'John Doe, 555-0100',
                'estimated_rent' => 5000,
                'status' => 'approved',
                'requester_id' => $admin->id,
            ]);

            Agreement::create([
                'branch_request_id' => $branch->id,
                'agreement_id' => 'AGR-TEST-' . $days . '-' . strtoupper(Str::random(4)),
                'landlord_name' => "Landlord {$days} Days",
                'property_address' => "123 Test Ave, Expiring in {$days} Days",
                'monthly_rent' => 5000,
                'payment_schedule' => 'Monthly',
                'start_date' => Carbon::today()->subYear()->toDateString(),
                'end_date' => Carbon::today()->addDays($days)->toDateString(),
                'status' => 'active',
                'legal_status' => 'approved',
            ]);
            $this->line("Created dummy agreement expiring in {$days} days.");
        }

        $this->info("Testing Agreements Check Expiry Command...");
        $this->call('agreements:check-expiry');

        $this->info("All done! You can log in as Admin and check the database notifications.");
    }
}
