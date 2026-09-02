<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anchors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('axis', 16);
            $table->text('manquement');
            $table->text('gesture');
            $table->string('confidant');
            $table->date('started_on');
            $table->date('ended_on')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });

        Schema::create('anchor_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anchor_id')->constrained()->cascadeOnDelete();
            $table->date('day');
            $table->boolean('held');
            $table->timestamps();

            $table->unique(['anchor_id', 'day']);
        });

        Schema::create('frictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anchor_id')->constrained()->cascadeOnDelete();
            $table->date('week_start');
            $table->text('body');
            $table->string('told_to')->nullable();
            $table->date('told_on')->nullable();
            $table->timestamps();

            $table->unique(['anchor_id', 'week_start']);
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anchor_id')->constrained()->cascadeOnDelete();
            $table->string('held', 8);
            $table->text('changed');
            $table->text('next_friction')->nullable();
            $table->string('decision', 16)->nullable();
            $table->date('reviewed_on');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('frictions');
        Schema::dropIfExists('anchor_checkins');
        Schema::dropIfExists('anchors');
    }
};
