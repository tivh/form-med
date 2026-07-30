// database/migrations/2026_07_30_000002_add_form_scope_to_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // null = acesso total (super admin)
            // 'form-med' = só vê Compliance
            // 'regime-tributario' = só vê Financeiro
            $table->string('form_scope')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('form_scope');
        });
    }
};