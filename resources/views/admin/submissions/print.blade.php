<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resposta do Formulário - {{ $submission->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            color: #1f2937;
            line-height: 1.6;
            background: white;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 40px 30px;
            background: white;
        }
        
        .header {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #dc2626;
        }
        
        .header h1 {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .header-meta {
            display: flex;
            gap: 30px;
            font-size: 13px;
            color: #6b7280;
            flex-wrap: wrap;
        }
        
        .meta-item {
            display: flex;
            gap: 5px;
        }
        
        .meta-label {
            font-weight: 600;
            color: #111827;
        }
        
        .section {
            margin-bottom: 35px;
            page-break-inside: avoid;
        }
        
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dc2626;
        }
        
        .section-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #dc2626;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .section-content {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 6px;
        }
        
        .section-content.full {
            grid-template-columns: 1fr;
        }
        
        .field {
            display: flex;
            flex-direction: column;
        }
        
        .field.full-width {
            grid-column: 1 / -1;
        }
        
        .field-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            margin-bottom: 6px;
        }
        
        .field-value {
            font-size: 13px;
            color: #1f2937;
            font-weight: 500;
            line-height: 1.5;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .field-value.empty {
            color: #9ca3af;
            font-style: italic;
        }

        .compliance-answer {
            display: block;
            font-size: 13px;
            font-weight: 600;
        }

        .compliance-answer-status {
            display: block;
            margin-top: 4px;
        }

        .terms-content {
            margin-top: 8px;
            font-size: 12px;
            font-weight: 400;
            color: #374151;
            white-space: pre-wrap;
        }

        .compliance-answer.yes {
            color: #15803d;
        }

        .compliance-answer.no {
            color: #b91c1c;
        }
        
        .list-item {
            font-size: 13px;
            color: #374151;
            margin-left: 15px;
            margin-bottom: 5px;
        }
        
        .subsection-title {
            font-size: 12px;
            font-weight: 700;
            color: #1f2937;
            margin-top: 15px;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-yes {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-no {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 20px;
            }
            .section {
                page-break-inside: avoid;
            }
            a {
                text-decoration: none;
                color: inherit;
            }
        }
        
        @media (max-width: 768px) {
            .section-content {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @php $isPj = $submission->registration_type === 'pj'; @endphp
        
        <!-- HEADER -->
        <div class="header">
            <h1>{{ $isPj ? 'Cadastro da Empresa' : 'Cadastro do Fornecedor' }}</h1>
            <div class="header-meta">
                <div class="meta-item">
                    <span class="meta-label">ID:</span>
                    <span>#{{ $submission->id }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Data:</span>
                    <span>{{ optional($submission->created_at)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tipo:</span>
                    <span>{{ $isPj ? 'Pessoa Jurídica' : 'Pessoa Física' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Status:</span>
                    <span>{{ $submission->verified ? 'Verificado' : 'Pendente' }}</span>
                </div>
            </div>
        </div>

        <!-- SEÇÃO 1 -->
        <div class="section">
            <div class="section-header">
                <div class="section-number">1</div>
                <div class="section-title">{{ $isPj ? 'Identificação da Empresa' : 'Identificação Pessoal' }}</div>
            </div>
            
            <div class="section-content {{ $isPj ? '' : 'full' }}">
                @if($isPj)
                    <div class="field full-width">
                        <div class="field-label">Razão Social</div>
                        <div class="field-value">{{ $submission->razao_social ?: '—' }}</div>
                    </div>
                    <div class="field full-width">
                        <div class="field-label">Nome Fantasia</div>
                        <div class="field-value">{{ $submission->nome_fantasia ?: '—' }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">CNPJ</div>
                        <div class="field-value">{{ $submission->cnpj ?: '—' }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Website</div>
                        <div class="field-value">{{ $submission->website ?: '—' }}</div>
                    </div>
                @else
                    <div class="field full-width">
                        <div class="field-label">Nome Completo</div>
                        <div class="field-value">{{ $submission->nome ?: '—' }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">CPF</div>
                        <div class="field-value">{{ $submission->cpf ?: '—' }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Data de Nascimento</div>
                        <div class="field-value">{{ optional($submission->data_nascimento)->format('d/m/Y') ?: '—' }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Nacionalidade</div>
                        <div class="field-value">{{ $submission->nacionalidade ?: '—' }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Profissão</div>
                        <div class="field-value">{{ $submission->profissao ?: '—' }}</div>
                    </div>
                @endif
                
                <div class="field full-width">
                    <div class="field-label">Endereço</div>
                    <div class="field-value">{{ $submission->endereco ?: '—' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">E-mail</div>
                    <div class="field-value">{{ $submission->email ?: '—' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Telefone</div>
                    <div class="field-value">{{ $submission->telefone ?: '—' }}</div>
                </div>
            </div>
        </div>

        <!-- SEÇÃO 2: DOCUMENTAÇÃO OBRIGATÓRIA -->
        <div class="section">
            <div class="section-header">
                <div class="section-number">2</div>
                <div class="section-title">Documentação obrigatória</div>
            </div>

            <div class="section-content full">
                @php
                    $requiredDocumentLabels = [
                        'personal_documents' => 'Documento pessoal (CNH ou RG)',
                        'corporate_document' => 'Contrato social ou Estatuto social',
                        'legal_representative_document' => 'Documento do representante legal (CNH ou RG)',
                    ];
                @endphp

                @if(is_array($submission->required_documents) && count($submission->required_documents))
                    @foreach ($submission->required_documents as $key => $doc)
                        <div class="field full-width">
                            <div class="field-label">{{ $requiredDocumentLabels[$key] ?? 'Documento obrigatório' }}</div>
                            <div class="field-value">{{ $doc['original_name'] ?? 'Arquivo enviado' }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="field full-width">
                        <div class="field-value empty">Nenhum documento obrigatório anexado</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- SEÇÃO 3 (apenas PJ) -->
        @if($isPj)
        <div class="section">
            <div class="section-header">
                <div class="section-number">3</div>
                <div class="section-title">Representantes Legais</div>
            </div>
            
            <div class="section-content full">
                <div class="subsection-title">Representante Legal</div>
                <div class="field full-width">
                    <div class="field-label">Nome</div>
                    <div class="field-value">{{ $submission->representante_legal_nome ?: '—' }}</div>
                </div>
                <div class="field full-width">
                    <div class="field-label">E-mail</div>
                    <div class="field-value">{{ $submission->representante_legal_email ?: '—' }}</div>
                </div>
                
                <div class="subsection-title">Responsável Jurídico</div>
                <div class="field full-width">
                    <div class="field-label">Nome</div>
                    <div class="field-value">{{ $submission->responsavel_juridico_nome ?: '—' }}</div>
                </div>
                <div class="field full-width">
                    <div class="field-label">E-mail</div>
                    <div class="field-value">{{ $submission->responsavel_juridico_email ?: '—' }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- SEÇÃO 4 -->
        <div class="section">
            <div class="section-header">
                <div class="section-number">{{ $isPj ? '4' : '3' }}</div>
                <div class="section-title">Testemunha e Dados Financeiros</div>
            </div>
            
            <div class="section-content full">
                <div class="field">
                    <div class="field-label">Nome da Testemunha</div>
                    <div class="field-value">{{ $submission->testemunha_nome ?: '—' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">E-mail da Testemunha</div>
                    <div class="field-value">{{ $submission->testemunha_email ?: '—' }}</div>
                </div>
                <div class="field full-width">
                    <div class="field-label">Dados Bancários</div>
                    <div class="field-value">{{ $submission->dados_bancarios ?: '—' }}</div>
                </div>
            </div>
        </div>

        <!-- SEÇÃO 5: COMPLIANCE -->
        <div class="section">
            <div class="section-header">
                <div class="section-number">{{ $isPj ? '5' : '4' }}</div>
                <div class="section-title">Compliance e Conformidades</div>
            </div>
            
            <div class="section-content full">
                <div class="field">
                    <div class="field-value">
                        @if($submission->law_12846_compliant === null)
                            <span class="empty">A PJ/PF está em conformidade com a Lei 12.846/2013 (Lei Anticorrupção)? —</span>
                        @else
                            <span class="compliance-answer {{ $submission->law_12846_compliant ? 'yes' : 'no' }}">
                                A PJ/PF está em conformidade com a Lei 12.846/2013 (Lei Anticorrupção)?
                                <span class="compliance-answer-status">{{ $submission->law_12846_compliant ? '✓ Sim' : '✗ Não' }}</span>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="field">
                    <div class="field-value">
                        @if($submission->lgpd_compliant === null)
                            <span class="empty">A PJ/PF está em conformidade com a Lei 13.709/2018 (LGPD)? —</span>
                        @else
                            <span class="compliance-answer {{ $submission->lgpd_compliant ? 'yes' : 'no' }}">
                                A PJ/PF está em conformidade com a Lei 13.709/2018 (LGPD)?
                                <span class="compliance-answer-status">{{ $submission->lgpd_compliant ? '✓ Sim' : '✗ Não' }}</span>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="field full-width">
                    <div class="field-label">Investigação Anterior</div>
                    <div class="field-value">{{ $submission->investigated_for ?: '—' }}</div>
                    @if($submission->investigation_details)
                        <div class="field-value" style="margin-top: 8px;">{{ $submission->investigation_details }}</div>
                    @endif
                </div>
                
                <div class="field full-width">
                    <div class="field-label">Checklist de Documentos</div>
                    @if(is_array($submission->doc_checklist) && count($submission->doc_checklist))
                        <div>
                            @foreach ($submission->doc_checklist as $item)
                                <div class="list-item">✓ {{ $item }}</div>
                            @endforeach
                        </div>
                    @else
                        <div class="field-value empty">Nenhum item marcado</div>
                    @endif
                </div>
                
                <div class="field full-width">
                    <div class="field-label">Políticas de Compliance</div>
                    @if(is_array($submission->compliance_policies) && count($submission->compliance_policies))
                        <div>
                            @foreach ($submission->compliance_policies as $item)
                                <div class="list-item">✓ {{ $item }}</div>
                            @endforeach
                        </div>
                    @else
                        <div class="field-value empty">Nenhuma política registrada</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- SEÇÃO 6: CONFLITO DE INTERESSES -->
        <div class="section">
            <div class="section-header">
                <div class="section-number">{{ $isPj ? '6' : '5' }}</div>
                <div class="section-title">Conflito de Interesses</div>
            </div>
            
            <div class="section-content full">
                <!-- Pergunta 1 -->
                <div class="field full-width">
                    <div class="field-label"><strong>1. A PJ/PF possui algum dos seguintes documentos ou programas?</strong></div>
                    @php
                        $policyOptions = [
                            'Código de Ética ou Conduta',
                            'Programa de Compliance estruturado',
                            'Canal de Denúncias',
                            'Política Anticorrupção',
                            'Política de Conflito de Interesses',
                            'Política de Proteção de Dados (LGPD)',
                            'Nenhum',
                        ];
                        $selectedPolicies = is_array($submission->compliance_policies) ? $submission->compliance_policies : [];
                    @endphp
                    <div>
                        @foreach ($policyOptions as $option)
                            <div class="list-item">{{ in_array($option, $selectedPolicies, true) ? '☒' : '☐' }} {{ $option === 'Nenhum' ? 'Nenhum dos itens acima' : $option }}</div>
                        @endforeach
                    </div>
                </div>

                <!-- Pergunta 2 -->
                <div class="field">
                    <div class="field-label"><strong>2. Lei 12.846/2013 (Lei Anticorrupção)</strong></div>
                    <div class="field-value">
                        @if($submission->law_12846_compliant === null)
                            <span class="empty">—</span>
                        @else
                            <span class="status-badge {{ $submission->law_12846_compliant ? 'status-yes' : 'status-no' }}">
                                {{ $submission->law_12846_compliant ? '( ✓ ) Sim' : '( ✗ ) Não' }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Pergunta 3 -->
                <div class="field">
                    <div class="field-label"><strong>3. Lei 13.709/2018 (LGPD)</strong></div>
                    <div class="field-value">
                        @if($submission->lgpd_compliant === null)
                            <span class="empty">—</span>
                        @else
                            <span class="status-badge {{ $submission->lgpd_compliant ? 'status-yes' : 'status-no' }}">
                                {{ $submission->lgpd_compliant ? '( ✓ ) Sim' : '( ✗ ) Não' }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Pergunta 4 -->
                <div class="field full-width">
                    <div class="field-label"><strong>4. A PJ/PF ou sócios já foi investigado ou condenado?</strong></div>
                    @if(is_array($submission->investigated_for) && count($submission->investigated_for))
                        <div>
                            @foreach ($submission->investigated_for as $item)
                                <div class="list-item">☐ {{ $item }}</div>
                            @endforeach
                        </div>
                    @else
                        <div class="field-value empty">Nenhuma opção marcada</div>
                    @endif
                    @if($submission->conflict_roles_details && in_array('Não', (array)$submission->investigated_for) === false)
                        <div style="margin-top: 8px; padding: 8px; background: #fef2f2; border-left: 3px solid #dc2626;">
                            <strong style="color: #991b1b; font-size: 12px;">Detalhes:</strong>
                            <div style="margin-top: 4px; font-size: 12px; color: #7f1d1d;">{{ $submission->conflict_roles_details }}</div>
                        </div>
                    @endif
                </div>

                <!-- Pergunta 5 -->
                <div class="field full-width">
                    <div class="field-label"><strong>5. A PJ/PF ou sócios enquadra-se em situações de conflito?</strong></div>
                    @if(is_array($submission->conflict_roles) && count($submission->conflict_roles))
                        <div>
                            @foreach ($submission->conflict_roles as $item)
                                <div class="list-item">☐ {{ $item }}</div>
                            @endforeach
                        </div>
                    @else
                        <div class="field-value empty">Nenhuma opção marcada</div>
                    @endif
                    @if($submission->conflict_roles_details_q5)
                        <div style="margin-top: 8px; padding: 8px; background: #fffbeb; border-left: 3px solid #f59e0b;">
                            <strong style="color: #92400e; font-size: 12px;">Detalhes:</strong>
                            <div style="margin-top: 4px; font-size: 12px; color: #78350f;">{{ $submission->conflict_roles_details_q5 }}</div>
                        </div>
                    @endif
                </div>

                <!-- Pergunta 6 -->
                <div class="field full-width">
                    <div class="field-label"><strong>6. Ocupa cargo ou vínculo com órgão público relacionado à Vitória Hospitalar?</strong></div>
                    <div class="field-value">{{ $submission->public_power_relatives === 'sim' ? '( ) Sim' : '( ✓ ) Não' }}</div>
                    @if($submission->public_power_relatives_details)
                        <div style="margin-top: 6px; padding: 6px; background: #eff6ff; border-left: 2px solid #0284c7; font-size: 12px;">
                            <strong style="color: #0c4a6e;">Órgão/Servidor:</strong> {{ $submission->public_power_relatives_details }}
                        </div>
                    @endif
                </div>

                <!-- Pergunta 7 -->
                <div class="field full-width">
                    <div class="field-label"><strong>7. Possui relacionamento pessoal/familiar/comercial com a Vitória Hospitalar?</strong></div>
                    <div class="field-value">{{ $submission->internal_relationships === 'sim' ? '( ) Sim' : '( ✓ ) Não' }}</div>
                    @if($submission->internal_relationships_details)
                        <div style="margin-top: 6px; padding: 6px; background: #eff6ff; border-left: 2px solid #0284c7; font-size: 12px;">
                            <strong style="color: #0c4a6e;">Pessoa/Área/Natureza:</strong> {{ $submission->internal_relationships_details }}
                        </div>
                    @endif
                </div>

                <!-- Pergunta 8 -->
                <div class="field full-width">
                    <div class="field-label"><strong>8. Possuem participação societária na Vitória Hospitalar?</strong></div>
                    <div class="field-value">{{ $submission->employee_shareholding === 'sim' ? '( ) Sim' : '( ✓ ) Não' }}</div>
                    @if($submission->employee_shareholding_details)
                        <div style="margin-top: 6px; padding: 6px; background: #eff6ff; border-left: 2px solid #0284c7; font-size: 12px;">
                            <strong style="color: #0c4a6e;">Detalhes:</strong> {{ $submission->employee_shareholding_details }}
                        </div>
                    @endif
                </div>

                <!-- Pergunta 9 -->
                <div class="field full-width">
                    <div class="field-label"><strong>9. Relacionamento com concorrentes da Vitória Hospitalar?</strong></div>
                    <div class="field-value">{{ $submission->competitor_relationships === 'sim' ? '( ) Sim' : '( ✓ ) Não' }}</div>
                    @if($submission->competitor_relationships_details)
                        <div style="margin-top: 6px; padding: 6px; background: #eff6ff; border-left: 2px solid #0284c7; font-size: 12px;">
                            <strong style="color: #0c4a6e;">Detalhes:</strong> {{ $submission->competitor_relationships_details }}
                        </div>
                    @endif
                </div>

                <!-- Pergunta 10 -->
                <div class="field full-width">
                    <div class="field-label"><strong>10. Existe situação que caracterize conflito de interesses com a Vitória Hospitalar?</strong></div>
                    <div class="field-value">{{ $submission->conflict_situation === 'sim' ? '( ) Sim' : '( ✓ ) Não' }}</div>
                </div>
            </div>
        </div>

        <!-- SEÇÃO 7: ASSINATURA LEGAL -->
        <div class="section">
            <div class="section-header">
                <div class="section-number">{{ $isPj ? '7' : '6' }}</div>
                <div class="section-title">Assinatura Legal</div>
            </div>
            
            <div class="section-content">
                <div class="field full-width">
                    <div class="field-label">Declaração Legal</div>
                    <div class="field-value">
                        @if($submission->legal_declaration === null)
                            <span class="empty">A PJ/PF manifesta sua expressa concordância com a declaração legal? —</span>
                        @else
                            <span class="compliance-answer {{ $submission->legal_declaration === 'concorda' ? 'yes' : 'no' }}">
                                A PJ/PF manifesta sua expressa concordância com a declaração legal?
                                <span class="compliance-answer-status">{{ $submission->legal_declaration === 'concorda' ? '✓ Concorda' : '✗ Discorda' }}</span>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="field full-width">
                    <div class="field-label">Termos e Condições</div>
                    <div class="field-value">
                        <span class="compliance-answer yes">
                            Li e aceito os documentos referentes a este cadastro.
                            <span class="compliance-answer-status">✓ Aceitos</span>
                        </span>

                        @foreach($documents ?? [] as $document)
                            <div class="terms-content" style="margin-top: 14px; border-top: 1px solid #e5e7eb; padding-top: 10px;">
                                <strong>{{ $document['label'] }}</strong>
                                @if(!empty($document['version']))
                                    <span style="color: #6b7280;"> (Versão {{ $document['version'] }})</span>
                                @endif
                                @if(!empty($document['text']))
                                    <div style="margin-top: 6px; white-space: pre-wrap;">{{ $document['text'] }}</div>
                                @endif
                            </div>
                        @endforeach

                        @if($terms)
                            <div class="terms-content" style="margin-top: 14px; border-top: 1px solid #e5e7eb; padding-top: 10px;">{{ $terms }}</div>
                        @endif
                    </div>
                </div>
                <div class="field">
                    <div class="field-label">Responsável Legal</div>
                    <div class="field-value">{{ $submission->legal_representative ?: '—' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">CPF do Responsável</div>
                    <div class="field-value">{{ $submission->legal_representative_cpf ?: '—' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Cargo</div>
                    <div class="field-value">{{ $submission->legal_representative_role ?: '—' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Data da Assinatura</div>
                    <div class="field-value">{{ optional($submission->legal_representative_date)->format('d/m/Y') ?: '—' }}</div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p>Documento gerado em {{ now()->format('d/m/Y H:i:s') }} | Sistema de Qualificação e Cadastro VH</p>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>
</html>
