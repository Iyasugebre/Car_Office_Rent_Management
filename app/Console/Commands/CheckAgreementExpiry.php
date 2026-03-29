<?php

namespace App\Console\Commands;

use App\Models\Agreement;
use App\Models\User;
use App\Notifications\AgreementExpiryNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckAgreementExpiry extends Command
{
    protected $signature   = 'agreements:check-expiry';
    protected $description = 'Check active agreements and send expiry notifications at 90, 60, and 30 days.';

    public function handle(): void
    {
        $thresholds = [90, 60, 30];
        $admins     = User::all(); // Notify all admin users
        $today      = Carbon::today();
        $count      = 0;

        $activeAgreements = Agreement::where('status', 'active')
            ->where('legal_status', 'approved')
            ->get();

        foreach ($activeAgreements as $agreement) {
            $endDate       = Carbon::parse($agreement->end_date);
            $daysRemaining = $today->diffInDays($endDate, false); // negative if past

            if ($daysRemaining < 0) {
                // Agreement has expired — update status
                $agreement->update(['status' => 'expired']);
                $this->line("⚠️  Expired: {$agreement->agreement_id}");
                continue;
            }

            foreach ($thresholds as $threshold) {
                if ($daysRemaining === $threshold) {
                    foreach ($admins as $admin) {
                        // Avoid duplicate notifications for same threshold
                        $alreadyNotified = $admin->notifications()
                            ->where('type', AgreementExpiryNotification::class)
                            ->whereJsonContains('data->agreement_id', $agreement->id)
                            ->whereJsonContains('data->days_remaining', $threshold)
                            ->exists();

                        if (! $alreadyNotified) {
                            $admin->notify(new AgreementExpiryNotification($agreement, $threshold));
                            $count++;
                        }
                    }
                }
            }
        }

        $this->info("✅ Expiry check complete. {$count} notification(s) sent.");
    }
}
