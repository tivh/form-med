<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // RH submissions: normalize legacy values to the new pj-rh classification.
        DB::table('form_submissions')
            ->where('registration_type', 'pj')
            ->where(function ($query) {
                $query->where('form_type', 'fornecedor-rh')
                    ->orWhereIn('classification', ['pj_colaborador', 'pj-rh']);
            })
            ->update([
                'classification' => 'pj-rh',
            ]);

        // Main PJ form submissions: normalize to pj.
        DB::table('form_submissions')
            ->where('registration_type', 'pj')
            ->where(function ($query) {
                $query->where(function ($sub) {
                    $sub->whereNot('form_type', 'fornecedor-rh')
                        ->orWhereNull('form_type');
                });
            })
            ->whereIn('classification', ['pj_diverso', 'pj', null])
            ->update([
                'classification' => 'pj',
            ]);

        // PF submissions must remain pf even when historical records were stored with null/legacy values.
        DB::table('form_submissions')
            ->where('registration_type', 'pf')
            ->whereIn('classification', ['pf', null, 'pj', 'pj_diverso', 'pj_colaborador', 'pj-rh'])
            ->update([
                'classification' => 'pf',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a data normalization migration; reverting to old values would be ambiguous.
        // Keeping it intentionally as a no-op avoids destructive data changes.
    }
};
