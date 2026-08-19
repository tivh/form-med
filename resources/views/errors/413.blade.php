@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 pt-16">
    <div class="glass rounded-3xl p-8 md:p-12">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-12 w-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-2xl font-bold">!</div>
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-rose-600 font-semibold">Arquivo muito grande</p>
                <h1 class="text-3xl font-bold text-slate-900">Não foi possível enviar esta solicitação</h1>
            </div>
        </div>

        <p class="text-lg text-slate-700 leading-relaxed">
            O arquivo ou os dados enviados excedem o limite permitido pelo servidor.
        </p>

        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            <strong>O que fazer agora:</strong>
            <ul class="list-disc list-inside mt-2 space-y-1">
                <li>reduza o tamanho do arquivo;</li>
                <li>comprima a imagem ou PDF;</li>
                <li>tente novamente com um arquivo menor.</li>
            </ul>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('forms.list') }}" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white hover:bg-red-700 transition">
                Voltar para os formulários
            </a>
            <button onclick="window.history.back()" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                Tentar novamente
            </button>
        </div>
    </div>
</div>
@endsection
