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
    'novo-formulario' => [
        'title' => 'Novo formulário (em preparação)',
        'description' => 'Entrada reservada para o próximo formulário corporativo.',
        'view' => 'forms.coming-soon',
        'form_type' => 'novo-formulario',
        'status' => 'draft',
    ],
];
