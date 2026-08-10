<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRegimeSubmission extends Model
{
    protected $fillable = [
        'verified',
        'razao_social',
        'cnpj',
        'regime_tributario',
        'lc_214_2025_compliant',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'lc_214_2025_compliant' => 'boolean',
    ];
}