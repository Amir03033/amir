<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Conversions (CV downloads, booking clicks, contact messages, outbound clicks). No personal data.
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('label', 120)->nullable();
            $table->char('locale', 2)->nullable();
            $table->timestamp('created_at')->index();
            $table->index(['name', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
