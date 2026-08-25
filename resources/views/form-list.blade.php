@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 pt-10 pb-12">
    {{-- Hero Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-red-600 via-red-500 to-rose-600 text-white shadow-xl mb-10">
        <div class="absolute right-0 top-0 w-80 h-80 bg-white/10 rounded-full -translate-y-1/3 translate-x-1/3 blur-2xl pointer-events-none"></div>
        <div class="absolute left-1/3 bottom-0 w-60 h-60 bg-rose-400/20 rounded-full translate-y-1/2 blur-2xl pointer-events-none"></div>
        
        <div class="relative p-8 md:p-12 space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-xs font-semibold uppercase tracking-widest text-white/90">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Vitória Hospitalar · Portal Oficial
            </div>
            <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight leading-tight">
                Formulários & Cadastros
            </h1>
            <p class="text-base md:text-lg text-white/90 max-w-2xl font-normal leading-relaxed">
                Selecione o formulário correspondente às suas atividades para realizar o envio seguro de dados e documentações.
            </p>
        </div>
    </div>

    {{-- Forms Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($forms as $form)
            <div class="group relative rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900/90 shadow-sm hover:shadow-xl hover:border-red-500/40 dark:hover:border-red-500/40 transition-all duration-300 p-6 flex flex-col justify-between overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-red-500/10 dark:from-red-500/5 to-transparent rounded-bl-full pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Cadastro Oficial
                        </span>
                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Online
                        </span>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                            {{ $form['title'] }}
                        </h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-2 leading-relaxed">
                            {{ $form['description'] }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">Preenchimento seguro</span>
                    <a href="{{ $form['route'] }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-500 text-white text-sm font-semibold shadow-sm hover:shadow group-hover:translate-x-0.5 transition-all">
                        <span>Iniciar Preenchimento</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
