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
        Schema::table('sales', function (Blueprint $table) {

            // Amount (for amount-type targets)
            $table->decimal('amount', 12, 2)
                  ->nullable()
                  ->after('boxes_sold');

            // Executive who made the sale
            $table->foreignId('executive_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('target_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['executive_id']);
            $table->dropColumn(['amount', 'executive_id']);
        });
    }
};
