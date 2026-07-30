{{-- resources/views/tax-regime-form.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 pt-10">
    <div class="glass rounded-3xl p-10 shadow-xl border border-white/70">
        <div class="space-y-3 mb-8">
            <div class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 border border-red-100">Envio seguro</div>
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900">Identificação do Regime Tributário</h1>
            <p class="text-slate-600">
                Este formulário tem como objetivo identificar o regime tributário da sua empresa para subsidiar
                nossas análises fiscais e comerciais. As informações devem ser preenchidas com base na situação
                atual do CNPJ.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                <p class="font-semibold mb-2">Revise os campos abaixo:</p>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tax-regime.submit') }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="razao_social" class="block text-sm font-semibold text-slate-900">1. Razão Social</label>
                <input type="text" name="razao_social" id="razao_social" value="{{ old('razao_social') }}" required
                    class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-sm focus:border-red-500 focus:ring-red-500" />
            </div>

            <div class="space-y-2">
                <label for="cnpj" class="block text-sm font-semibold text-slate-900">2. CNPJ</label>
                <input type="text" name="cnpj" id="cnpj" value="{{ old('cnpj') }}" required placeholder="00.000.000/0000-00"
                    class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-sm focus:border-red-500 focus:ring-red-500" />
            </div>

            <div class="space-y-2">
                <label for="regime_tributario" class="block text-sm font-semibold text-slate-900">3. Qual é o regime tributário atual da empresa?</label>
                <select name="regime_tributario" id="regime_tributario" required
                    class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Selecione...</option>
                    @foreach (['Simples Nacional','Lucro Presumido','Lucro Real'] as $opt)
                        <option value="{{ $opt }}" {{ old('regime_tributario') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-slate-900">4. A sua empresa está adequada à Lei Complementar nº 214/2025, que regulamenta as regras gerais do IBS e da CBS?</label>
                <p class="text-xs text-slate-500">Em caso de dúvida, consulte o contador responsável pela empresa.</p>
                <div class="flex items-center gap-6 mt-2">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="lc_214_2025_compliant" value="1" required class="text-red-600 border-slate-300">
                        <span>Sim</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="lc_214_2025_compliant" value="0" required class="text-red-600 border-slate-300">
                        <span>Não</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="inline-flex items-center px-6 py-3 rounded-xl bg-red-600 text-white font-semibold shadow-lg hover:bg-red-700 transition">
                    Enviar formulário
                </button>
            </div>
        </form>
    </div>
</div>
@endsection