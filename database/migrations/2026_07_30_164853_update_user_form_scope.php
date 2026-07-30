<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Todos os usuários viram Compliance
        DB::table('users')->update([
            'form_scope' => 'form-med',
        ]);

        // Exceto o administrador
        DB::table('users')
            ->where('email', 'admin@form-med.test')
            ->update([
                'form_scope' => null,
            ]);
    }

    public function down(): void
    {
        DB::table('users')->update([
            'form_scope' => null,
        ]);
    }
};