<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FormSubmission extends Model
{
    public static function normalizeClassification(?string $classification, ?string $registrationType = null, ?string $source = null): ?string
    {
        $normalized = is_string($classification) ? strtolower(trim($classification)) : null;

        return match (true) {
            in_array($normalized, ['pj-rh', 'pj_colaborador', 'pj colaborador', 'pj_colab', 'rh'], true) => 'pj-rh',
            in_array($normalized, ['pj', 'pj_diverso', 'pj diverso', 'diverso'], true) => 'pj',
            in_array($normalized, ['pf', 'pf pessoa fisica', 'pessoa fisica'], true) => 'pf',
            default => match (true) {
                ($source !== null && strtolower((string) $source) === 'rh') || ($registrationType !== null && strtolower((string) $registrationType) === 'pj' && strtolower((string) $source) === 'rh') => 'pj-rh',
                strtolower((string) ($registrationType ?? '')) === 'pj' => 'pj',
                strtolower((string) ($registrationType ?? '')) === 'pf' => 'pf',
                default => null,
            },
        };
    }

    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $submission): void {
            $submission->submission_hash ??= strtoupper((string) Str::uuid());
            $submission->submitted_ip ??= request()->ip() ?: request()->server('REMOTE_ADDR') ?: '0.0.0.0';
            $submission->submitted_location ??= request()->header('CF-IPCountry')
                ?: request()->header('X-Country-Code')
                ?: request()->header('X-Vercel-IP-Country')
                ?: 'Localização não informada';
        });
    }

    protected $fillable = [
        'submission_hash',
        'submitted_ip',
        'submitted_location',
        'verified',
        'form_type',
        'registration_type',
        'classification',
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
