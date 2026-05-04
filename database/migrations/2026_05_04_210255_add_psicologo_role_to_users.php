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
        // MariaDB/MySQL: ALTER COLUMN to extend the enum
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE users MODIFY COLUMN role ENUM('admin','hr','psicologo') NOT NULL DEFAULT 'hr'"
        );
    }

    public function down(): void
    {
        // Remove psicologo (existing rows with that role revert to 'hr' first)
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE users SET role = 'hr' WHERE role = 'psicologo'"
        );
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE users MODIFY COLUMN role ENUM('admin','hr') NOT NULL DEFAULT 'hr'"
        );
    }
};
