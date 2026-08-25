<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\CustomForm;
use App\Models\FormField;
use App\Models\FormStep;
use Illuminate\Database\Seeder;

class TransferExistingFormsSeeder extends Seeder
{
    public function run(): void
    {
        $complianceArea = Area::firstOrCreate(
            ['slug' => 'form-med'],
            [
                'name' => 'Compliance',
                'description' => 'Formulário de Fornecedores e Documentos de Compliance',
                'default_route' => 'admin.submissions.index',
                'is_active' => true,
            ]
        );

        $financeArea = Area::firstOrCreate(
            ['slug' => 'regime-tributario'],
            [
                'name' => 'Financeiro',
                'description' => 'Declarações de Regime Tributário e Documentação Fiscal',
                'default_route' => 'admin.tax-regime.index',
                'is_active' => true,
            ]
        );

        // 1. Transferência do Formulário: form-med (Compliance - Fornecedores)
        $this->createComplianceForm($complianceArea, 'form-med', 'Formulário de Qualificação e Cadastro', 'Questionário inicial para solicitação de cadastro e compliance de fornecedores.', 'public', null);

        // 2. Transferência do Formulário: fornecedor-rh (Compliance - RH PJ)
        $this->createComplianceForm($complianceArea, 'fornecedor-rh', 'Formulário de Qualificação e Cadastro - RH', 'Cadastro e qualificação de colaboradores Pessoa Jurídica (PJ).', 'rh', 'pj');

        // 3. Transferência do Formulário: regime-tributario (Financeiro)
        $this->createTaxRegimeForm($financeArea);
    }

    private function createComplianceForm(Area $area, string $slug, string $title, string $description, string $context, ?string $restrictType): CustomForm
    {
        $form = CustomForm::updateOrCreate(
            ['slug' => $slug],
            [
                'area_id' => $area->id,
                'title' => $title,
                'description' => $description,
                'status' => 'online',
                'is_multi_step' => true,
                'submission_context' => $context,
                'restrict_registration_type' => $restrictType,
            ]
        );

        // Limpa etapas antigas para recriar de forma idempotente
        $form->steps()->delete();

        // ETAPA 1: Dados Cadastrais
        $step1 = FormStep::create([
            'custom_form_id' => $form->id,
            'title' => 'Dados cadastrais',
            'description' => 'Identificação da empresa ou pessoa física e informações para contato.',
            'order_index' => 1,
        ]);

        $fieldsStep1 = [
            [
                'name' => 'registration_type',
                'label' => 'Tipo de cadastro',
                'type' => 'radio',
                'is_required' => true,
                'options' => [
                    ['label' => 'Pessoa Jurídica (PJ)', 'value' => 'pj'],
                    ['label' => 'Pessoa Física (PF)', 'value' => 'pf'],
                ],
                'grid_columns' => 2,
                'order_index' => 1,
            ],
            [
                'name' => 'nome',
                'label' => 'Nome completo',
                'type' => 'text',
                'placeholder' => 'Seu nome completo',
                'is_required' => true,
                'conditional_logic' => ['show_if' => ['field' => 'registration_type', 'value' => 'pf']],
                'grid_columns' => 2,
                'order_index' => 2,
            ],
            [
                'name' => 'cpf',
                'label' => 'CPF',
                'type' => 'text',
                'placeholder' => '000.000.000-00',
                'is_required' => true,
                'conditional_logic' => ['show_if' => ['field' => 'registration_type', 'value' => 'pf']],
                'grid_columns' => 2,
                'order_index' => 3,
            ],
            [
                'name' => 'razao_social',
                'label' => 'Razão social',
                'type' => 'text',
                'placeholder' => 'Razão social da empresa',
                'is_required' => true,
                'conditional_logic' => ['show_if' => ['field' => 'registration_type', 'value' => 'pj']],
                'grid_columns' => 2,
                'order_index' => 4,
            ],
            [
                'name' => 'nome_fantasia',
                'label' => 'Nome fantasia',
                'type' => 'text',
                'placeholder' => 'Nome fantasia',
                'is_required' => false,
                'conditional_logic' => ['show_if' => ['field' => 'registration_type', 'value' => 'pj']],
                'grid_columns' => 2,
                'order_index' => 5,
            ],
            [
                'name' => 'cnpj',
                'label' => 'CNPJ',
                'type' => 'text',
                'placeholder' => '00.000.000/0000-00',
                'is_required' => true,
                'conditional_logic' => ['show_if' => ['field' => 'registration_type', 'value' => 'pj']],
                'grid_columns' => 2,
                'order_index' => 6,
            ],
            [
                'name' => 'representante_legal',
                'label' => 'Representante legal',
                'type' => 'text',
                'placeholder' => 'Nome completo do representante',
                'is_required' => true,
                'conditional_logic' => ['show_if' => ['field' => 'registration_type', 'value' => 'pj']],
                'grid_columns' => 2,
                'order_index' => 7,
            ],
            [
                'name' => 'representante_cpf',
                'label' => 'CPF do representante legal',
                'type' => 'text',
                'placeholder' => '000.000.000-00',
                'is_required' => true,
                'conditional_logic' => ['show_if' => ['field' => 'registration_type', 'value' => 'pj']],
                'grid_columns' => 2,
                'order_index' => 8,
            ],
            [
                'name' => 'website',
                'label' => 'Site / Redes sociais',
                'type' => 'text',
                'placeholder' => 'https://exemplo.com.br',
                'is_required' => false,
                'conditional_logic' => ['show_if' => ['field' => 'registration_type', 'value' => 'pj']],
                'grid_columns' => 2,
                'order_index' => 9,
            ],
            [
                'name' => 'email',
                'label' => 'E-mail corporativo / principal',
                'type' => 'email',
                'placeholder' => 'email@exemplo.com',
                'is_required' => true,
                'grid_columns' => 2,
                'order_index' => 10,
            ],
            [
                'name' => 'telefone',
                'label' => 'Telefone / WhatsApp',
                'type' => 'tel',
                'placeholder' => '(00) 00000-0000',
                'is_required' => true,
                'grid_columns' => 2,
                'order_index' => 11,
            ],
            [
                'name' => 'nacionalidade',
                'label' => 'Nacionalidade',
                'type' => 'text',
                'placeholder' => 'Ex: Brasileira',
                'is_required' => true,
                'grid_columns' => 2,
                'order_index' => 12,
            ],
            [
                'name' => 'profissao',
                'label' => 'Profissão / Ramo de atividade',
                'type' => 'text',
                'placeholder' => 'Ex: Consultoria / Serviços Médicos',
                'is_required' => true,
                'grid_columns' => 2,
                'order_index' => 13,
            ],
            [
                'name' => 'data_nascimento',
                'label' => 'Data de nascimento / Fundação',
                'type' => 'date',
                'is_required' => false,
                'grid_columns' => 2,
                'order_index' => 14,
            ],
            [
                'name' => 'endereco',
                'label' => 'Endereço completo',
                'type' => 'textarea',
                'placeholder' => 'Rua, número, complemento, bairro, cidade - UF, CEP',
                'is_required' => true,
                'grid_columns' => 1,
                'order_index' => 15,
            ],
            [
                'name' => 'dados_bancarios',
                'label' => 'Dados bancários (Banco, Agência, Conta, Chave PIX)',
                'type' => 'textarea',
                'placeholder' => 'Banco: 001, Agência: 1234, Conta: 56789-0, PIX: chave@exemplo.com',
                'is_required' => false,
                'grid_columns' => 1,
                'order_index' => 16,
            ],
        ];

        foreach ($fieldsStep1 as $fieldData) {
            $step1->fields()->create($fieldData);
        }

        // ETAPA 2: Compliance e Conflito de Interesses
        $step2 = FormStep::create([
            'custom_form_id' => $form->id,
            'title' => 'Compliance e Conflito de Interesses',
            'description' => 'Declarações e questionário de conformidade e integridade corporativa.',
            'order_index' => 2,
        ]);

        $fieldsStep2 = [
            [
                'name' => 'compliance_policies',
                'label' => 'Quais políticas de compliance você ou sua empresa possuem?',
                'type' => 'checkbox',
                'options' => [
                    'Código de Ética e Conduta',
                    'Política Anticorrupção',
                    'Política de Privacidade e Proteção de Dados (LGPD)',
                    'Canal de Denúncias',
                    'Nenhuma das anteriores',
                ],
                'grid_columns' => 1,
                'order_index' => 1,
            ],
            [
                'name' => 'investigated_for',
                'label' => 'Você ou sua empresa já foram investigados ou responderam a processos por:',
                'type' => 'checkbox',
                'options' => [
                    'Fraude ou Corrupção',
                    'Trabalho Infantil ou Análogo à Escravidão',
                    'Crimes Ambientais',
                    'Improbidade Administrativa',
                    'Não, nenhuma das opções acima',
                ],
                'grid_columns' => 1,
                'order_index' => 2,
            ],
            [
                'name' => 'investigation_details',
                'label' => 'Se respondeu positivamente a alguma investigação acima, favor detalhar:',
                'type' => 'textarea',
                'is_required' => false,
                'grid_columns' => 1,
                'order_index' => 3,
            ],
            [
                'name' => 'law_12846_compliant',
                'label' => 'Declara estar ciente e em conformidade com as diretrizes da Lei nº 12.846/2013 (Lei Anticorrupção)?',
                'type' => 'radio',
                'is_required' => true,
                'options' => [
                    ['label' => 'Sim, estou em conformidade', 'value' => '1'],
                    ['label' => 'Não', 'value' => '0'],
                ],
                'grid_columns' => 2,
                'order_index' => 4,
            ],
            [
                'name' => 'lgpd_compliant',
                'label' => 'Declara estar ciente e em conformidade com a Lei nº 13.709/2018 (Lei Geral de Proteção de Dados - LGPD)?',
                'type' => 'radio',
                'is_required' => true,
                'options' => [
                    ['label' => 'Sim, estou em conformidade', 'value' => '1'],
                    ['label' => 'Não', 'value' => '0'],
                ],
                'grid_columns' => 2,
                'order_index' => 5,
            ],
            [
                'name' => 'conflict_roles',
                'label' => 'Identificação de eventuais vínculos ou papéis com a nossa organização:',
                'type' => 'checkbox',
                'options' => [
                    'Agente Público ou Posição de Governo',
                    'Fornecedor de Concorrente Direto',
                    'Sócio ou Familiar de Colaborador Interno',
                    'Nenhuma das opções acima',
                ],
                'grid_columns' => 1,
                'order_index' => 6,
            ],
            [
                'name' => 'public_power_relatives',
                'label' => 'Possui cônjuge, companheiro ou parente até 3º grau que exerça cargo ou função pública de relevância?',
                'type' => 'radio',
                'is_required' => true,
                'options' => [
                    ['label' => 'Não', 'value' => 'nao'],
                    ['label' => 'Sim', 'value' => 'sim'],
                ],
                'grid_columns' => 2,
                'order_index' => 7,
            ],
            [
                'name' => 'internal_relationships',
                'label' => 'Possui relacionamento de parentesco, afetivo ou societário com colaboradores da nossa instituição?',
                'type' => 'radio',
                'is_required' => true,
                'options' => [
                    ['label' => 'Não', 'value' => 'nao'],
                    ['label' => 'Sim', 'value' => 'sim'],
                ],
                'grid_columns' => 2,
                'order_index' => 8,
            ],
            [
                'name' => 'employee_shareholding',
                'label' => 'Algum colaborador nosso possui participação societária ou gestão em sua empresa?',
                'type' => 'radio',
                'is_required' => true,
                'options' => [
                    ['label' => 'Não', 'value' => 'nao'],
                    ['label' => 'Sim', 'value' => 'sim'],
                ],
                'grid_columns' => 2,
                'order_index' => 9,
            ],
            [
                'name' => 'conflict_situation',
                'label' => 'Existe qualquer outra situação que possa configurar conflito de interesses?',
                'type' => 'radio',
                'is_required' => true,
                'options' => [
                    ['label' => 'Não', 'value' => 'nao'],
                    ['label' => 'Sim', 'value' => 'sim'],
                ],
                'grid_columns' => 2,
                'order_index' => 10,
            ],
            [
                'name' => 'conflict_details',
                'label' => 'Se respondeu "Sim" para alguma situação de conflito, descreva detalhadamente:',
                'type' => 'textarea',
                'is_required' => false,
                'grid_columns' => 1,
                'order_index' => 11,
            ],
        ];

        foreach ($fieldsStep2 as $fieldData) {
            $step2->fields()->create($fieldData);
        }

        // ETAPA 3: Termos e Condições
        $step3 = FormStep::create([
            'custom_form_id' => $form->id,
            'title' => 'Termos e Condições',
            'description' => 'Aceite dos termos legais, código de conduta e envio final.',
            'order_index' => 3,
        ]);

        $fieldsStep3 = [
            [
                'name' => 'legal_declaration',
                'label' => 'Declaração de veracidade',
                'type' => 'radio',
                'is_required' => true,
                'options' => [
                    ['label' => 'Declaro que todas as informações prestadas são verdadeiras e completas.', 'value' => 'concorda'],
                ],
                'grid_columns' => 1,
                'order_index' => 1,
            ],
            [
                'name' => 'testemunha_nome',
                'label' => 'Nome do responsável pelo preenchimento / Testemunha',
                'type' => 'text',
                'placeholder' => 'Nome completo',
                'is_required' => false,
                'grid_columns' => 2,
                'order_index' => 2,
            ],
            [
                'name' => 'testemunha_email',
                'label' => 'E-mail do responsável / Testemunha',
                'type' => 'email',
                'placeholder' => 'email@exemplo.com',
                'is_required' => false,
                'grid_columns' => 2,
                'order_index' => 3,
            ],
        ];

        foreach ($fieldsStep3 as $fieldData) {
            $step3->fields()->create($fieldData);
        }

        return $form;
    }

    private function createTaxRegimeForm(Area $area): CustomForm
    {
        $form = CustomForm::updateOrCreate(
            ['slug' => 'regime-tributario'],
            [
                'area_id' => $area->id,
                'title' => 'Regime Tributário',
                'description' => 'Identificação do regime tributário do fornecedor e declaração fiscal.',
                'status' => 'online',
                'is_multi_step' => false,
                'submission_context' => 'financeiro',
            ]
        );

        $form->steps()->delete();

        $step = FormStep::create([
            'custom_form_id' => $form->id,
            'title' => 'Dados Fiscais e Regime Tributário',
            'description' => 'Preencha as informações do enquadramento tributário da sua empresa.',
            'order_index' => 1,
        ]);

        $fields = [
            [
                'name' => 'razao_social',
                'label' => 'Razão social',
                'type' => 'text',
                'placeholder' => 'Razão social da empresa',
                'is_required' => true,
                'grid_columns' => 2,
                'order_index' => 1,
            ],
            [
                'name' => 'cnpj',
                'label' => 'CNPJ',
                'type' => 'text',
                'placeholder' => '00.000.000/0000-00',
                'is_required' => true,
                'grid_columns' => 2,
                'order_index' => 2,
            ],
            [
                'name' => 'regime_tributario',
                'label' => 'Regime Tributário',
                'type' => 'radio',
                'is_required' => true,
                'options' => [
                    'simples_nacional' => 'Simples Nacional',
                    'lucro_presumido' => 'Lucro Presumido',
                    'lucro_real' => 'Lucro Real',
                    'mei' => 'MEI (Microempreendedor Individual)',
                ],
                'grid_columns' => 2,
                'order_index' => 3,
            ],
            [
                'name' => 'enquadramento_anexo',
                'label' => 'Anexo de Enquadramento (se aplicável ao Simples)',
                'type' => 'text',
                'placeholder' => 'Ex: Anexo III / Anexo V',
                'is_required' => false,
                'grid_columns' => 2,
                'order_index' => 4,
            ],
            [
                'name' => 'comprovante_regime',
                'label' => 'Comprovante / Certidão de Opção pelo Regime Tributário',
                'type' => 'file',
                'is_required' => false,
                'grid_columns' => 1,
                'order_index' => 5,
            ],
            [
                'name' => 'declaracao_veracidade',
                'label' => 'Declaração de veracidade fiscal',
                'type' => 'radio',
                'is_required' => true,
                'options' => [
                    'concorda' => 'Declaro sob as penas da lei a exatidão e veracidade do regime tributário informado.',
                ],
                'grid_columns' => 1,
                'order_index' => 6,
            ],
        ];

        foreach ($fields as $fieldData) {
            $step->fields()->create($fieldData);
        }

        return $form;
    }
}
