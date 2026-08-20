@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 pt-12 pb-10">
    <div class="rounded-3xl bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 text-white shadow-xl overflow-hidden mb-8">
        <div class="p-8 md:p-10">
            <p class="text-xs uppercase tracking-[0.25em] text-white/60 mb-3">Admin • Configurações</p>
            <h1 class="text-3xl font-black">Termos e Condições</h1>
            <p class="text-white/70 mt-2 text-sm">Configure o texto dos termos exibidos no formulário público para cada tipo de cadastro.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 flex items-center gap-3">
            <svg class="h-5 w-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L9 9.586 7.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        @php
            $documentGroups = [
                [
                    'key' => 'code_of_conduct',
                    'label' => 'Código de conduta',
                    'version_pf' => old('code_of_conduct_version_pf', $code_of_conduct_version_pf ?? 'v1.0'),
                    'version_pj' => old('code_of_conduct_version_pj', $code_of_conduct_version_pj ?? 'v1.0'),
                    'text_pf' => old('code_of_conduct_pf', $code_of_conduct_pf ?? ''),
                    'text_pj' => old('code_of_conduct_pj', $code_of_conduct_pj ?? ''),
                    'updated_pf' => $code_of_conduct_updated_pf ?? null,
                    'updated_pj' => $code_of_conduct_updated_pj ?? null,
                ],
                [
                    'key' => 'integrity_policy',
                    'label' => 'Política de integridade',
                    'version_pf' => old('integrity_policy_version_pf', $integrity_policy_version_pf ?? 'v1.0'),
                    'version_pj' => old('integrity_policy_version_pj', $integrity_policy_version_pj ?? 'v1.0'),
                    'text_pf' => old('integrity_policy_pf', $integrity_policy_pf ?? ''),
                    'text_pj' => old('integrity_policy_pj', $integrity_policy_pj ?? ''),
                    'updated_pf' => $integrity_policy_updated_pf ?? null,
                    'updated_pj' => $integrity_policy_updated_pj ?? null,
                ],
                [
                    'key' => 'data_protection',
                    'label' => 'Termo de proteção de dados pessoais - LGPD',
                    'version_pf' => old('data_protection_version_pf', $data_protection_version_pf ?? 'v1.0'),
                    'version_pj' => old('data_protection_version_pj', $data_protection_version_pj ?? 'v1.0'),
                    'text_pf' => old('data_protection_pf', $data_protection_pf ?? ''),
                    'text_pj' => old('data_protection_pj', $data_protection_pj ?? ''),
                    'updated_pf' => $data_protection_updated_pf ?? null,
                    'updated_pj' => $data_protection_updated_pj ?? null,
                ],
            ];
        @endphp

        <div class="glass rounded-2xl p-6 border border-white/60 shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 text-sm font-bold">PF</span>
                <h2 class="text-base font-bold text-slate-900">Documentos para Pessoa Física</h2>
            </div>
            <p class="text-sm text-slate-600">Configure cada documento e a versão exibida no formulário quando o usuário selecionar <strong>Pessoa Física</strong>.</p>

            @foreach($documentGroups as $doc)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-sm font-bold text-slate-900">{{ $doc['label'] }}</h3>
                        <div class="flex items-center gap-2">
                            <label class="flex items-center gap-2 text-xs font-medium text-slate-700">
                                <span>Versão</span>
                                <input type="text" name="{{ $doc['key'] }}_version_pf" value="{{ $doc['version_pf'] }}" class="w-24 rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-900 focus:border-red-500 focus:ring-red-500" placeholder="v1.0" />
                            </label>
                        </div>
                    </div>
                    <div class="text-[11px] text-slate-500">
                        Última atualização: {{ !empty($doc['updated_pf']) ? \Carbon\Carbon::parse($doc['updated_pf'])->format('d/m/Y H:i') : '—' }}
                    </div>
                    <textarea
                        name="{{ $doc['key'] }}_pf"
                        rows="6"
                        class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 font-mono"
                        placeholder="Digite o texto do documento para pessoa física..."
                    >{{ $doc['text_pf'] }}</textarea>
                </div>
            @endforeach
        </div>

        <div class="glass rounded-2xl p-6 border border-white/60 shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-red-700 text-sm font-bold">PJ</span>
                <h2 class="text-base font-bold text-slate-900">Documentos para Pessoa Jurídica</h2>
            </div>
            <p class="text-sm text-slate-600">Configure cada documento e a versão exibida no formulário quando o usuário selecionar <strong>Pessoa Jurídica</strong>.</p>

            @foreach($documentGroups as $doc)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-sm font-bold text-slate-900">{{ $doc['label'] }}</h3>
                        <div class="flex items-center gap-2">
                            <label class="flex items-center gap-2 text-xs font-medium text-slate-700">
                                <span>Versão</span>
                                <input type="text" name="{{ $doc['key'] }}_version_pj" value="{{ $doc['version_pj'] }}" class="w-24 rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-slate-900 focus:border-red-500 focus:ring-red-500" placeholder="v1.0" />
                            </label>
                        </div>
                    </div>
                    <div class="text-[11px] text-slate-500">
                        Última atualização: {{ !empty($doc['updated_pj']) ? \Carbon\Carbon::parse($doc['updated_pj'])->format('d/m/Y H:i') : '—' }}
                    </div>
                    <textarea
                        name="{{ $doc['key'] }}_pj"
                        rows="6"
                        class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 font-mono"
                        placeholder="Digite o texto do documento para pessoa jurídica..."
                    >{{ $doc['text_pj'] }}</textarea>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-red-600 text-white font-semibold shadow-lg hover:bg-red-700 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Salvar configurações
            </button>
        </div>
    </form>
</div>
@endsection
