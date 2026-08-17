<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'verified',
        'form_type',
        'registration_type',
        'nome',
        'cpf',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'representante_legal',
        'representante_legal_nome',
        'representante_legal_email',
        'responsavel_juridico_nome',
        'responsavel_juridico_email',
        'testemunha_nome',
        'testemunha_email',
        'website',
        'endereco',
        'email',
        'email_testemunha',
        'telefone',
        'nacionalidade',
        'profissao',
        'data_nascimento',
        'dados_bancarios',
        'mensagem',
        'doc_checklist',
        'compliance_policies',
        'investigated_for',
        'investigation_details',
        'law_12846_compliant',
        'lgpd_compliant',
        'conflict_roles',
        'conflict_roles_details',
        'public_power_relatives',
        'public_power_relatives_details',
        'internal_relationships',
        'internal_relationships_details',
        'employee_shareholding',
        'employee_shareholding_details',
        'conflict_situation',
        'conflict_situation_details',
        'competitor_relationships',
        'competitor_relationships_details',
        'contractor_shareholding',
        'contractor_shareholding_details',
        'friendship_ties',
        'friendship_ties_details',
        'legal_declaration',
        'legal_representative',
        'legal_representative_cpf',
        'legal_representative_role',
        'legal_representative_date',
        'compliance_aceito_em',
        'documents',
        'required_documents',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'documents' => 'array',
        'required_documents' => 'array',
        'doc_checklist' => 'array',
        'compliance_policies' => 'array',
        'conflict_roles' => 'array',
        'data_nascimento' => 'date',
        'law_12846_compliant' => 'boolean',
        'lgpd_compliant' => 'boolean',
        'legal_representative_date' => 'date',
        'compliance_aceito_em' => 'datetime',
    ];
}
