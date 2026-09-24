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
        Schema::create('parker_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_parker_id')->constrained('monthly_parkers')->onDelete('cascade');
            $table->string('plate_raw', 32);
            $table->string('plate', 16);
            $table->timestamps();
            $table->softDeletes();

            $table->index('plate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parker_vehicles');
    }
};
