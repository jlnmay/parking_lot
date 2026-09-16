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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendant_id')->constrained('users')->onDelete('restrict');
            $table->decimal('opening_cash', 8, 2);
            $table->decimal('expected_cash', 8, 2)->nullable();
            $table->decimal('actual_cash', 8, 2)->nullable();
            $table->decimal('discrepancy', 8, 2)->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
