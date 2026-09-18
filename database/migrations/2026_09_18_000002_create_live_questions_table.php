<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_session_id')->constrained('live_sessions')->cascadeOnDelete();
            $table->unsignedSmallInteger('position')->default(0);
            $table->string('type', 16); // choice | words | text
            $table->text('prompt');
            $table->json('options')->nullable();
            $table->text('notes')->nullable(); // notes de l'animateur, visibles seulement dans la vue presentateur
            $table->string('status', 16)->default('pending'); // pending | open | closed
            $table->boolean('show_results')->default(false);
            $table->timestamps();

            $table->index(['live_session_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_questions');
    }
};
