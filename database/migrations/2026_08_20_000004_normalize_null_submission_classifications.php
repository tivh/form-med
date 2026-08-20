<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('form_submissions')
            ->where('registration_type', 'pf')
            ->whereNull('classification')
            ->update(['classification' => 'pf']);

        DB::table('form_submissions')
            ->where('registration_type', 'pj')
            ->whereNull('classification')
            ->where('form_type', 'fornecedor-rh')
            ->update(['classification' => 'pj-rh']);

        DB::table('form_submissions')
            ->where('registration_type', 'pj')
            ->whereNull('classification')
            ->where(function ($query) {
                $query->where('form_type', '!=', 'fornecedor-rh')
                    ->orWhereNull('form_type');
            })
            ->update(['classification' => 'pj']);
    }

    public function down(): void
    {
        // Data normalization cannot be reversed reliably.
    }
};