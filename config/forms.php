<?php

return [
    'form-med' => [
        'title' => 'Formulário de Qualificação e Cadastro',
        'description' => 'Questionário inicial para solicitação de cadastro e compliance.',
        'view' => 'form',
        'form_type' => 'form-med',
        'status' => 'online',
    ],

    'fornecedor-rh' => [
        'title' => 'Formulário de Qualificação e Cadastro - RH',
        'description' => 'Cadastro de colaborador PJ.',
        'view' => 'form',
        'form_type' => 'form-med',
        'status' => 'online',
        'submission_context' => 'rh',
        'restrict_registration_type' => 'pj',
    ],

    // Placeholder for the next form type; set status to "online" and point to the right view when ready.
    'regime-tributario' => [
        'title' => 'Regime Tributário',
        'description' => 'Identificação do regime tributário do fornecedor.',
        'view' => 'tax-regime-form',
        'status' => 'online',
    ],
];
