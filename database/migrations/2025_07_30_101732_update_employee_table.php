<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('employees')) {
            if (!Schema::hasColumn('employees', 'organization')) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('organization')->nullable();
                });
            }
            if (!Schema::hasColumn('employees', 'division')) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('division')->nullable();
                });
            }
            if (!Schema::hasColumn('employees', 'unit_program')) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('unit_program')->nullable();
                });
            }

            if (!Schema::hasColumn('employees', 'base_station')) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('base_station')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
