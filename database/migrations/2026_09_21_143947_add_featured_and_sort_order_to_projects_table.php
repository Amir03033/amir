<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('featured')
                ->default(false)
                ->after('demo_url');

            $table->unsignedInteger('sort_order')
                ->default(0)
                ->after('featured');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'featured',
                'sort_order',
            ]);
        });
    }
};