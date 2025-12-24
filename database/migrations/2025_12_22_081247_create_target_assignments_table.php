<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('target_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('target_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('executive_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->enum('status', ['pending', 'accepted', 'rejected'])
                  ->default('pending');

            // Accepted box / amount
            $table->integer('accepted_value')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_assignments');
    }
};
