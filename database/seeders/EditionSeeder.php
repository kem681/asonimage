<?php

namespace Database\Seeders;

use App\Models\Edition;
use Illuminate\Database\Seeder;

class EditionSeeder extends Seeder
{
    public function run(): void
    {
        Edition::firstOrCreate(['year' => 2026], ['label' => 'Edition 2026']);
    }
}
