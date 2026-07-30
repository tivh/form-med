{{-- resources/views/admin/tax-regime/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 pt-12 pb-10 space-y-6">
    <div class="glass rounded-2xl p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><p class="text-sm text-slate-500">Razão Social</p><p class="text-lg font-semibold">{{ $submission->razao_social }}</p></div>
        <div><p class="text-sm text-slate-500">CNPJ</p><p class="text-lg font-semibold">{{ $submission->cnpj }}</p></div>
        <div><p class="text-sm text-slate-500">Regime Tributário</p><p class="text-lg font-semibold">{{ $submission->regime_tributario }}</p></div>
        <div><p class="text-sm text-slate-500">LC 214/2025</p><p class="text-lg font-semibold">{{ $submission->lc_214_2025_compliant ? 'Sim' : 'Não' }}</p></div>
        <div><p class="text-sm text-slate-500">Enviado em</p><p class="text-lg font-semibold">{{ optional($submission->created_at)->format('d/m/Y H:i') }}</p></div>
    </div>
    <a href="{{ route('admin.tax-regime.index') }}" class="text-slate-600">← Voltar</a>
</div>
@endsection