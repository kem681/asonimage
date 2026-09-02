<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshop_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 12)->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_contact_at')->nullable();
            $table->timestamp('next_meeting_at')->nullable();
            $table->timestamps();
        });

        Schema::create('workshop_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at');
            $table->timestamps();

            $table->unique(['workshop_group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_group_members');
        Schema::dropIfExists('workshop_groups');
    }
};
