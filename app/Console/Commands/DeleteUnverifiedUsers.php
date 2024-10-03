<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Jenssegers\Mongodb\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DeleteUnverifiedUsers extends Command
{
    protected $signature = 'users:delete-unverified';
    protected $description = 'Deletes unverified users who have not verified their emails within 10 minutes of registration.';

    public function handle()
    {
        Log::info('Inside DeleteUnverifiedUsers');

        Log::info('Starting the unverified users deletion process...');

        $tenMinutesAgo = Carbon::now()->subMinutes(1);

        $unverifiedUsers = User::whereNull('email_verified_at')
            ->where('created_at', '<', $tenMinutesAgo)
            ->get();

        $count = $unverifiedUsers->count();
        Log::info("Found $count unverified users.");

        if ($count > 0) {
            foreach ($unverifiedUsers as $user) {
                $user->delete();
                Log::info("Deleted user with email: {$user->email}");
            }
            Log::info('Unverified users deleted successfully.');
        } else {
            Log::info('No unverified users found for deletion.');
        }

        return 0;
    }
}
