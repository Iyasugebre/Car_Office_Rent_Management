<?php

namespace App\Console\Commands;

use App\Models\Agreement;
use App\Models\User;
use App\Notifications\AgreementExpiryNotification;
use App\Notifications\RentPaymentNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:check-office-lease-alerts')]
#[Description('Check office agreements for rent payments due and contract expiries')]
class CheckOfficeLeaseAlerts extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admins = User::where('role', 'admin')->get();
        if ($admins->isEmpty()) {
            $this->error('No admin found.');
            return;
        }

        $agreements = Agreement::where('status', 'active')->get();

        foreach ($agreements as $agreement) {
            // 1. Check Contract Expiry
            $expiryDays = (int) now()->diffInDays($agreement->end_date, false);
            if (in_array($expiryDays, [60, 30, 7, 0])) {
                foreach ($admins as $admin) {
                    $admin->notify(new AgreementExpiryNotification($agreement, $expiryDays));
                }
                $this->info("Notified for Expiry: {$agreement->agreement_id} ({$expiryDays} days left)");
            }

            // 2. Check Rent Payment Due
            if ($agreement->next_rent_due_at) {
                $dueDays = (int) now()->diffInDays($agreement->next_rent_due_at, false);
                if (in_array($dueDays, [30, 7, 0])) {
                    $msg = $dueDays === 0 ? "Rent is DUE TODAY for agreement {$agreement->agreement_id}." : "Rent payment for agreement {$agreement->agreement_id} is due in {$dueDays} days.";
                    foreach ($admins as $admin) {
                        $admin->notify(new RentPaymentNotification($agreement, $msg, $dueDays));
                    }
                    $this->info("Notified for Rent: {$agreement->agreement_id} ({$dueDays} days left)");
                }
            }
        }

        $this->info('Office lease alert checks completed.');
    }
}
