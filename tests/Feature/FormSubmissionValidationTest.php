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

    public function test_public_form_always_displays_legal_document_acceptances(): void
    {
        $this->get(route('forms.show', ['form' => 'form-med']))
            ->assertOk()
            ->assertSee('Termos e Condições')
            ->assertSee('Código de conduta')
            ->assertSee('Política de integridade')
            ->assertSee('Termo de proteção de dados pessoais - LGPD');
    }

    public function test_public_form_allows_empty_documentation_checklist_and_empty_additional_uploads(): void
    {
        Storage::fake('private_uploads');

        $response = $this->post(route('forms.submit', ['form' => 'form-med']), [
            'registration_type' => 'pf',
            'nome' => 'Maria da Silva',
            'cpf' => '123.456.789-09',
            'endereco' => 'Rua A, 123',
            'email' => 'maria@example.com',
            'nacionalidade' => 'Brasileira',
            'profissao' => 'Analista',
            'telefone' => '(11) 99999-9999',
            'data_nascimento' => '1990-01-01',
            'testemunha_nome' => 'João Testemunha',
            'testemunha_email' => 'joao@example.com',
            'doc_checklist' => [],
            'required_documents' => [
                'personal_documents' => UploadedFile::fake()->create('documento-pessoal.pdf', 100, 'application/pdf'),
            ],
            'compliance_policies' => ['Nenhum'],
            'investigated_for' => ['Não'],
            'law_12846_compliant' => '1',
            'lgpd_compliant' => '1',
            'conflict_roles' => ['Nenhuma das opções acima'],
            'public_power_relatives' => 'nao',
            'internal_relationships' => 'nao',
            'employee_shareholding' => 'nao',
            'conflict_situation' => 'nao',
            'competitor_relationships' => 'nao',
            'contractor_shareholding' => 'nao',
            'friendship_ties' => 'nao',
            'legal_declaration' => 'concorda',
            'legal_representative' => 'Fulano Responsável',
            'legal_representative_cpf' => '123.456.789-09',
            'legal_representative_role' => 'Diretor',
            'legal_representative_date' => '2026-07-28',
            'terms_accepted' => '1',
            'representation_authority_accepted' => '1',
            'documents' => [],
        ]);

        $response->assertRedirect(route('forms.success', ['form' => 'form-med']));
    }

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
            'doc_checklist' => ['Contrato social', 'Documento do representante legal (CNH ou RG)'],
            'required_documents' => [
                'corporate_document' => UploadedFile::fake()->create('contrato-social.pdf', 100, 'application/pdf'),
                'legal_representative_document' => UploadedFile::fake()->create('representante.pdf', 100, 'application/pdf'),
            ],
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
            'terms_accepted' => '1',
            'representation_authority_accepted' => '1',
            'documents' => [UploadedFile::fake()->create('contrato.pdf', 100, 'application/pdf')],
        ]);

        $response->assertRedirect(route('forms.success', ['form' => 'form-med']));

        $submission = FormSubmission::latest()->first();
        $this->assertNotNull($submission);
        $this->assertSame('João da Silva', $submission->representante_legal_nome);
        $this->assertSame('joao@example.com', $submission->representante_legal_email);
        $this->assertSame('Carlos Testemunha', $submission->testemunha_nome);
        $this->assertSame('carlos@example.com', $submission->testemunha_email);
        $this->assertArrayHasKey('corporate_document', $submission->required_documents);
        $this->assertArrayHasKey('legal_representative_document', $submission->required_documents);
        $this->assertNotNull($submission->submission_hash);
        $this->assertNotNull($submission->submitted_ip);
        $this->assertNotNull($submission->submitted_location);
        $this->assertNotNull($submission->compliance_aceito_em);
        $this->assertFalse($submission->verified);
    }

    public function test_public_pj_submissions_default_to_pj_diverso(): void
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
            'required_documents' => [
                'corporate_document' => UploadedFile::fake()->create('contrato-social.pdf', 100, 'application/pdf'),
                'legal_representative_document' => UploadedFile::fake()->create('representante.pdf', 100, 'application/pdf'),
            ],
            'compliance_policies' => ['Programa de Compliance estruturado'],
            'investigated_for' => ['Não'],
            'law_12846_compliant' => '1',
            'lgpd_compliant' => '1',
            'conflict_roles' => ['Nenhuma das opções acima'],
            'public_power_relatives' => 'nao',
            'internal_relationships' => 'nao',
            'employee_shareholding' => 'nao',
            'conflict_situation' => 'nao',
            'competitor_relationships' => 'nao',
            'contractor_shareholding' => 'nao',
            'friendship_ties' => 'nao',
            'legal_declaration' => 'concorda',
            'legal_representative' => 'Ana Responsável',
            'legal_representative_cpf' => '123.456.789-09',
            'legal_representative_role' => 'Diretora',
            'legal_representative_date' => '2026-07-28',
            'terms_accepted' => '1',
            'representation_authority_accepted' => '1',
            'documents' => [UploadedFile::fake()->create('contrato.pdf', 100, 'application/pdf')],
        ]);

        $response->assertRedirect(route('forms.success', ['form' => 'form-med']));

        $submission = FormSubmission::latest()->first();
        $this->assertNotNull($submission);
        $this->assertSame('pj', $submission->classification);
    }

    public function test_rh_form_submission_is_classified_as_pj_colaborador(): void
    {
        Storage::fake('private_uploads');

        $response = $this->post(route('forms.submit', ['form' => 'fornecedor-rh']), [
            'registration_type' => 'pj',
            'nome' => 'Empresa RH Ltda',
            'razao_social' => 'Empresa RH Ltda',
            'nome_fantasia' => 'Empresa RH',
            'cnpj' => '12.345.678/0001-95',
            'representante_legal_nome' => 'João da Silva',
            'representante_legal_email' => 'joao@example.com',
            'responsavel_juridico_nome' => 'Maria da Silva',
            'responsavel_juridico_email' => 'maria@example.com',
            'testemunha_nome' => 'Carlos Testemunha',
            'testemunha_email' => 'carlos@example.com',
            'endereco' => 'Rua das Empresas, 123',
            'email' => 'empresa-rh@example.com',
            'website' => 'https://empresa-rh.com.br',
            'telefone' => '(11) 99999-9999',
            'nacionalidade' => 'Brasileira',
            'profissao' => 'Empresária',
            'dados_bancarios' => 'Banco do Brasil, Agencia 1234, Conta 56789',
            'doc_checklist' => ['Contrato social'],
            'required_documents' => [
                'corporate_document' => UploadedFile::fake()->create('contrato-social.pdf', 100, 'application/pdf'),
                'legal_representative_document' => UploadedFile::fake()->create('representante.pdf', 100, 'application/pdf'),
            ],
            'compliance_policies' => ['Programa de Compliance estruturado'],
            'investigated_for' => ['Não'],
            'law_12846_compliant' => '1',
            'lgpd_compliant' => '1',
            'conflict_roles' => ['Nenhuma das opções acima'],
            'public_power_relatives' => 'nao',
            'internal_relationships' => 'nao',
            'employee_shareholding' => 'nao',
            'conflict_situation' => 'nao',
            'competitor_relationships' => 'nao',
            'contractor_shareholding' => 'nao',
            'friendship_ties' => 'nao',
            'legal_declaration' => 'concorda',
            'legal_representative' => 'Ana Responsável',
            'legal_representative_cpf' => '123.456.789-09',
            'legal_representative_role' => 'Diretora',
            'legal_representative_date' => '2026-07-28',
            'terms_accepted' => '1',
            'representation_authority_accepted' => '1',
            'documents' => [UploadedFile::fake()->create('contrato.pdf', 100, 'application/pdf')],
        ]);

        $response->assertRedirect(route('forms.success', ['form' => 'fornecedor-rh']));

        $submission = FormSubmission::latest()->first();
        $this->assertNotNull($submission);
        $this->assertSame('pj-rh', $submission->classification);
    }

    public function test_legacy_classification_values_are_normalized_for_admin_visibility(): void
    {
        $this->assertSame('pj-rh', FormSubmission::normalizeClassification('pj_colaborador', 'pj', 'rh'));
        $this->assertSame('pj-rh', FormSubmission::normalizeClassification('pj-rh', 'pj', 'rh'));
        $this->assertSame('pj', FormSubmission::normalizeClassification('pj_diverso', 'pj', 'public'));
        $this->assertSame('pj', FormSubmission::normalizeClassification(null, 'pj', 'public'));
        $this->assertSame('pf', FormSubmission::normalizeClassification(null, 'pf', 'public'));
    }

    public function test_rh_user_only_sees_pj_colaborador_classification(): void
    {
        $rhUser = User::factory()->create([
            'form_scope' => 'form-med',
            'admin_role' => 'rh',
        ]);
        $rhUser->markEmailAsVerified();

        FormSubmission::create([
            'form_type' => 'form-med',
            'registration_type' => 'pj',
            'classification' => 'pj-rh',
            'nome' => 'Colaborador Ltda',
            'email' => 'rh@example.com',
            'endereco' => 'Rua RH, 1',
            'nacionalidade' => 'Brasileira',
            'profissao' => 'Empresa',
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

        FormSubmission::create([
            'form_type' => 'form-med',
            'registration_type' => 'pj',
            'classification' => 'pj',
            'nome' => 'Diverso Ltda',
            'email' => 'diverso@example.com',
            'endereco' => 'Rua Diversa, 2',
            'nacionalidade' => 'Brasileira',
            'profissao' => 'Empresa',
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

        $response = $this->actingAs($rhUser)
            ->get(route('admin.submissions.index'));

        $response->assertOk();
        $response->assertSee('Colaborador Ltda');
        $response->assertDontSee('Diverso Ltda');
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
            'classification' => 'pf',
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

    public function test_admin_can_print_a_submission(): void
    {
        $user = User::factory()->create([
            'form_scope' => 'form-med',
        ]);
        $user->markEmailAsVerified();

        $submission = FormSubmission::create([
            'form_type' => 'form-med',
            'registration_type' => 'pf',
            'classification' => 'pf',
            'nome' => 'Cliente para Impressão',
            'email' => 'impressao@example.com',
            'endereco' => 'Rua da Impressão, 123',
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
            ->get(route('admin.submissions.print', $submission))
            ->assertOk()
            ->assertSee('Cliente para Impressão');
    }
}
