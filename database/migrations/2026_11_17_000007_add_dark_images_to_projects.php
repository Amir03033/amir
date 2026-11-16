<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive: an optional dark-theme version of the cover (image_dark) and of each gallery image
 * (gallery_dark, same order as gallery). When one version is missing, the site shows the other.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'image_dark')) {
                $table->string('image_dark')->nullable()->after('image');
            }
            if (! Schema::hasColumn('projects', 'gallery_dark')) {
                $table->json('gallery_dark')->nullable()->after('gallery');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            foreach (['image_dark', 'gallery_dark'] as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
