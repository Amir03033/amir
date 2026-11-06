<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Every stats query filters on created_at; without an index each one scans the whole table.
        Schema::table('visits', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['created_at', 'ip_hash']);
        });

        // One row per day survives after raw visits are pruned.
        Schema::create('visit_days', function (Blueprint $table) {
            $table->date('date')->primary();
            $table->unsignedInteger('views');
            $table->unsignedInteger('visitors');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_days');

        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex(['created_at', 'ip_hash']);
            $table->dropIndex(['created_at']);
        });
    }
};
