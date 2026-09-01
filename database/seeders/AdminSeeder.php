<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $emails = array_filter(array_map('trim', explode(',', (string) env('ADMIN_EMAILS', ''))));

        if (empty($emails)) {
            return;
        }

        User::whereIn('email', array_map('strtolower', $emails))->update(['is_admin' => true]);
    }
}
