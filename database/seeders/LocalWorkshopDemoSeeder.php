<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WorkshopCode;
use App\Models\WorkshopParticipant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Donnees de demonstration pour le poste de developpement uniquement :
 * un code d'atelier, un participant et un admin. Ne jamais lancer en
 * production (il n'est pas dans la commande de demarrage Coolify).
 */
class LocalWorkshopDemoSeeder extends Seeder
{
    public function run(): void
    {
        $code = WorkshopCode::firstOrCreate(
            ['code' => 'TEST2026'],
            ['label' => 'Atelier de test', 'is_active' => true],
        );

        $participant = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Jonathan Test', 'password' => Hash::make('password123')],
        );

        WorkshopParticipant::firstOrCreate(
            ['user_id' => $participant->id],
            ['workshop_code_id' => $code->id, 'email' => $participant->email, 'joined_at' => now()],
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin Test', 'password' => Hash::make('password123')],
        );
        $admin->forceFill(['is_admin' => true])->save();
    }
}
