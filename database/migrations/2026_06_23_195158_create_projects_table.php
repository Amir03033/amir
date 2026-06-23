<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->json('title');       // Wordt vertaald via JSON {"nl": "...", "en": "..."}
            $table->json('description'); // Wordt vertaald via JSON
            $table->string('image')->nullable();
            $table->string('tags');      // Bijv: "Laravel, Tailwind, Vue"
            $table->string('github_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};