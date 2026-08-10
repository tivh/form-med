{{-- resources/views/admin/tax-regime/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 pt-12 pb-10">
    <div class="rounded-3xl bg-gradient-to-r from-red-500 to-rose-500 text-white shadow-xl overflow-hidden mb-8 p-8">
        <p class="text-xs uppercase tracking-[0.25em] text-white/70">Admin • Financeiro</p>
        <h1 class="text-3xl md:text-4xl font-black">Regime Tributário — Fornecedores</h1>
        <p class="text-white/80">{{ $submissions->total() }} respostas recebidas.</p>
    </div>

    @if(session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">{{ session('status') }}</div>
    @endif

    <form method="GET" class="glass rounded-2xl p-6 mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 shadow-lg">
        <input type="text" name="razao_social" value="{{ request('razao_social') }}" placeholder="Razão social"
            class="rounded-xl border border-slate-200 bg-white text-slate-900" />
        <input type="text" name="cnpj" value="{{ request('cnpj') }}" placeholder="CNPJ"
            class="rounded-xl border border-slate-200 bg-white text-slate-900" />
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white font-semibold">Filtrar</button>
            <a href="{{ route('admin.tax-regime.export', array_merge(request()->only(['razao_social','cnpj']), ['format'=>'csv'])) }}" class="px-4 py-2 rounded-xl border border-slate-200">CSV</a>
            <a href="{{ route('admin.tax-regime.export', array_merge(request()->only(['razao_social','cnpj']), ['format'=>'xlsx'])) }}" class="px-4 py-2 rounded-xl border border-slate-200">XLSX</a>
        </div>
    </form>

    <div class="space-y-4">
        @forelse ($submissions as $s)
            <div class="glass rounded-2xl p-6 border border-white/60 shadow-lg hover:shadow-xl transition">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-start">
                    <!-- Razão Social -->
                    <div class="lg:col-span-2">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Razão Social</p>
                        <p class="text-sm font-semibold text-slate-900">{{ $s->razao_social }}</p>
                    </div>

                    <!-- CNPJ -->
                    <div class="lg:col-span-1">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">CNPJ</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $s->cnpj }}</p>
                    </div>

                    <!-- Regime -->
                    <div class="lg:col-span-1">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Regime</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $s->regime_tributario }}
                        </span>
                    </div>

                    <!-- Verificado -->
                    <div class="lg:col-span-1">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Verificado</p>
                        <form method="POST" action="{{ route('admin.tax-regime.toggle-verified', $s) }}" class="inline">
                            @csrf
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                                <input type="checkbox" name="verified" value="1" {{ $s->verified ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500 cursor-pointer" onchange="this.form.submit()">
                                <span class="text-xs font-semibold">{{ $s->verified ? '✓ Sim' : 'Não' }}</span>
                            </label>
                        </form>
                    </div>

                    <!-- Data -->
                    <div class="lg:col-span-1">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Data</p>
                        <p class="text-sm font-semibold text-slate-600">{{ optional($s->created_at)->format('d/m/Y') }}</p>
                        <p class="text-xs text-slate-500">{{ optional($s->created_at)->format('H:i') }}</p>
                    </div>

                    <!-- Ações -->
                    <div class="lg:col-span-1 flex flex-wrap gap-2">
                        <a href="{{ route('admin.tax-regime.show', $s) }}" class="inline-flex items-center px-2 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 text-xs font-medium">Ver</a>
                        <form method="POST" action="{{ route('admin.tax-regime.destroy', $s) }}" class="inline" onsubmit="return confirm('Remover essa submissão?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-2 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-100 hover:bg-red-200 text-xs font-medium">Excluir</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-xl bg-slate-50 border border-slate-200">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m0 6a9 9 0 100-18 9 9 0 000 18z"/></svg>
                    <span class="text-slate-600 font-medium">Nenhuma submissão encontrada.</span>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection