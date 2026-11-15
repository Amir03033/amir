<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Safety net: production may have missed 2026_09_21_143947. Adds the columns only when they are
 * missing, so it is a no-op where they already exist. Additive; down() removes nothing it didn't add.
 */
return new class extends Migration
{
    public function up(): void
    {
        $missing = array_values(array_filter(['featured', 'sort_order'], fn ($c) => ! Schema::hasColumn('projects', $c)));

        if (! $missing) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) use ($missing) {
            if (in_array('featured', $missing, true)) {
                $table->boolean('featured')->default(false);
            }
            if (in_array('sort_order', $missing, true)) {
                $table->unsignedInteger('sort_order')->default(0);
            }
        });
    }

    public function down(): void
    {
        // Intentionally empty: these columns belong to the earlier migration and must not be dropped here.
    }
};
