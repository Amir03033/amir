<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** "Suggest something": visitor ideas with a status, shown on the public roadmap once approved. New tables, additive. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ideas', function (Blueprint $table) {
            $table->id();
            $table->string('title', 140);
            $table->text('body')->nullable();
            $table->string('status', 16)->default('new')->index(); // new, planned, building, shipped, declined
            $table->boolean('is_public')->default(false);
            $table->unsignedInteger('votes_count')->default(0);
            $table->string('locale', 2)->default('nl');
            $table->string('voter_hash', 64)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_public', 'status', 'votes_count']);
        });

        Schema::create('idea_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idea_id')->constrained()->cascadeOnDelete();
            $table->string('voter_hash', 64);
            $table->string('session_hash', 64);
            $table->timestamp('created_at')->nullable();

            $table->unique(['idea_id', 'voter_hash']);
            $table->index(['idea_id', 'session_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idea_votes');
        Schema::dropIfExists('ideas');
    }
};
