<?php

namespace App\Exports;

use App\Models\FormSubmission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FormSubmissionsExport implements FromCollection, WithHeadings, WithMapping
{
    private array $filters;
    private array $formCatalog;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->formCatalog = config('forms', []);
    }

    public function collection(): Collection
    {
        $query = FormSubmission::query()->orderByDesc('created_at');

        if (!empty($this->filters['email'])) {
            $query->where('email', 'like', '%'.$this->filters['email'].'%');
        }

        if (!empty($this->filters['name'])) {
            $name = $this->filters['name'];
            $query->where(function ($q) use ($name) {
                $q->where('nome', 'like', '%'.$name.'%')
                    ->orWhere('razao_social', 'like', '%'.$name.'%')
                    ->orWhere('nome_fantasia', 'like', '%'.$name.'%');
            });
        }

        if (!empty($this->filters['registration_type']) && in_array($this->filters['registration_type'], ['pf', 'pj'], true)) {
            $query->where('registration_type', $this->filters['registration_type']);
        }

        if (!empty($this->filters['form_type'])) {
            $query->where('form_type', $this->filters['form_type']);
        }

        if (!empty($this->filters['from'])) {
            $query->whereDate('created_at', '>=', $this->filters['from']);
        }

        if (!empty($this->filters['to'])) {
            $query->whereDate('created_at', '<=', $this->filters['to']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Formulário',
            'Tipo Cadastro',
            'Nome / Razão social',
            'CPF',
            'Razão Social',
            'Nome Fantasia',
            'CNPJ',
            'Representante Legal',
            'Website',
            'Endereço',
            'Email',
            'Email Testemunha',
            'Telefone',
            'Nacionalidade',
            'Profissão',
            'Data Nascimento',
            'Dados Bancários',
            'Mensagem',
            'Checklist Docs',
            'Políticas Compliance',
            'Investigado por',
            'Detalhes investigação',
            'Lei 12.846',
            'LGPD',
            'Conflito - perfis',
            'Conflito - detalhes perfis',
            'Parentes em órgão público',
            'Detalhes parentes órgão público',
            'Relacionamento interno',
            'Detalhes relacionamento interno',
            'Participação colaborador',
            'Detalhes participação colaborador',
            'Situação de conflito',
            'Detalhes situação de conflito',
            'Relacionamento com concorrente',
            'Detalhes relacionamento concorrente',
            'Participação na contratante',
            'Detalhes participação na contratante',
            'Laços de amizade/parentesco',
            'Detalhes laços',
            'Declaração legal',
            'Responsável legal',
            'CPF responsável legal',
            'Cargo responsável legal',
            'Data assinatura',
            'Qtd Documentos',
            'Criado em',
        ];
    }

    public function map($submission): array
    {
        $join = static function ($value) {
            return is_array($value) ? implode('; ', $value) : $value;
        };

        return [
            $submission->id,
            $this->formName($submission->form_type),
            $submission->registration_type,
            $submission->nome,
            $submission->cpf,
            $submission->razao_social,
            $submission->nome_fantasia,
            $submission->cnpj,
            $submission->representante_legal,
            $submission->website,
            $submission->endereco,
            $submission->email,
            $submission->email_testemunha,
            $submission->telefone,
            $submission->nacionalidade,
            $submission->profissao,
            optional($submission->data_nascimento)->format('Y-m-d'),
            $submission->dados_bancarios,
            $submission->mensagem,
            $join($submission->doc_checklist),
            $join($submission->compliance_policies),
            $submission->investigated_for,
            $submission->investigation_details,
            $submission->law_12846_compliant === null ? '' : ($submission->law_12846_compliant ? 'Sim' : 'Não'),
            $submission->lgpd_compliant === null ? '' : ($submission->lgpd_compliant ? 'Sim' : 'Não'),
            $join($submission->conflict_roles),
            $submission->conflict_roles_details,
            $submission->public_power_relatives,
            $submission->public_power_relatives_details,
            $submission->internal_relationships,
            $submission->internal_relationships_details,
            $submission->employee_shareholding,
            $submission->employee_shareholding_details,
            $submission->conflict_situation,
            $submission->conflict_situation_details,
            $submission->competitor_relationships,
            $submission->competitor_relationships_details,
            $submission->contractor_shareholding,
            $submission->contractor_shareholding_details,
            $submission->friendship_ties,
            $submission->friendship_ties_details,
            $submission->legal_declaration,
            $submission->legal_representative,
            $submission->legal_representative_cpf,
            $submission->legal_representative_role,
            optional($submission->legal_representative_date)->format('Y-m-d'),
            is_array($submission->documents) ? count($submission->documents) : 0,
            optional($submission->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    private function formName(?string $slug): string
    {
        if (!$slug) {
            return '';
        }

        $form = $this->formCatalog[$slug] ?? null;

        return $form['title'] ?? $slug;
    }
}
