<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela de Formulários Personalizados
        Schema::create('custom_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('online');
            $table->boolean('is_multi_step')->default(true);
            $table->string('submission_context')->nullable()->default('public');
            $table->string('restrict_registration_type')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        // 2. Tabela de Etapas / Seções do Formulário
        Schema::create('form_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_form_id')->constrained('custom_forms')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        // 3. Tabela de Campos do Formulário
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_step_id')->constrained('form_steps')->cascadeOnDelete();
            $table->string('name');
            $table->string('label');
            $table->string('type')->default('text'); // text, email, tel, date, textarea, select, radio, checkbox, file, checklist, terms, info
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable();
            $table->json('validation_rules')->nullable();
            $table->json('conditional_logic')->nullable();
            $table->integer('grid_columns')->default(2);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('form_steps');
        Schema::dropIfExists('custom_forms');
    }
};
