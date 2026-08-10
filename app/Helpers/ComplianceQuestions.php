<?php

namespace App\Helpers;

class ComplianceQuestions
{
    /**
     * Map database field names to complete question text
     */
    public static function getQuestions()
    {
        return [
            'compliance_policies' => [
                'number' => 1,
                'title' => 'A PJ/PF possui algum dos seguintes documentos ou programas?',
                'subtitle' => '(Marque todas as opções aplicáveis.)',
                'type' => 'checkbox',
            ],
            'law_12846_compliant' => [
                'number' => 2,
                'title' => 'A PJ/PF declara estar em conformidade com a Lei nº 12.846/2013 (Lei Anticorrupção)?',
                'type' => 'radio',
            ],
            'lgpd_compliant' => [
                'number' => 3,
                'title' => 'A PJ/PF declara estar em conformidade com a Lei nº 13.709/2018 (Lei Geral de Proteção de Dados – LGPD)?',
                'type' => 'radio',
            ],
            'investigated_for' => [
                'number' => 4,
                'title' => 'A PJ/PF ou, no caso de pessoa jurídica, algum de seus sócios, administradores ou representantes, já foi investigado ou condenado por algum dos fatos abaixo?',
                'subtitle' => '(Marque todas as opções aplicáveis.)',
                'type' => 'checkbox',
                'details_field' => 'conflict_roles_details',
                'details_label' => 'Caso tenha assinalado qualquer opção diferente de "Não", descreva os detalhes:',
            ],
            'conflict_roles' => [
                'number' => 5,
                'title' => 'A PJ/PF ou, no caso de pessoa jurídica, algum de seus sócios, administradores ou representantes, enquadra-se em alguma das situações abaixo?',
                'subtitle' => '(Marque todas as opções aplicáveis.)',
                'type' => 'checkbox',
                'details_field' => 'conflict_roles_details',
                'details_label' => 'Caso positivo, informe os detalhes:',
            ],
            'public_power_relatives' => [
                'number' => 6,
                'title' => 'A PJ/PF ou, no caso de pessoa jurídica, algum de seus sócios, administradores ou representantes, ocupa cargo ou mantém vínculo familiar com pessoa em posição de decisão em órgão ou entidade pública que possua relação com a Vitória Hospitalar?',
                'type' => 'radio',
                'details_field' => 'public_power_relatives_details',
                'details_label' => 'Se sim, informe o órgão, cargo e/ou nome do servidor:',
            ],
            'internal_relationships' => [
                'number' => 7,
                'title' => 'A PJ/PF possui relacionamento pessoal, familiar ou comercial com sócios, administradores ou colaboradores da Vitória Hospitalar?',
                'type' => 'radio',
                'details_field' => 'internal_relationships_details',
                'details_label' => 'Se sim, informe a pessoa, área e a natureza do relacionamento:',
            ],
            'employee_shareholding' => [
                'number' => 8,
                'title' => 'A PJ/PF ou, no caso de pessoa jurídica, seus sócios ou administradores, possuem participação societária, direta ou indireta, na Vitória Hospitalar?',
                'type' => 'radio',
                'details_field' => 'employee_shareholding_details',
                'details_label' => 'Se sim, informe os detalhes:',
            ],
            'competitor_relationships' => [
                'number' => 9,
                'title' => 'A PJ/PF possui relacionamento pessoal, familiar ou comercial com sócios, administradores ou colaboradores de empresas concorrentes da Vitória Hospitalar?',
                'type' => 'radio',
                'details_field' => 'competitor_relationships_details',
                'details_label' => 'Se sim, informe os detalhes:',
            ],
            'conflict_situation' => [
                'number' => 10,
                'title' => 'Existe qualquer situação envolvendo a PJ/PF que possa caracterizar conflito de interesses real, potencial ou aparente em relação à Vitória Hospitalar?',
                'type' => 'radio',
            ],
        ];
    }

    /**
     * Get question title and number by field name
     */
    public static function getQuestion($fieldName)
    {
        $questions = self::getQuestions();
        return $questions[$fieldName] ?? null;
    }

    /**
     * Get full question display with number
     */
    public static function getFullQuestionText($fieldName)
    {
        $question = self::getQuestion($fieldName);
        if (!$question) {
            return null;
        }

        $text = "<strong>{$question['number']}. {$question['title']}</strong>";
        
        if (isset($question['subtitle'])) {
            $text .= " <em>{$question['subtitle']}</em>";
        }

        return $text;
    }

    /**
     * Get compliance policy options
     */
    public static function getCompliancePolicies()
    {
        return [
            'Código de Ética ou Conduta',
            'Programa de Compliance estruturado',
            'Canal de Denúncias',
            'Política Anticorrupção',
            'Política de Conflito de Interesses',
            'Política de Proteção de Dados (LGPD)',
        ];
    }

    /**
     * Get investigation options
     */
    public static function getInvestigationOptions()
    {
        return [
            'Corrupção',
            'Fraude',
            'Lavagem de dinheiro',
            'Crimes ambientais',
            'Infrações trabalhistas graves',
            'Não',
        ];
    }

    /**
     * Get conflict role options
     */
    public static function getConflictRoleOptions()
    {
        return [
            'Agente Público',
            'Ex-Agente Público (nos últimos 5 anos)',
            'Pessoa Politicamente Exposta (PPE)',
            'Parente até o 3º grau de Agente Público',
            'Nenhuma das opções acima',
        ];
    }
}
