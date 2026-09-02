<?php

namespace Database\Seeders;

use App\Models\AuthorizedEmail;
use App\Models\User;
use Illuminate\Database\Seeder;

class BackfillUserEditionSeeder extends Seeder
{
    public function run(): void
    {
        User::whereNull('edition_id')->each(function (User $user) {
            $authorized = AuthorizedEmail::where('email', $user->email)->first();

            if ($authorized) {
                $user->update(['edition_id' => $authorized->edition_id]);
            }
        });
    }
}
