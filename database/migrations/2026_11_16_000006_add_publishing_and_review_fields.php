<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive. Draft/publish for projects (existing projects stay published: default true), case-study fields
 * (status, lessons, SEO, alt texts) and a hand-written excerpt for blog posts. Columns are only added
 * when they don't exist yet.
 */
return new class extends Migration
{
    private const PROJECT_COLUMNS = ['is_published', 'status', 'lessons', 'seo_title', 'seo_description', 'image_alt', 'gallery_alts'];

    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'is_published')) {
                $table->boolean('is_published')->default(true)->index();
            }
            if (! Schema::hasColumn('projects', 'status')) {
                $table->string('status', 16)->nullable(); // live | building
            }
            foreach (['lessons', 'seo_title', 'seo_description', 'image_alt', 'gallery_alts'] as $column) {
                if (! Schema::hasColumn('projects', $column)) {
                    $table->json($column)->nullable();
                }
            }
        });

        Schema::table('blogs', function (Blueprint $table) {
            if (! Schema::hasColumn('blogs', 'excerpt')) {
                $table->json('excerpt')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', fn (Blueprint $table) => $table->dropColumn(self::PROJECT_COLUMNS));
        Schema::table('blogs', fn (Blueprint $table) => $table->dropColumn('excerpt'));
    }
};
