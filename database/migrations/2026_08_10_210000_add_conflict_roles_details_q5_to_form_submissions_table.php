<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('form_submissions', 'conflict_roles_details_q5')) {
                $table->text('conflict_roles_details_q5')->nullable()->after('conflict_roles_details');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('form_submissions', 'conflict_roles_details_q5')) {
                $table->dropColumn('conflict_roles_details_q5');
            }
        });
    }
};
