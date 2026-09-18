<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_question_id')->constrained('live_questions')->cascadeOnDelete();
            $table->string('token', 64);
            $table->unsignedTinyInteger('option_index')->nullable();
            $table->string('value', 400)->nullable();
            $table->timestamps();

            $table->unique(['live_question_id', 'token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_answers');
    }
};
