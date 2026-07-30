<?php

return [
    'form-med' => [
        'title' => 'Cadastro de Fornecedores – VH',
        'description' => 'Questionário inicial para solicitação de cadastro e compliance.',
        'view' => 'form',
        'form_type' => 'form-med',
        'status' => 'online',
    ],

    // Placeholder for the next form type; set status to "online" and point to the right view when ready.
        'regime-tributario' => [
        'title' => 'Regime Tributário',
        'description' => 'Identificação do regime tributário do fornecedor.',
        'view' => 'tax-regime-form',
        'status' => 'online',
    ],
];
