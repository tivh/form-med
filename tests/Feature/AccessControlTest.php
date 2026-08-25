<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\FormSubmission;
use App\Models\TaxRegimeSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    private Area $complianceArea;
    private Area $financeArea;

    protected function setUp(): void
    {
        parent::setUp();

        $this->complianceArea = Area::firstOrCreate(
            ['slug' => 'form-med'],
            [
                'name' => 'Compliance',
                'description' => 'Formulário de Fornecedores',
                'default_route' => 'admin.submissions.index',
                'is_active' => true,
            ]
        );

        $this->financeArea = Area::firstOrCreate(
            ['slug' => 'regime-tributario'],
            [
                'name' => 'Financeiro',
                'description' => 'Regime Tributário',
                'default_route' => 'admin.tax-regime.index',
                'is_active' => true,
            ]
        );
    }

    public function test_super_admin_has_full_access_to_all_areas_and_admin_settings(): void
    {
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);
        $superAdmin->markEmailAsVerified();

        // 1. Dashboard redireciona para a área unificada do Filament
        $this->actingAs($superAdmin)
            ->get(route('admin.dashboard'))
            ->assertRedirect('/filament');

        // 2. Acesso à área de Compliance no Filament
        $this->actingAs($superAdmin)
            ->get('/filament/form-submissions')
            ->assertOk();

        // 3. Acesso à área de Financeiro no Filament
        $this->actingAs($superAdmin)
            ->get('/filament/tax-regime-submissions')
            ->assertOk();

        // 4. Acesso à gestão de usuários no Filament
        $this->actingAs($superAdmin)
            ->get('/filament/users')
            ->assertOk();

        // 5. Acesso aos formulários dinâmicos no Filament
        $this->actingAs($superAdmin)
            ->get('/filament/custom-forms')
            ->assertOk();
    }

    public function test_multi_area_director_can_access_both_financial_and_compliance_areas(): void
    {
        $director = User::factory()->create([
            'name' => 'Diretora Financeira',
            'is_super_admin' => false,
        ]);
        $director->markEmailAsVerified();

        // Vincula as duas áreas: Compliance e Financeiro
        $director->areas()->attach([$this->complianceArea->id, $this->financeArea->id]);

        // 1. Dashboard redireciona para o Filament
        $response = $this->actingAs($director)->get(route('admin.dashboard'));
        $response->assertRedirect('/filament');

        // 2. Acesso à área de Compliance permitido
        $this->actingAs($director)
            ->get('/filament/form-submissions')
            ->assertOk();

        // 3. Acesso à área de Financeiro permitido
        $this->actingAs($director)
            ->get('/filament/tax-regime-submissions')
            ->assertOk();

        // 4. Bloqueada em Usuários (403 Forbidden)
        $this->actingAs($director)
            ->get('/filament/users')
            ->assertForbidden();
    }

    public function test_single_area_user_is_redirected_to_own_area_and_forbidden_from_other_area(): void
    {
        $financialUser = User::factory()->create([
            'is_super_admin' => false,
        ]);
        $financialUser->markEmailAsVerified();
        $financialUser->areas()->attach($this->financeArea->id);

        // 1. Ao acessar /admin é redirecionado para /filament
        $this->actingAs($financialUser)
            ->get(route('admin.dashboard'))
            ->assertRedirect('/filament');

        // 2. Acesso ao Financeiro no Filament permitido
        $this->actingAs($financialUser)
            ->get('/filament/tax-regime-submissions')
            ->assertOk();

        // 3. Acesso ao Compliance no Filament proibido (403)
        $this->actingAs($financialUser)
            ->get('/filament/form-submissions')
            ->assertForbidden();
    }

    public function test_compliance_user_with_granular_classification_permission_filters_submissions(): void
    {
        $rhUser = User::factory()->create([
            'is_super_admin' => false,
        ]);
        $rhUser->markEmailAsVerified();

        // Vincula Compliance com permissão restrita a apenas 'pj-rh'
        $rhUser->areas()->attach($this->complianceArea->id, [
            'permissions' => json_encode(['allowed_classifications' => ['pj-rh']]),
        ]);

        FormSubmission::create([
            'form_type' => 'form-med',
            'registration_type' => 'pj',
            'classification' => 'pj',
            'nome' => 'Fornecedor Diverso SA',
            'razao_social' => 'Fornecedor Diverso SA',
            'cnpj' => '11.111.111/0001-11',
            'endereco' => 'Rua Teste, 100',
            'nacionalidade' => 'Brasileira',
            'profissao' => 'Empresário',
            'email' => 'diverso@example.com',
            'legal_declaration' => 'concorda',
            'legal_representative' => 'Rep 1',
            'legal_representative_cpf' => '123.456.789-01',
            'legal_representative_date' => '2026-08-01',
            'documents' => [],
        ]);

        FormSubmission::create([
            'form_type' => 'form-med',
            'registration_type' => 'pj',
            'classification' => 'pj-rh',
            'nome' => 'Colaborador PJ RH Ltda',
            'razao_social' => 'Colaborador PJ RH Ltda',
            'cnpj' => '22.222.222/0001-22',
            'endereco' => 'Rua RH, 200',
            'nacionalidade' => 'Brasileira',
            'profissao' => 'Consultor',
            'email' => 'colaborador@example.com',
            'legal_declaration' => 'concorda',
            'legal_representative' => 'Rep 2',
            'legal_representative_cpf' => '123.456.789-02',
            'legal_representative_date' => '2026-08-01',
            'documents' => [],
        ]);

        $response = $this->actingAs($rhUser)
            ->get(route('admin.submissions.index'));

        $response->assertOk();
        $response->assertSee('Colaborador PJ RH Ltda');
        $response->assertDontSee('Fornecedor Diverso SA');
    }

    public function test_super_admin_can_create_user_with_multiple_areas(): void
    {
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);
        $superAdmin->markEmailAsVerified();

        $response = $this->actingAs($superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Diretora Nova',
                'email' => 'diretora@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'is_super_admin' => '0',
                'areas' => ['form-med', 'regime-tributario'],
                'admin_role' => ['pj', 'pj-rh'],
            ]);

        $response->assertRedirect(route('admin.users.index'));

        $newUser = User::where('email', 'diretora@example.com')->firstOrFail();
        $this->assertFalse($newUser->isSuperAdmin());
        $this->assertTrue($newUser->canAccess('form-med'));
        $this->assertTrue($newUser->canAccess('regime-tributario'));
        $this->assertEquals(['pj', 'pj-rh'], $newUser->allowedClassifications());
    }

    public function test_cannot_demote_the_last_super_admin(): void
    {
        User::query()->delete();

        $onlyAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);
        $onlyAdmin->markEmailAsVerified();

        $response = $this->actingAs($onlyAdmin)
            ->put(route('admin.users.update', $onlyAdmin), [
                'name' => 'Only Admin',
                'email' => $onlyAdmin->email,
                'is_super_admin' => '0',
                'areas' => ['form-med'],
            ]);

        $response->assertSessionHasErrors('is_super_admin');
        $this->assertTrue($onlyAdmin->fresh()->isSuperAdmin());
    }
}
