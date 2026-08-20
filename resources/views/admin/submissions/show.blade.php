@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 pt-12 pb-10">
    @php
        $isPj = $submission->registration_type === 'pj';
        $classificationLabel = match ($submission->classification ?? null) {
            'pj-rh' => 'PJ RH',
            'pj' => 'PJ',
            'pf' => 'PF',
            default => ($isPj ? 'PJ' : 'PF'),
        };
    @endphp
    
    <!-- Header -->
    <div class="rounded-3xl bg-gradient-to-r from-red-600 via-red-500 to-rose-500 text-white shadow-xl overflow-hidden mb-8">
        <div class="p-8 md:p-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.25em] text-white/70">Admin • Revisão</p>
                <h1 class="text-3xl md:text-4xl font-black">Resposta do {{ $isPj ? 'Cadastro da Empresa' : 'Cadastro do Fornecedor' }}</h1>
                <div class="flex flex-wrap gap-3 text-sm text-white/80">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20">
                        <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>
                        Recebida {{ optional($submission->created_at)->format('d/m/Y H:i') }}
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20">
                        {{ $isPj ? 'Pessoa Jurídica' : 'Pessoa Física' }}
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-200 font-semibold">
                        Classificação: {{ $classificationLabel }}
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.submissions.print', $submission) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-slate-900 font-semibold shadow-lg hover:shadow-xl transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h2m2 4H9a2 2 0 00-2 2v2a2 2 0 002 2h4a2 2 0 002-2v-2a2 2 0 00-2-2z"/></svg>
                    Imprimir/PDF
                </a>
                <form method="POST" action="{{ route('admin.submissions.destroy', $submission) }}" onsubmit="return confirm('Remover essa submissão?');" class="inline-flex">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-100 text-red-700 font-semibold border border-red-200 hover:bg-red-200/80">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Excluir
                    </button>
                </form>
                <a href="{{ route('admin.submissions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 text-white font-semibold border border-white/20 hover:bg-white/20">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Status e Verificação -->
    <div class="glass rounded-2xl p-6 mb-8 border border-white/60 shadow-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Tipo de Cadastro</p>
                <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-semibold {{ $isPj ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">
                    <span class="h-2 w-2 rounded-full {{ $isPj ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                    {{ $isPj ? 'Pessoa Jurídica' : 'Pessoa Física' }}
                </p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Verificado</p>
                <form method="POST" action="{{ route('admin.submissions.toggle-verified', $submission) }}" class="inline">
                    @csrf
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                        <input type="checkbox" name="verified" value="1" {{ $submission->verified ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500 cursor-pointer" onchange="this.form.submit()">
                        <span class="text-xs font-semibold">{{ $submission->verified ? '✓ Verificado' : 'Pendente' }}</span>
                    </label>
                </form>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Data de Envio</p>
                <p class="text-sm font-semibold text-slate-900">{{ optional($submission->created_at)->format('d/m/Y') }}</p>
                <p class="text-xs text-slate-600">{{ optional($submission->created_at)->format('H:i') }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">ID do Registro</p>
                <p class="text-sm font-semibold text-slate-900">#{{ $submission->id }}</p>
            </div>
        </div>
    </div>

    <!-- Documentos Obrigatórios -->
    @if(is_array($submission->required_documents) && count($submission->required_documents))
        @php
            $requiredDocumentLabels = [
                'personal_documents' => 'Documentos pessoais (CNH ou RG)',
                'corporate_document' => 'Contrato social ou Estatuto social',
                'legal_representative_document' => 'Documento do representante legal (CNH ou RG)',
            ];
        @endphp
        <div class="glass rounded-2xl p-6 mb-8 border border-red-100 shadow-lg">
            <div class="mb-4">
                <p class="text-xs uppercase tracking-[0.2em] text-red-600 font-semibold">Documentos Obrigatórios</p>
                <p class="text-sm text-slate-600 mt-1">{{ count($submission->required_documents) }} arquivo(s)</p>
            </div>
            <div class="space-y-2">
                @foreach ($submission->required_documents as $key => $doc)
                    <div class="flex items-center justify-between p-3 rounded-lg border border-red-100 bg-red-50/50 hover:bg-red-50 transition">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-red-700 font-semibold text-sm">!</span>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $requiredDocumentLabels[$key] ?? 'Documento obrigatório' }}</p>
                                <p class="text-xs text-slate-600">{{ $doc['original_name'] ?? 'Arquivo enviado' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.submissions.download', [$submission, 'required_doc' => $key]) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 text-xs font-medium">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Documentos Anexados -->
    @if(is_array($submission->documents) && count($submission->documents))
        <div class="glass rounded-2xl p-6 mb-8 border border-white/60 shadow-lg">
            <div class="mb-4">
                <p class="text-xs uppercase tracking-[0.2em] text-red-600 font-semibold">Documentos Enviados</p>
                <p class="text-sm text-slate-600 mt-1">{{ count($submission->documents) }} arquivo(s)</p>
            </div>
            <div class="space-y-2">
                @foreach ($submission->documents as $index => $doc)
                    <div class="flex items-center justify-between p-3 rounded-lg border border-slate-200 bg-white/50 hover:bg-white transition">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-700 font-semibold text-sm">{{ $index + 1 }}</span>
                            <span class="text-sm text-slate-800">{{ $doc['original_name'] ?? 'Documento '.($index + 1) }}</span>
                        </div>
                        <a href="{{ route('admin.submissions.download', [$submission, 'doc' => $index]) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 text-xs font-medium">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- SEÇÃO 1: Identificação -->
    <div class="space-y-6 mb-8">
        <div class="rounded-2xl border-2 border-red-200 bg-gradient-to-r from-red-50 to-rose-50 p-5">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">1</span>
                <h2 class="text-lg font-bold text-slate-900">{{ $isPj ? 'Identificação da Empresa' : 'Identificação Pessoal' }}</h2>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 border border-white/60 shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($isPj)
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Razão Social</p>
                        <p class="text-base font-semibold text-slate-900">{{ $submission->razao_social ?: '—' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Nome Fantasia</p>
                        <p class="text-base font-semibold text-slate-900">{{ $submission->nome_fantasia ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">CNPJ</p>
                        <p class="text-base font-semibold text-slate-900">{{ $submission->cnpj ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Website</p>
                        <p class="text-base font-semibold text-slate-900">{{ $submission->website ?: '—' }}</p>
                    </div>
                @else
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Nome Completo</p>
                        <p class="text-base font-semibold text-slate-900">{{ $submission->nome ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">CPF</p>
                        <p class="text-base font-semibold text-slate-900">{{ $submission->cpf ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Data de Nascimento</p>
                        <p class="text-base font-semibold text-slate-900">{{ optional($submission->data_nascimento)->format('d/m/Y') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Nacionalidade</p>
                        <p class="text-base font-semibold text-slate-900">{{ $submission->nacionalidade ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Profissão</p>
                        <p class="text-base font-semibold text-slate-900">{{ $submission->profissao ?: '—' }}</p>
                    </div>
                @endif
                
                <div class="md:col-span-2">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Endereço</p>
                    <p class="text-base font-semibold text-slate-900">{{ $submission->endereco ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">E-mail</p>
                    <p class="text-base font-semibold text-slate-900">{{ $submission->email ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Telefone</p>
                    <p class="text-base font-semibold text-slate-900">{{ $submission->telefone ?: '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SEÇÃO 2: Representantes (apenas PJ) -->
    @if($isPj)
    <div class="space-y-6 mb-8">
        <div class="rounded-2xl border-2 border-red-200 bg-gradient-to-r from-red-50 to-rose-50 p-5">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">2</span>
                <h2 class="text-lg font-bold text-slate-900">Representantes Legais</h2>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 border border-white/60 shadow-lg">
            <div class="space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 mb-4 pb-3 border-b border-slate-200">Representante Legal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Nome</p>
                            <p class="text-base font-semibold text-slate-900">{{ $submission->representante_legal_nome ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">E-mail</p>
                            <p class="text-base font-semibold text-slate-900">{{ $submission->representante_legal_email ?: '—' }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 pb-3 border-b border-slate-200">Responsável Jurídico</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Nome</p>
                            <p class="text-base font-semibold text-slate-900">{{ $submission->responsavel_juridico_nome ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">E-mail</p>
                            <p class="text-base font-semibold text-slate-900">{{ $submission->responsavel_juridico_email ?: '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- SEÇÃO 3: Testemunha e Dados Financeiros -->
    <div class="space-y-6 mb-8">
        <div class="rounded-2xl border-2 border-red-200 bg-gradient-to-r from-red-50 to-rose-50 p-5">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">{{ $isPj ? '3' : '2' }}</span>
                <h2 class="text-lg font-bold text-slate-900">Testemunha e Dados Financeiros</h2>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 border border-white/60 shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Nome da Testemunha</p>
                    <p class="text-base font-semibold text-slate-900">{{ $submission->testemunha_nome ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">E-mail da Testemunha</p>
                    <p class="text-base font-semibold text-slate-900">{{ $submission->testemunha_email ?: '—' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Dados Bancários</p>
                    <p class="text-base font-semibold text-slate-900 whitespace-pre-line">{{ $submission->dados_bancarios ?: '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SEÇÃO 4: Compliance e Conformidades -->
    <div class="space-y-6 mb-8">
        <div class="rounded-2xl border-2 border-blue-200 bg-gradient-to-r from-blue-50 to-cyan-50 p-5">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-bold">{{ $isPj ? '4' : '3' }}</span>
                <h2 class="text-lg font-bold text-slate-900">Compliance e Conformidades</h2>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 border border-white/60 shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Lei 12.846/2013</p>
                    <p class="text-base font-semibold {{ $submission->law_12846_compliant ? 'text-emerald-700' : 'text-slate-900' }}">
                        {{ $submission->law_12846_compliant === null ? '—' : ($submission->law_12846_compliant ? '✓ Sim' : '✗ Não') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">LGPD</p>
                    <p class="text-base font-semibold {{ $submission->lgpd_compliant ? 'text-emerald-700' : 'text-slate-900' }}">
                        {{ $submission->lgpd_compliant === null ? '—' : ($submission->lgpd_compliant ? '✓ Sim' : '✗ Não') }}
                    </p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Investigação Anterior</p>
                    <p class="text-base font-semibold text-slate-900">{{ $submission->investigated_for ?: '—' }}</p>
                    @if($submission->investigation_details)
                        <p class="text-sm text-slate-700 mt-2 whitespace-pre-line">{{ $submission->investigation_details }}</p>
                    @endif
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Checklist de Documentos</p>
                    @if(is_array($submission->doc_checklist) && count($submission->doc_checklist))
                        <ul class="space-y-1">
                            @foreach ($submission->doc_checklist as $item)
                                <li class="text-sm text-slate-800">✓ {{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-slate-500">Nenhum item marcado.</p>
                    @endif
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Políticas de Compliance</p>
                    @if(is_array($submission->compliance_policies) && count($submission->compliance_policies))
                        <ul class="space-y-1">
                            @foreach ($submission->compliance_policies as $item)
                                <li class="text-sm text-slate-800">✓ {{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-slate-500">Nenhuma política registrada.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- SEÇÃO 5: Conflito de Interesses -->
    <div class="space-y-6 mb-8">
        <div class="rounded-2xl border-2 border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 p-5">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-amber-600 text-white text-sm font-bold">{{ $isPj ? '5' : '4' }}</span>
                <h2 class="text-lg font-bold text-slate-900">Conflito de Interesses</h2>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 border border-white/60 shadow-lg space-y-8">
            <!-- Pergunta 1 - Compliance Policies -->
            <div class="border-b border-slate-200 pb-6 last:border-b-0">
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2"><strong>1.</strong> A PJ/PF possui algum dos seguintes documentos ou programas?</p>
                @if(is_array($submission->compliance_policies) && count($submission->compliance_policies))
                    <ul class="space-y-1 mt-3">
                        @foreach ($submission->compliance_policies as $item)
                            <li class="text-sm text-slate-800">☐ {{ $item }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-500 mt-2">Nenhuma política marcada.</p>
                @endif
            </div>

            <!-- Pergunta 2 - Lei 12.846/2013 -->
            <div class="border-b border-slate-200 pb-6 last:border-b-0">
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2"><strong>2.</strong> A PJ/PF declara estar em conformidade com a Lei nº 12.846/2013 (Lei Anticorrupção)?</p>
                <p class="text-sm font-semibold text-slate-900 mt-2">
                    @if($submission->law_12846_compliant === null)
                        <span class="text-slate-500">—</span>
                    @else
                        <span class="{{ $submission->law_12846_compliant ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ $submission->law_12846_compliant ? '( ✓ ) Sim' : '( ✗ ) Não' }}
                        </span>
                    @endif
                </p>
            </div>

            <!-- Pergunta 3 - LGPD -->
            <div class="border-b border-slate-200 pb-6 last:border-b-0">
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2"><strong>3.</strong> A PJ/PF declara estar em conformidade com a Lei nº 13.709/2018 (Lei Geral de Proteção de Dados – LGPD)?</p>
                <p class="text-sm font-semibold text-slate-900 mt-2">
                    @if($submission->lgpd_compliant === null)
                        <span class="text-slate-500">—</span>
                    @else
                        <span class="{{ $submission->lgpd_compliant ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ $submission->lgpd_compliant ? '( ✓ ) Sim' : '( ✗ ) Não' }}
                        </span>
                    @endif
                </p>
            </div>

            <!-- Pergunta 4 - Investigação -->
            <div class="border-b border-slate-200 pb-6 last:border-b-0">
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2"><strong>4.</strong> A PJ/PF ou, no caso de pessoa jurídica, algum de seus sócios, administradores ou representantes, já foi investigado ou condenado por algum dos fatos abaixo?</p>
                @if(is_array($submission->investigated_for) && count($submission->investigated_for))
                    <ul class="space-y-1 mt-3">
                        @foreach ($submission->investigated_for as $item)
                            <li class="text-sm text-slate-800">☐ {{ $item }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-500 mt-2">Nenhuma opção marcada.</p>
                @endif
                @if($submission->conflict_roles_details && in_array('Não', (array)$submission->investigated_for) === false)
                    <div class="mt-3 p-3 bg-red-50 rounded-lg border border-red-200">
                        <p class="text-xs font-semibold text-red-700 mb-1">Detalhes:</p>
                        <p class="text-sm text-red-600 whitespace-pre-line">{{ $submission->conflict_roles_details }}</p>
                    </div>
                @endif
            </div>

            <!-- Pergunta 5 - Conflict Roles -->
            <div class="border-b border-slate-200 pb-6 last:border-b-0">
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2"><strong>5.</strong> A PJ/PF ou, no caso de pessoa jurídica, algum de seus sócios, administradores ou representantes, enquadra-se em alguma das situações abaixo?</p>
                @if(is_array($submission->conflict_roles) && count($submission->conflict_roles))
                    <ul class="space-y-1 mt-3">
                        @foreach ($submission->conflict_roles as $item)
                            <li class="text-sm text-slate-800">☐ {{ $item }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-500 mt-2">Nenhuma opção marcada.</p>
                @endif
                @if($submission->conflict_roles_details_q5)
                    <div class="mt-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                        <p class="text-xs font-semibold text-amber-700 mb-1">Detalhes:</p>
                        <p class="text-sm text-amber-800 whitespace-pre-line">{{ $submission->conflict_roles_details_q5 }}</p>
                    </div>
                @endif
            </div>

            <!-- Pergunta 6 - Public Power Relatives -->
            <div class="border-b border-slate-200 pb-6 last:border-b-0">
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2"><strong>6.</strong> A PJ/PF ou, no caso de pessoa jurídica, algum de seus sócios, administradores ou representantes, ocupa cargo ou mantém vínculo familiar com pessoa em posição de decisão em órgão ou entidade pública que possua relação com a Vitória Hospitalar?</p>
                <p class="text-sm font-semibold text-slate-900 mt-2">
                    <span class="{{ $submission->public_power_relatives === 'sim' ? 'text-red-700' : 'text-emerald-700' }}">
                        {{ $submission->public_power_relatives === 'sim' ? '( ) Sim' : '( ✓ ) Não' }}
                    </span>
                </p>
                @if($submission->public_power_relatives_details)
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-xs font-semibold text-blue-700 mb-1">Órgão, cargo e/ou nome do servidor:</p>
                        <p class="text-sm text-blue-600 whitespace-pre-line">{{ $submission->public_power_relatives_details }}</p>
                    </div>
                @endif
            </div>

            <!-- Pergunta 7 - Internal Relationships -->
            <div class="border-b border-slate-200 pb-6 last:border-b-0">
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2"><strong>7.</strong> A PJ/PF possui relacionamento pessoal, familiar ou comercial com sócios, administradores ou colaboradores da Vitória Hospitalar?</p>
                <p class="text-sm font-semibold text-slate-900 mt-2">
                    <span class="{{ $submission->internal_relationships === 'sim' ? 'text-red-700' : 'text-emerald-700' }}">
                        {{ $submission->internal_relationships === 'sim' ? '( ) Sim' : '( ✓ ) Não' }}
                    </span>
                </p>
                @if($submission->internal_relationships_details)
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-xs font-semibold text-blue-700 mb-1">Pessoa, área e natureza do relacionamento:</p>
                        <p class="text-sm text-blue-600 whitespace-pre-line">{{ $submission->internal_relationships_details }}</p>
                    </div>
                @endif
            </div>

            <!-- Pergunta 8 - Employee Shareholding -->
            <div class="border-b border-slate-200 pb-6 last:border-b-0">
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2"><strong>8.</strong> A PJ/PF ou, no caso de pessoa jurídica, seus sócios ou administradores, possuem participação societária, direta ou indireta, na Vitória Hospitalar?</p>
                <p class="text-sm font-semibold text-slate-900 mt-2">
                    <span class="{{ $submission->employee_shareholding === 'sim' ? 'text-red-700' : 'text-emerald-700' }}">
                        {{ $submission->employee_shareholding === 'sim' ? '( ) Sim' : '( ✓ ) Não' }}
                    </span>
                </p>
                @if($submission->employee_shareholding_details)
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-xs font-semibold text-blue-700 mb-1">Detalhes:</p>
                        <p class="text-sm text-blue-600 whitespace-pre-line">{{ $submission->employee_shareholding_details }}</p>
                    </div>
                @endif
            </div>

            <!-- Pergunta 9 - Competitor Relationships -->
            <div class="border-b border-slate-200 pb-6 last:border-b-0">
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2"><strong>9.</strong> A PJ/PF possui relacionamento pessoal, familiar ou comercial com sócios, administradores ou colaboradores de empresas concorrentes da Vitória Hospitalar?</p>
                <p class="text-sm font-semibold text-slate-900 mt-2">
                    <span class="{{ $submission->competitor_relationships === 'sim' ? 'text-red-700' : 'text-emerald-700' }}">
                        {{ $submission->competitor_relationships === 'sim' ? '( ) Sim' : '( ✓ ) Não' }}
                    </span>
                </p>
                @if($submission->competitor_relationships_details)
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-xs font-semibold text-blue-700 mb-1">Detalhes:</p>
                        <p class="text-sm text-blue-600 whitespace-pre-line">{{ $submission->competitor_relationships_details }}</p>
                    </div>
                @endif
            </div>

            <!-- Pergunta 10 - Conflict Situation -->
            <div class="pb-6">
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2"><strong>10.</strong> Existe qualquer situação envolvendo a PJ/PF que possa caracterizar conflito de interesses real, potencial ou aparente em relação à Vitória Hospitalar?</p>
                <p class="text-sm font-semibold text-slate-900 mt-2">
                    <span class="{{ $submission->conflict_situation === 'sim' ? 'text-red-700' : 'text-emerald-700' }}">
                        {{ $submission->conflict_situation === 'sim' ? '( ) Sim' : '( ✓ ) Não' }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- SEÇÃO 6: Assinatura Legal -->
    <div class="space-y-6 mb-8">
        <div class="rounded-2xl border-2 border-purple-200 bg-gradient-to-r from-purple-50 to-pink-50 p-5">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-purple-600 text-white text-sm font-bold">{{ $isPj ? '6' : '5' }}</span>
                <h2 class="text-lg font-bold text-slate-900">Assinatura Legal</h2>
            </div>
        </div>

        <div class="glass rounded-2xl p-6 border border-white/60 shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Declaração Legal</p>
                    <p class="text-base font-semibold text-slate-900">{{ $submission->legal_declaration === 'concorda' ? '✓ Concorda' : '✗ Discorda' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Responsável Legal</p>
                    <p class="text-base font-semibold text-slate-900">{{ $submission->legal_representative ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">CPF do Responsável</p>
                    <p class="text-base font-semibold text-slate-900">{{ $submission->legal_representative_cpf ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Cargo</p>
                    <p class="text-base font-semibold text-slate-900">{{ $submission->legal_representative_role ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Data da Assinatura</p>
                    <p class="text-base font-semibold text-slate-900">{{ optional($submission->legal_representative_date)->format('d/m/Y') ?: '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
