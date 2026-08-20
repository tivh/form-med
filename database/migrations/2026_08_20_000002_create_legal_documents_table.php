<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_key');
            $table->string('person_type');
            $table->string('title');
            $table->string('version')->default('v1.0');
            $table->longText('text')->nullable();
            $table->timestamps();

            $table->unique(['document_key', 'person_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_documents');
    }
};
