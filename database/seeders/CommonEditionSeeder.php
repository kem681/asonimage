<?php

namespace Database\Seeders;

use App\Models\Edition;
use Illuminate\Database\Seeder;

class CommonEditionSeeder extends Seeder
{
    public function run(): void
    {
        Edition::updateOrCreate(
            ['year' => 0],
            ['label' => 'Ressources communes', 'is_common' => true]
        );
    }
}
