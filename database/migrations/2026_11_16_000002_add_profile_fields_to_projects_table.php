<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/** Additive: slug (for /project/{slug}), year, role and a one-line summary. Existing rows get a slug from their title. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('id');
            $table->unsignedSmallInteger('year')->nullable();
            $table->json('role')->nullable();
            $table->json('summary')->nullable();
        });

        $taken = [];
        foreach (DB::table('projects')->orderBy('id')->get(['id', 'title']) as $row) {
            $title = json_decode($row->title, true);
            $base = Str::slug(is_array($title) ? ($title['nl'] ?? $title['en'] ?? '') : (string) $row->title) ?: 'project';
            $slug = $base;
            for ($i = 2; in_array($slug, $taken, true) || ctype_digit($slug); $i++) {
                $slug = $base.'-'.$i;
            }
            $taken[] = $slug;
            DB::table('projects')->where('id', $row->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'year', 'role', 'summary']);
        });
    }
};
