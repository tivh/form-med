@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 pt-12 pb-10 space-y-6">
    <div class="rounded-3xl bg-gradient-to-r from-red-600 via-red-500 to-rose-500 text-white shadow-xl overflow-hidden">
        <div class="p-8 md:p-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="space-y-2">
                <p class="text-xs uppercase tracking-[0.25em] text-white/70">Admin • Revisão</p>
                <h1 class="text-3xl md:text-4xl font-black">Detalhes da submissão</h1>
                <div class="flex flex-wrap gap-3 text-sm text-white/80">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20">
                        <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>
                        Recebida {{ optional($submission->created_at)->format('d/m/Y H:i') }}
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20">
                        {{ $formCatalog[$submission->form_type]['title'] ?? $submission->form_type ?? 'Formulário' }}
                    </span>
                    @php $isPj = $submission->registration_type === 'pj'; @endphp
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20">
                        {{ $isPj ? 'Pessoa Jurídica' : 'Pessoa Física' }}
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @if(is_array($submission->documents) && count($submission->documents))
                    <a href="{{ route('admin.submissions.download', $submission) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-slate-900 font-semibold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 10l5 5m0 0l5-5m-5 5V3"/></svg>
                        Baixar primeiro arquivo
                    </a>
                @endif
                <form method="POST" action="{{ route('admin.submissions.destroy', $submission) }}" onsubmit="return confirm('Remover essa submissão?');" class="inline-flex">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-100 text-red-700 font-semibold border border-red-200 hover:bg-red-200/80">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12H9m10 0a8 8 0 11-16 0 8 8 0 0116 0z"/></svg>
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

    @if(is_array($submission->documents) && count($submission->documents))
        <div class="glass rounded-2xl p-6 border border-slate-100 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-red-600">Arquivos enviados</p>
                    <p class="text-sm text-slate-600">{{ count($submission->documents) }} arquivo(s) disponível(is) para download</p>
                </div>
            </div>
            <ul class="divide-y divide-slate-200 text-sm text-slate-800">
                @foreach ($submission->documents as $index => $doc)
                    <li class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-700 font-semibold">{{ $index + 1 }}</span>
                            <span>{{ $doc['original_name'] ?? ('Documento '.($index + 1)) }}</span>
                        </div>
                        <a href="{{ route('admin.submissions.download', [$submission, 'doc' => $index]) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-100 hover:bg-red-100">Baixar</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass rounded-2xl p-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-slate-900 shadow-lg">
        <div>
            <p class="text-sm text-slate-500">Formulário</p>
            <p class="text-lg font-semibold">{{ $formCatalog[$submission->form_type]['title'] ?? $submission->form_type ?? '—' }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Tipo de cadastro</p>
            <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-semibold {{ $isPj ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">
                <span class="h-2 w-2 rounded-full {{ $isPj ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                {{ $isPj ? 'Pessoa Jurídica' : 'Pessoa Física' }}
            </p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Nome</p>
            <p class="text-lg font-semibold">{{ $submission->nome }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">CPF</p>
            <p class="text-lg font-semibold">{{ $submission->cpf ?: '—' }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Razão social</p>
            <p class="text-lg font-semibold">{{ $submission->razao_social }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Nome fantasia</p>
            <p class="text-lg font-semibold">{{ $submission->nome_fantasia }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">CNPJ</p>
            <p class="text-lg font-semibold">{{ $submission->cnpj ?: '—' }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Representante legal</p>
            <p class="text-lg font-semibold">{{ $submission->representante_legal }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Website</p>
            <p class="text-lg font-semibold">{{ $submission->website }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">E-mail</p>
            <p class="text-lg font-semibold">{{ $submission->email }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">E-mail testemunha</p>
            <p class="text-lg font-semibold">{{ $submission->email_testemunha }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Telefone</p>
            <p class="text-lg font-semibold">{{ $submission->telefone }}</p>
        </div>
        <div class="md:col-span-2">
            <p class="text-sm text-slate-500">Endereço</p>
            <p class="text-lg font-semibold">{{ $submission->endereco }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Nacionalidade</p>
            <p class="text-lg font-semibold">{{ $submission->nacionalidade }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Profissão</p>
            <p class="text-lg font-semibold">{{ $submission->profissao }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Data de nascimento</p>
            <p class="text-lg font-semibold">{{ optional($submission->data_nascimento)->format('d/m/Y') ?: '—' }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Dados bancários</p>
            <p class="text-lg font-semibold">{{ $submission->dados_bancarios }}</p>
        </div>
        <div class="md:col-span-2">
            <p class="text-sm text-slate-500">Mensagem</p>
            <p class="text-base text-slate-800 whitespace-pre-line">{{ $submission->mensagem }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Enviado em</p>
            <p class="text-lg font-semibold">{{ optional($submission->created_at)->format('d/m/Y H:i') }}</p>
        </div>
        <div class="md:col-span-2 space-y-2">
            <p class="text-sm text-slate-500">Documentos</p>
            @if(is_array($submission->documents) && count($submission->documents))
                <p class="text-sm text-slate-700">Consulte a lista acima para baixar.</p>
            @else
                <p class="text-slate-500 text-sm">Nenhum documento enviado.</p>
            @endif
        </div>

        <div class="md:col-span-2">
            <p class="text-sm text-slate-500">Checklist enviado</p>
            @if(is_array($submission->doc_checklist) && count($submission->doc_checklist))
                <ul class="list-disc list-inside text-sm text-slate-800 space-y-1">
                    @foreach ($submission->doc_checklist as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-slate-500 text-sm">Nenhum item marcado.</p>
            @endif
        </div>

        <div class="md:col-span-2">
            <p class="text-sm text-slate-500">Políticas de compliance</p>
            @if(is_array($submission->compliance_policies) && count($submission->compliance_policies))
                <ul class="list-disc list-inside text-sm text-slate-800 space-y-1">
                    @foreach ($submission->compliance_policies as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-slate-500 text-sm">Não informado.</p>
            @endif
        </div>

        <div>
            <p class="text-sm text-slate-500">Investigado por</p>
            <p class="text-lg font-semibold">{{ $submission->investigated_for ?: '—' }}</p>
        </div>
        <div class="md:col-span-2">
            <p class="text-sm text-slate-500">Detalhes investigação</p>
            <p class="text-base text-slate-800 whitespace-pre-line">{{ $submission->investigation_details }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Lei 12.846/2013</p>
            <p class="text-lg font-semibold">{{ $submission->law_12846_compliant === null ? '' : ($submission->law_12846_compliant ? 'Sim' : 'Não') }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">LGPD</p>
            <p class="text-lg font-semibold">{{ $submission->lgpd_compliant === null ? '' : ($submission->lgpd_compliant ? 'Sim' : 'Não') }}</p>
        </div>

        <div class="md:col-span-2 pt-4 border-t border-slate-100">
            <p class="text-xs uppercase tracking-[0.2em] text-red-600 mb-1">Conflito de interesse</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-slate-900">
                <div>
                    <p class="text-sm text-slate-500">Perfis marcados</p>
                    @if(is_array($submission->conflict_roles) && count($submission->conflict_roles))
                        <ul class="list-disc list-inside text-sm text-slate-800 space-y-1">
                            @foreach ($submission->conflict_roles as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-slate-500 text-sm">Não informado.</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-slate-500">Detalhes perfis</p>
                    <p class="text-base text-slate-800 whitespace-pre-line">{{ $submission->conflict_roles_details }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Parentes em órgão público</p>
                    <p class="text-lg font-semibold">{{ $submission->public_power_relatives ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Detalhes (órgão público)</p>
                    <p class="text-base text-slate-800 whitespace-pre-line">{{ $submission->public_power_relatives_details }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Relacionamento interno</p>
                    <p class="text-lg font-semibold">{{ $submission->internal_relationships ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Detalhes (relacionamento interno)</p>
                    <p class="text-base text-slate-800 whitespace-pre-line">{{ $submission->internal_relationships_details }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Participação de colaborador</p>
                    <p class="text-lg font-semibold">{{ $submission->employee_shareholding ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Detalhes (participação colaborador)</p>
                    <p class="text-base text-slate-800 whitespace-pre-line">{{ $submission->employee_shareholding_details }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Situação de conflito</p>
                    <p class="text-lg font-semibold">{{ $submission->conflict_situation ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Detalhes (situação conflito)</p>
                    <p class="text-base text-slate-800 whitespace-pre-line">{{ $submission->conflict_situation_details }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Relacionamento com concorrente</p>
                    <p class="text-lg font-semibold">{{ $submission->competitor_relationships ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Detalhes (concorrente)</p>
                    <p class="text-base text-slate-800 whitespace-pre-line">{{ $submission->competitor_relationships_details }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Participação na contratante</p>
                    <p class="text-lg font-semibold">{{ $submission->contractor_shareholding ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Detalhes (participação contratante)</p>
                    <p class="text-base text-slate-800 whitespace-pre-line">{{ $submission->contractor_shareholding_details }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Laços de amizade/parentesco</p>
                    <p class="text-lg font-semibold">{{ $submission->friendship_ties ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Detalhes (laços)</p>
                    <p class="text-base text-slate-800 whitespace-pre-line">{{ $submission->friendship_ties_details }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Declaração legal</p>
                    <p class="text-lg font-semibold">{{ $submission->legal_declaration ?: '—' }}</p>
                </div>
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-slate-500">Responsável legal</p>
                        <p class="text-lg font-semibold">{{ $submission->legal_representative }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">CPF responsável</p>
                        <p class="text-lg font-semibold">{{ $submission->legal_representative_cpf }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Cargo</p>
                        <p class="text-lg font-semibold">{{ $submission->legal_representative_role }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Data</p>
                        <p class="text-lg font-semibold">{{ optional($submission->legal_representative_date)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
