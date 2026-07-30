<?php

return [
    'form-med' => [
        'label' => 'Compliance',
        'dashboard_title' => 'Formulário de Fornecedores',
        'count_model' => \App\Models\FormSubmission::class,
        'nav_items' => [
            ['route' => 'admin.submissions.index', 'pattern' => 'admin.submissions.*', 'label' => 'Submissões'],
            ['route' => 'admin.compliance.index', 'pattern' => 'admin.compliance.*', 'label' => 'Documentos'],
        ],
        'default_route' => 'admin.submissions.index',
    ],

    'regime-tributario' => [
        'label' => 'Financeiro',
        'dashboard_title' => 'Regime Tributário',
        'count_model' => \App\Models\TaxRegimeSubmission::class,
        'nav_items' => [
            ['route' => 'admin.tax-regime.index', 'pattern' => 'admin.tax-regime.*', 'label' => 'Regime Tributário'],
        ],
        'default_route' => 'admin.tax-regime.index',
    ],
];