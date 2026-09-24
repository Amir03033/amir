<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->json('title');           // {"nl": "...", "en": "..."}
            $table->string('issuer')->nullable();
            $table->date('issued_at')->nullable();
            $table->string('file')->nullable();       // pdf of afbeelding
            $table->string('external_url')->nullable(); // bv. verificatielink
            $table->boolean('featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};