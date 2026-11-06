<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_checks', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->boolean('ok');
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('response_ms')->nullable();
            $table->timestamp('checked_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_checks');
    }
};
