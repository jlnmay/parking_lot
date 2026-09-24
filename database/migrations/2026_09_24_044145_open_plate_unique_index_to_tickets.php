<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE UNIQUE INDEX tickets_open_plate_unique ON tickets (plate) WHERE exit_time IS NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS tickets_open_plate_unique');
    }
};