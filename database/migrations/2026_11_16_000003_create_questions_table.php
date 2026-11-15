<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** "Ask Amir": questions from visitors, answered in Filament. New table, additive. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->nullable();
            $table->string('email', 190)->nullable();
            $table->text('body');
            $table->string('locale', 2)->default('nl');
            $table->text('answer')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->boolean('is_public')->default(false);
            $table->string('voter_hash', 64)->index();
            $table->timestamps();

            $table->index(['is_public', 'answered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
