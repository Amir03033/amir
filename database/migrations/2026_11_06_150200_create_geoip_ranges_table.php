<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // IP ranges as 32-char hex of the 16-byte (IPv4-mapped) address, so string comparison equals numeric comparison.
        Schema::create('geoip_ranges', function (Blueprint $table) {
            $table->char('ip_from', 32)->primary();
            $table->char('ip_to', 32);
            $table->char('country', 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('geoip_ranges');
    }
};
