<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\CustomForm;
use App\Models\FormSubmission;
use App\Models\TaxRegimeSubmission;
use App\Models\User;
use Database\Seeders\TransferExistingFormsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicFormFilamentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TransferExistingFormsSeeder::class);
    }

    public function test_existing_forms_were_properly_transferred_to_database(): void
    {
        $this->assertDatabaseHas('custom_forms', ['slug' => 'form-med', 'title' => 'Formulário de Qualificação e Cadastro']);
        $this->assertDatabaseHas('custom_forms', ['slug' => 'fornecedor-rh', 'submission_context' => 'rh']);
        $this->assertDatabaseHas('custom_forms', ['slug' => 'regime-tributario', 'status' => 'online']);

        $formMed = CustomForm::where('slug', 'form-med')->firstOrFail();
        $this->assertCount(3, $formMed->steps);
        $this->assertTrue($formMed->fields()->count() > 15);
    }

    public function test_public_form_can_be_retrieved_from_dynamic_database_model(): void
    {
        $response = $this->get(route('forms.show', ['form' => 'form-med']));

        $response->assertOk()
            ->assertSee('Formulário de Qualificação e Cadastro')
            ->assertSee('Dados cadastrais')
            ->assertSee('Compliance e Conflito de Interesses')
            ->assertSee('Termos e Condições');
    }

    public function test_public_tax_regime_form_can_be_retrieved(): void
    {
        $response = $this->get(route('tax-regime.show'));

        $response->assertOk()
            ->assertSee('Regime Tributário');
    }

    public function test_super_admin_can_access_filament_panel(): void
    {
        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);
        $admin->markEmailAsVerified();

        $this->actingAs($admin)
            ->get('/filament')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/filament/custom-forms')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/filament/custom-forms/create')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/filament/form-submissions')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/filament/tax-regime-submissions')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/filament/compliance-documents')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/filament/legal-documents')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/filament/areas')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/filament/users')
            ->assertOk();
    }

    public function test_editing_form_title_in_database_immediately_updates_public_view(): void
    {
        $formMed = CustomForm::where('slug', 'form-med')->firstOrFail();
        $formMed->update([
            'title' => 'Formulário Atualizado pelo Filament Form Builder',
        ]);

        $response = $this->get(route('forms.show', ['form' => 'form-med']));

        $response->assertOk()
            ->assertSee('Formulário Atualizado pelo Filament Form Builder');
    }

    public function test_new_dynamic_form_created_in_filament_is_available_in_catalog(): void
    {
        $complianceArea = Area::where('slug', 'form-med')->firstOrFail();

        $newForm = CustomForm::create([
            'area_id' => $complianceArea->id,
            'slug' => 'novo-formulario-teste',
            'title' => 'Novo Formulário Customizado',
            'description' => 'Formulário criado dinamicamente no Filament',
            'status' => 'online',
            'is_multi_step' => false,
            'submission_context' => 'public',
        ]);

        $newStep = $newForm->steps()->create([
            'title' => 'Dados Gerais',
            'order_index' => 1,
        ]);

        $newStep->fields()->create([
            'name' => 'nome',
            'label' => 'Nome completo',
            'type' => 'text',
            'is_required' => true,
            'order_index' => 1,
        ]);

        $response = $this->get(route('forms.list'));
        $response->assertOk()
            ->assertSee('Novo Formulário Customizado');

        $showResponse = $this->get(route('forms.show', ['form' => 'novo-formulario-teste']));
        $showResponse->assertOk()
            ->assertSee('Novo Formulário Customizado');
    }
}
