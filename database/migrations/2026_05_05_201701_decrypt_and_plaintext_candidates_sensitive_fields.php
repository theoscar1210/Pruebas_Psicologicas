<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Descifrar valores existentes y guardarlos en texto plano
        $rows = DB::table('candidates')->select('id', 'email', 'document_number')->get();

        foreach ($rows as $row) {
            DB::table('candidates')->where('id', $row->id)->update([
                'email'           => $row->email           ? Crypt::decryptString($row->email)           : null,
                'document_number' => $row->document_number ? Crypt::decryptString($row->document_number) : null,
            ]);
        }

        // Añadir índice único en document_number
        Schema::table('candidates', function (Blueprint $table) {
            $table->unique('document_number');
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropUnique(['document_number']);
        });
    }
};
