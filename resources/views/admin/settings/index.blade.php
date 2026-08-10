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

        <div class="glass rounded-2xl p-6 border border-white/60 shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 text-sm font-bold">PF</span>
                <h2 class="text-base font-bold text-slate-900">Termos para Pessoa Física</h2>
            </div>
            <p class="text-sm text-slate-600">Este texto será exibido no formulário quando o usuário selecionar <strong>Pessoa Física</strong>.</p>
            <textarea
                name="terms_pf"
                id="terms_pf"
                rows="10"
                class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 font-mono"
                placeholder="Digite aqui os termos e condições para Pessoa Física..."
            >{{ old('terms_pf', $terms_pf) }}</textarea>
            @error('terms_pf')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="glass rounded-2xl p-6 border border-white/60 shadow-lg space-y-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-red-700 text-sm font-bold">PJ</span>
                <h2 class="text-base font-bold text-slate-900">Termos para Pessoa Jurídica</h2>
            </div>
            <p class="text-sm text-slate-600">Este texto será exibido no formulário quando o usuário selecionar <strong>Pessoa Jurídica</strong>.</p>
            <textarea
                name="terms_pj"
                id="terms_pj"
                rows="10"
                class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 font-mono"
                placeholder="Digite aqui os termos e condições para Pessoa Jurídica..."
            >{{ old('terms_pj', $terms_pj) }}</textarea>
            @error('terms_pj')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
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
