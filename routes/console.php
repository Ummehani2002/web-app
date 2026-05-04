<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:grant-super-admin {email : User email (case-insensitive)}', function (string $email) {
    if (! Schema::hasColumn('users', 'is_super_admin')) {
        $this->error('Column users.is_super_admin is missing. Run migrations first.');

        return 1;
    }

    $email = strtolower(trim($email));
    if ($email === '') {
        $this->error('Email is required.');

        return 1;
    }

    $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();
    if (! $user) {
        $this->error("No user found with email matching: {$this->argument('email')}");

        return 1;
    }

    $user->forceFill(['is_super_admin' => true])->save();
    $this->info("Super admin granted: {$user->email} (id {$user->id}). Log out and log in to refresh the session.");

    return 0;
})->purpose('Set is_super_admin = true for the given user (Masters + Settings access)');

Schedule::command('d365:ensure-token')->everyTenMinutes();
