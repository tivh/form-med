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

    <div class="glass rounded-2xl overflow-hidden shadow-xl border border-white/60">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50/80">
                <tr>
                    <th class="px-4 py-3 text-left">Razão Social</th>
                    <th class="px-4 py-3 text-left">CNPJ</th>
                    <th class="px-4 py-3 text-left">Regime</th>
                    <th class="px-4 py-3 text-left">LC 214/2025</th>
                    <th class="px-4 py-3 text-left">Data</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white/90">
                @forelse ($submissions as $s)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $s->razao_social }}</td>
                        <td class="px-4 py-3">{{ $s->cnpj }}</td>
                        <td class="px-4 py-3">{{ $s->regime_tributario }}</td>
                        <td class="px-4 py-3">{{ $s->lc_214_2025_compliant ? 'Sim' : 'Não' }}</td>
                        <td class="px-4 py-3">{{ optional($s->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.tax-regime.show', $s) }}" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700">Ver</a>
                            <form method="POST" action="{{ route('admin.tax-regime.destroy', $s) }}" class="inline" onsubmit="return confirm('Excluir?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">Nenhuma resposta ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $submissions->withQueryString()->links() }}</div>
</div>
@endsection