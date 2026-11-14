<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A tenant may pay one month's rent in several parts (partial payments),
 * so (lease_id, rent_period) can no longer be unique.
 *
 * The plain index is added first: on MySQL the lease_id foreign key needs an
 * index, and the unique index was the one serving it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['lease_id', 'rent_period']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['lease_id', 'rent_period']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unique(['lease_id', 'rent_period']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['lease_id', 'rent_period']);
        });
    }
};
