@extends('layouts.app')

@section('content')
@php
    $customForm = \App\Models\CustomForm::where('slug', 'regime-tributario')->with(['steps.fields'])->first();
    $formTitle = $customForm?->title ?? 'Identificação do Regime Tributário';
    $formDescription = $customForm?->description ?? 'Este formulário tem como objetivo identificar o regime tributário da sua empresa para subsidiar nossas análises fiscais e comerciais. As informações devem ser preenchidas com base na situação atual do CNPJ.';

    // Busca opções dinâmicas configuradas no Filament para regime_tributario
    $regimeField = null;
    $allFields = collect();
    if ($customForm) {
        foreach ($customForm->steps as $step) {
            foreach ($step->fields as $field) {
                $allFields->push($field);
                if ($field->name === 'regime_tributario') {
                    $regimeField = $field;
                }
            }
        }
    }

    $regimeOptions = [];
    if ($regimeField && !empty($regimeField->options)) {
        foreach ($regimeField->options as $key => $val) {
            if (is_array($val)) {
                $label = $val['label'] ?? ($val['value'] ?? $key);
                $regimeOptions[$label] = $label;
            } else {
                $regimeOptions[$val] = $val;
            }
        }
    }

    if (empty($regimeOptions)) {
        $regimeOptions = [
            'Simples Nacional' => 'Simples Nacional',
            'Lucro Presumido' => 'Lucro Presumido',
            'Lucro Real' => 'Lucro Real',
            'MEI' => 'MEI',
        ];
    }
@endphp

<div class="max-w-3xl mx-auto px-4 sm:px-6 pt-10 pb-12">
    <div class="rounded-3xl p-8 sm:p-10 shadow-xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors">
        <div class="space-y-3 mb-8">
            <div class="inline-flex items-center gap-2 rounded-full bg-red-50 dark:bg-red-950/60 px-3 py-1 text-xs font-semibold text-red-700 dark:text-red-400 border border-red-100 dark:border-red-900/50">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                Setor Fiscal & Financeiro
            </div>
            <h1 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white">{{ $formTitle }}</h1>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed">
                {{ $formDescription }}
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-950/40 p-4 text-red-800 dark:text-red-300">
                <p class="font-semibold mb-2 text-sm">Revise os campos abaixo:</p>
                <ul class="list-disc list-inside space-y-1 text-xs sm:text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tax-regime.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="razao_social" class="block text-sm font-semibold text-slate-900 dark:text-slate-200">1. Razão Social</label>
                <input type="text" name="razao_social" id="razao_social" value="{{ old('razao_social') }}" required
                    class="block w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition" />
            </div>

            <div class="space-y-2">
                <label for="cnpj" class="block text-sm font-semibold text-slate-900 dark:text-slate-200">2. CNPJ</label>
                <input type="text" name="cnpj" id="cnpj" value="{{ old('cnpj') }}" required placeholder="00.000.000/0000-00"
                    class="block w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition" />
            </div>

            <div class="space-y-2">
                <label for="regime_tributario" class="block text-sm font-semibold text-slate-900 dark:text-slate-200">
                    3. {{ $regimeField?->label ?? 'Qual é o regime tributário atual da empresa?' }}
                </label>
                <select name="regime_tributario" id="regime_tributario" required
                    class="block w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 transition">
                    <option value="">Selecione...</option>
                    @foreach ($regimeOptions as $optValue => $optLabel)
                        <option value="{{ $optLabel }}" {{ old('regime_tributario') === $optLabel ? 'selected' : '' }}>
                            {{ $optLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-900 dark:text-slate-200">4. A sua empresa está adequada à Lei Complementar nº 214/2025 (IBS e CBS)?</label>
                <p class="text-xs text-slate-500 dark:text-slate-400">Em caso de dúvida, consulte o contador responsável pela empresa.</p>
                <div class="flex items-center gap-6 mt-3">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-slate-800 dark:text-slate-200 text-sm">
                        <input type="radio" name="lc_214_2025_compliant" value="1" {{ old('lc_214_2025_compliant', '1') == '1' ? 'checked' : '' }} required class="text-red-600 focus:ring-red-500 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                        <span>Sim</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer text-slate-800 dark:text-slate-200 text-sm">
                        <input type="radio" name="lc_214_2025_compliant" value="0" {{ old('lc_214_2025_compliant') === '0' ? 'checked' : '' }} required class="text-red-600 focus:ring-red-500 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                        <span>Não</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-bold shadow-md hover:shadow-lg transition cursor-pointer">
                    <span>Enviar formulário</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection