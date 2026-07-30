// database/migrations/2026_07_30_000001_create_tax_regime_submissions_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_regime_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('razao_social');
            $table->string('cnpj');
            $table->string('regime_tributario'); // Simples Nacional | Lucro Presumido | Lucro Real
            $table->boolean('lc_214_2025_compliant')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_regime_submissions');
    }
};