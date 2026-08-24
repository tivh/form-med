<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Criar tabela de áreas / módulos do sistema
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('default_route')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Criar tabela pivot de vínculo de usuário com áreas e permissões personalizadas
        Schema::create('area_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->json('permissions')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'area_id']);
        });

        // 3. Adicionar coluna is_super_admin na tabela users
        if (!Schema::hasColumn('users', 'is_super_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_super_admin')->default(false)->after('password');
            });
        }

        // 4. Semear áreas padrão iniciais
        $now = now();
        DB::table('areas')->insertOrIgnore([
            [
                'slug' => 'form-med',
                'name' => 'Compliance',
                'description' => 'Formulário de Fornecedores e Documentos de Compliance',
                'default_route' => 'admin.submissions.index',
                'icon' => 'shield-check',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'regime-tributario',
                'name' => 'Financeiro',
                'description' => 'Declarações de Regime Tributário e Documentação Fiscal',
                'default_route' => 'admin.tax-regime.index',
                'icon' => 'banknotes',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // 5. Migrar dados legados de usuários existentes (form_scope e admin_role)
        $users = DB::table('users')->get();
        $complianceArea = DB::table('areas')->where('slug', 'form-med')->first();
        $financeArea = DB::table('areas')->where('slug', 'regime-tributario')->first();

        foreach ($users as $user) {
            if ($user->form_scope === null) {
                // Usuário sem form_scope era o Super Admin
                DB::table('users')->where('id', $user->id)->update([
                    'is_super_admin' => true,
                ]);
            } elseif ($user->form_scope === 'form-med' && $complianceArea) {
                $permissions = null;
                if (!empty($user->admin_role)) {
                    $roles = array_filter(array_map('trim', explode(',', (string) $user->admin_role)));
                    if ($roles !== []) {
                        $permissions = json_encode(['allowed_classifications' => array_values($roles)]);
                    }
                }

                DB::table('area_user')->insertOrIgnore([
                    'user_id' => $user->id,
                    'area_id' => $complianceArea->id,
                    'permissions' => $permissions,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } elseif ($user->form_scope === 'regime-tributario' && $financeArea) {
                DB::table('area_user')->insertOrIgnore([
                    'user_id' => $user->id,
                    'area_id' => $financeArea->id,
                    'permissions' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_user');
        Schema::dropIfExists('areas');

        if (Schema::hasColumn('users', 'is_super_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_super_admin');
            });
        }
    }
};
