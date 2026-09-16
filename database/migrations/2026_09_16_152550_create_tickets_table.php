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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('plate');
            $table->string('ticket_number')->unique();
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->decimal('fee', 8, 2)->nullable();
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('restrict');
            $table->timestamps();

            $table->index('plate');
            $table->index('ticket_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
