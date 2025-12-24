<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_parent_id_to_targets_table.php
    public function up()
    {
        Schema::table('targets', function (Blueprint $table) {
            $table->foreignId('parent_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('targets')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('targets', function (Blueprint $table) {
            //
        });
    }
};
