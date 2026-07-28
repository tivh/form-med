<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->string('representante_legal_nome')->nullable();
            $table->string('representante_legal_email')->nullable();
            $table->string('responsavel_juridico_nome')->nullable();
            $table->string('responsavel_juridico_email')->nullable();
            $table->string('testemunha_nome')->nullable();
            $table->string('testemunha_email')->nullable();
            $table->timestamp('compliance_aceito_em')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'representante_legal_nome',
                'representante_legal_email',
                'responsavel_juridico_nome',
                'responsavel_juridico_email',
                'testemunha_nome',
                'testemunha_email',
                'compliance_aceito_em',
            ]);
        });
    }
};
