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
        Schema::create('monthly_parkers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('plan');
            $table->date('expiration_date');
            $table->timestamps();
             $table->softDeletes();

            $table->index('expiration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_parkers');
    }
};
