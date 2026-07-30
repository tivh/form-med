<?php

namespace App\Exports;

use App\Models\TaxRegimeSubmission;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TaxRegimeSubmissionsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters = []) {}

    public function collection(): Collection
    {
        $query = TaxRegimeSubmission::query()->orderByDesc('created_at');

        if (!empty($this->filters['razao_social'])) {
            $query->where('razao_social', 'like', '%'.$this->filters['razao_social'].'%');
        }
        if (!empty($this->filters['cnpj'])) {
            $query->where('cnpj', 'like', '%'.$this->filters['cnpj'].'%');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['ID', 'Razão Social', 'CNPJ', 'Regime Tributário', 'Adequado à LC 214/2025', 'Enviado em'];
    }

    public function map($submission): array
    {
        return [
            $submission->id,
            $submission->razao_social,
            $submission->cnpj,
            $submission->regime_tributario,
            $submission->lc_214_2025_compliant === null ? '' : ($submission->lc_214_2025_compliant ? 'Sim' : 'Não'),
            optional($submission->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}