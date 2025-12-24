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
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('executive_id')->constrained('users')->cascadeOnDelete();

            $table->enum('target_type', ['box', 'amount']);
            $table->integer('target_value');

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed'])
                  ->default('pending');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
