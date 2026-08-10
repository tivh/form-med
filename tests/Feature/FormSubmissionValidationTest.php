<?php

namespace Tests\Feature;

use App\Models\FormSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FormSubmissionValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pj_submission_requires_new_partner_fields_and_sets_compliance_timestamp(): void
    {
        Storage::fake('private_uploads');

        $response = $this->post(route('forms.submit', ['form' => 'form-med']), [
            'registration_type' => 'pj',
            'nome' => 'Empresa Ltda',
            'razao_social' => 'Empresa Ltda',
            'nome_fantasia' => 'Empresa',
            'cnpj' => '12.345.678/0001-95',
            'representante_legal_nome' => 'João da Silva',
            'representante_legal_email' => 'joao@example.com',
            'responsavel_juridico_nome' => 'Maria da Silva',
            'responsavel_juridico_email' => 'maria@example.com',
            'testemunha_nome' => 'Carlos Testemunha',
            'testemunha_email' => 'carlos@example.com',
            'endereco' => 'Rua das Empresas, 123',
            'email' => 'empresa@example.com',
            'website' => 'https://empresa.com.br',
            'telefone' => '(11) 99999-9999',
            'nacionalidade' => 'Brasileira',
            'profissao' => 'Empresária',
            'dados_bancarios' => 'Banco do Brasil, Agencia 1234, Conta 56789',
            'doc_checklist' => ['Contrato social'],
            'compliance_policies' => ['Programa de Compliance estruturado'],
            'investigated_for' => ['Não'],
            'investigation_details' => null,
            'law_12846_compliant' => '1',
            'lgpd_compliant' => '1',
            'conflict_roles' => ['Nenhuma das opções acima'],
            'conflict_roles_details' => null,
            'public_power_relatives' => 'nao',
            'public_power_relatives_details' => null,
            'internal_relationships' => 'nao',
            'internal_relationships_details' => null,
            'employee_shareholding' => 'nao',
            'employee_shareholding_details' => null,
            'conflict_situation' => 'nao',
            'conflict_situation_details' => null,
            'competitor_relationships' => 'nao',
            'competitor_relationships_details' => null,
            'contractor_shareholding' => 'nao',
            'contractor_shareholding_details' => null,
            'friendship_ties' => 'nao',
            'friendship_ties_details' => null,
            'legal_declaration' => 'concorda',
            'legal_representative' => 'Ana Responsável',
            'legal_representative_cpf' => '123.456.789-09',
            'legal_representative_role' => 'Diretora',
            'legal_representative_date' => '2026-07-28',
            'documents' => [UploadedFile::fake()->create('contrato.pdf', 100, 'application/pdf')],
        ]);

        $response->assertRedirect(route('forms.success', ['form' => 'form-med']));

        $submission = FormSubmission::latest()->first();
        $this->assertNotNull($submission);
        $this->assertSame('João da Silva', $submission->representante_legal_nome);
        $this->assertSame('joao@example.com', $submission->representante_legal_email);
        $this->assertSame('Carlos Testemunha', $submission->testemunha_nome);
        $this->assertSame('carlos@example.com', $submission->testemunha_email);
        $this->assertNotNull($submission->compliance_aceito_em);
        $this->assertFalse($submission->verified);
    }

    public function test_admin_can_toggle_verified_status_for_a_submission(): void
    {
        $user = User::factory()->create([
            'form_scope' => 'form-med',
        ]);
        $user->markEmailAsVerified();
        $submission = FormSubmission::create([
            'form_type' => 'form-med',
            'registration_type' => 'pf',
            'nome' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'endereco' => 'Rua Teste, 123',
            'nacionalidade' => 'Brasileira',
            'profissao' => 'Analista',
            'legal_declaration' => 'concorda',
            'legal_representative' => 'Responsável',
            'legal_representative_cpf' => '123.456.789-09',
            'legal_representative_date' => '2026-07-28',
            'public_power_relatives' => 'nao',
            'internal_relationships' => 'nao',
            'employee_shareholding' => 'nao',
            'conflict_situation' => 'nao',
            'competitor_relationships' => 'nao',
            'documents' => [],
        ]);

        $this->actingAs($user)
            ->post(route('admin.submissions.toggle-verified', $submission), ['verified' => '1'])
            ->assertRedirect();

        $this->assertTrue($submission->fresh()->verified);
    }
}
