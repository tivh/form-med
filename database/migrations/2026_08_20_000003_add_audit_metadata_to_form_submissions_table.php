<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->string('submission_hash')->nullable()->unique()->after('id');
            $table->ipAddress('submitted_ip')->nullable()->after('email');
            $table->string('submitted_location')->nullable()->after('submitted_ip');
        });

        foreach (DB::table('form_submissions')->orderBy('id')->get() as $submission) {
            DB::table('form_submissions')
                ->where('id', $submission->id)
                ->update([
                    'submission_hash' => strtoupper(Str::uuid()->toString()),
                ]);
        }

        Schema::table('form_submissions', function (Blueprint $table) {
            $table->string('submission_hash')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropColumn(['submission_hash', 'submitted_ip', 'submitted_location']);
        });
    }
};
