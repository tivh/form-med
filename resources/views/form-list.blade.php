@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-12 pb-10">
    <div class="rounded-3xl bg-gradient-to-r from-red-600 via-red-500 to-rose-500 text-white shadow-xl overflow-hidden mb-8">
        <div class="p-8 md:p-10 space-y-4">
            <p class="text-xs uppercase tracking-[0.25em] text-white/70">Vitória Hospitalar</p>
            <h1 class="text-3xl md:text-4xl font-black">Formulários disponíveis</h1>
            <p class="text-white/80">Escolha um formulário corporativo para continuar.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @foreach ($forms as $form)
            <a href="{{ $form['route'] }}" class="block rounded-2xl border border-slate-200 bg-white/90 shadow hover:shadow-lg hover:-translate-y-0.5 transition p-6">
                <div class="flex items-center justify-between gap-4">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-red-700">Formulário</p>
                        <h2 class="text-xl font-bold text-slate-900">{{ $form['title'] }}</h2>
                        <p class="text-slate-600">{{ $form['description'] }}</p>
                    </div>
                    <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-red-50 text-red-700 font-semibold border border-red-100">Acessar</div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
