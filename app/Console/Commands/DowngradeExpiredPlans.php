<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DowngradeExpiredPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:downgrade-expired-plans';
    protected $description = 'Auto downgrade Pro users to Basic when plan expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::where('plan_id', 2)
            ->whereNotNull('plan_expired_at')
            ->where('plan_expired_at', '<', now())
            ->get();

        foreach ($users as $user) {

            $user->update([
                'plan_id' => 1,
                'plan_expired_at' => null,
            ]);

            // OPTIONAL: kirim notifikasi / email
            // Mail::to($user->email)->send(new PlanExpiredMail($user));

            $this->info("User {$user->email} downgraded to Basic");
        }

        $this->info('Downgrade process completed.');
    }
}
