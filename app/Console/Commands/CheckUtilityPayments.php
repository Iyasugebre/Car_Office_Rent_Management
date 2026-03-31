<?php

namespace App\Console\Commands;

use App\Models\BranchUtility;
use App\Models\User;
use App\Notifications\UtilityPaymentNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:check-utility-payments')]
#[Description('Scan branch utilities and notify admins of upcoming bill due dates')]
class CheckUtilityPayments extends Command
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

        $utilities = BranchUtility::where('is_active', true)
            ->whereNotNull('next_due_at')
            ->get();

        foreach ($utilities as $utility) {
            $dueDays = (int) now()->diffInDays($utility->next_due_at, false);

            // Notify 7 days before and on the day
            if (in_array($dueDays, [7, 0])) {
                foreach ($admins as $admin) {
                    $admin->notify(new UtilityPaymentNotification($utility, $dueDays));
                }
                $this->info("Notified for {$utility->utility_type} at {$utility->office->name} ({$dueDays} days left)");
            }
        }

        $this->info('Utility payment check completed.');
    }
}
