<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->json('conflict_roles')->nullable();
            $table->text('conflict_roles_details')->nullable();
            $table->string('public_power_relatives', 10)->nullable();
            $table->text('public_power_relatives_details')->nullable();
            $table->string('internal_relationships', 10)->nullable();
            $table->text('internal_relationships_details')->nullable();
            $table->string('employee_shareholding', 10)->nullable();
            $table->text('employee_shareholding_details')->nullable();
            $table->string('conflict_situation', 10)->nullable();
            $table->text('conflict_situation_details')->nullable();
            $table->string('competitor_relationships', 10)->nullable();
            $table->text('competitor_relationships_details')->nullable();
            $table->string('contractor_shareholding', 10)->nullable();
            $table->text('contractor_shareholding_details')->nullable();
            $table->string('friendship_ties', 10)->nullable();
            $table->text('friendship_ties_details')->nullable();
            $table->string('legal_declaration', 20)->nullable();
            $table->string('legal_representative')->nullable();
            $table->string('legal_representative_cpf')->nullable();
            $table->string('legal_representative_role')->nullable();
            $table->date('legal_representative_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'conflict_roles',
                'conflict_roles_details',
                'public_power_relatives',
                'public_power_relatives_details',
                'internal_relationships',
                'internal_relationships_details',
                'employee_shareholding',
                'employee_shareholding_details',
                'conflict_situation',
                'conflict_situation_details',
                'competitor_relationships',
                'competitor_relationships_details',
                'contractor_shareholding',
                'contractor_shareholding_details',
                'friendship_ties',
                'friendship_ties_details',
                'legal_declaration',
                'legal_representative',
                'legal_representative_cpf',
                'legal_representative_role',
                'legal_representative_date',
            ]);
        });
    }
};
